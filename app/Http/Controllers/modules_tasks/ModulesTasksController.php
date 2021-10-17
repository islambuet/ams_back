<?php
namespace App\Http\Controllers\modules_tasks;

use App\Http\Controllers\RootController;

use App\Helpers\TaskHelper;
use App\Helpers\TokenHelper;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

class ModulesTasksController extends RootController
{
    public $permissions;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->permissions=TaskHelper::getPermissions((new \ReflectionClass(__CLASS__))->getShortName(),$this->user['userGroupRole']);        
    }
    public function initialize(Request $request)
    {
        if ($this->permissions['action_0'] == 1){
            $response=array();
            $response['error'] = '';  
                      
            $response['permissions'] = $this->permissions;            
            $response['itemDefault']=TaskHelper::getTableDefaultItem(TABLE_SYSTEM_TASKS);            
            $response['hidden_columns'] =TaskHelper::getHiddenColumns((new \ReflectionClass(__CLASS__))->getShortName(),$this->user['id']);
            $response['types'] = ['MODULE','TASK'];
            return response()->json($response, 200);

        }else{
            return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED')], 401);
        }
    }
    public function getItems(Request $request)
    {
        if ($this->permissions['action_0'] == 1){
            $response=array();
            $response['error'] = '';
            $response['modules_tasks'] = TaskHelper::getModulesTasksTableTree();
            return response()->json($response, 200);

        }else{
            return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED')], 401);
        }        
    }
    public function getItem($itemId,Request $request)
    {
        if ($this->permissions['action_0'] == 1){
            
            $response=array();
            $response['error'] = '';
            $result = DB::table(TABLE_SYSTEM_TASKS)->find($itemId);
            if(!$result){
                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.data_not_exists',['attribute'=>'id: '.$itemId])], 416);                  
            }
            $response['item'] = $result;            
            return response()->json($response, 200);

        }else{
            return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED')], 401);
        }        
    }
    public function saveItem(Request $request)
    {
        $itemOld=array();
        $language_current=App::currentLocale(); 
        $language_available=config('app.language_available');
        $save_token=TokenHelper::getSaveToken($request->save_token,$this->user['id']);
       
        $itemId=$request->id?$request->id:0;

        
        $validation_rule=array();    
        $validation_rule['name']=['nullable'];//change latter      
        $validation_rule['type']=['required',Rule::in(['MODULE', 'TASK']),];
        $validation_rule['parent']=['required','numeric','gte:0'];        
        $validation_rule['url']=['nullable'];
        $validation_rule['controller']=['nullable'];
        $validation_rule['status']=['required',Rule::in([SYSTEM_STATUS_ACTIVE, SYSTEM_STATUS_INACTIVE])]; 
        $validation_rule['ordering']=['nullable','numeric']; 
       
        $itemNew=$request->item;
        //validation start
        //checking if any input there
        if(!is_array($itemNew)){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.input_not_found')], 416);
        }
        //checking if any invalid input
        foreach($itemNew as $key=>$value){            
            if( !$key || (!in_array ($key,array_keys($validation_rule)))){                        
                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.input_not_valid',['attribute'=>$key])], 416);
            }
        }

        if($itemId>0) {

            if($this->permissions['action_2']!=1) {
                return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED_EDIT')], 401);
            }        
            $result = DB::table(TABLE_SYSTEM_TASKS)->select(array_keys($validation_rule))->find($itemId);       
            if(!$result){
                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.data_not_exists',['attribute'=>'id: '.$itemId])], 416);
            }
            $itemOld=$result;
            foreach($itemOld as $key=>$oldValue){
                if(array_key_exists($key,$itemNew)){
                    if($key=='name'){
                        if(!is_array($itemNew[$key])){                            
                            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.input_not_valid',['attribute'=>$key])], 416);
                        }
                        if($itemOld->$key==json_encode($itemNew[$key])){
                            unset($itemNew[$key]);
                            unset($itemOld->$key);
                        }
                        else{
                            foreach($itemNew[$key] as $name_lang=>$name_value){
                                $validation_rule['name.'.$name_lang]=['nullable','min:3','max:255'];
                            }
                            $validation_rule['name.'.$language_current]=['required','min:3','max:255'];
                        }
                                                
                    }                    
                    else if($itemOld->$key==$itemNew[$key]){
                        unset($itemNew[$key]);
                        unset($itemOld->$key);
                        unset($validation_rule[$key]);
                    } 

                }
                else{
                    unset($validation_rule[$key]);
                    unset($itemOld->$key); //no change
                }
            }
            
        } 
        else{
            if($this->permissions['action_1']!=1) {
                return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED_ADD')], 401);
            }  
            //name field validation          
            foreach($language_available as $lang){
                $validation_rule['name.'.$lang]=['nullable','min:3','max:255'];
            } 
            $validation_rule['name.'.$language_current]=['required','min:3','max:255'];

        }
        if(!$itemNew){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.input_not_changed')], 416);
        }

        unset($validation_rule['name']);
        $validator = Validator::make($itemNew, $validation_rule);
        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED','errorMessage' => $validator->errors()], 416);
        }
        //validation end

        if(array_key_exists('name',$itemNew)){
            $itemNew['name']=json_encode($itemNew['name']);
        }        
        DB::beginTransaction();
        try{

            $dataHistory=array();
            $dataHistory['table_name']=TABLE_SYSTEM_TASKS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;
            if($itemId>0){
                $itemNew['updated_by']=$this->user['id'];
                $itemNew['updated_at']=Carbon::now();
                DB::table(TABLE_SYSTEM_TASKS)->where('id',$itemId)->update($itemNew);
                $dataHistory['table_id']=$itemId;
                $dataHistory['action']=DB_ACTION_EDIT;
            } else {
                $itemNew['created_by']=$this->user['id'];
                $itemNew['created_at']=Carbon::now();
                $id = DB::table(TABLE_SYSTEM_TASKS)->insertGetId($itemNew);
                $itemNew['id']=$id;
                $dataHistory['table_id']=$id;
                $dataHistory['action']=DB_ACTION_ADD;
            }
            $returnItem=$itemNew;            
            unset($returnItem['password']);
            unset($itemNew['updated_by'],$itemNew['created_by'],$itemNew['created_at'],$itemNew['updated_at']);

            $dataHistory['data_old']=json_encode($itemOld);
            $dataHistory['data_new']=json_encode($itemNew);
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user['id'];

            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);
            TokenHelper::updateSaveToken($save_token);
            DB::commit();
            
            return response()->json(['error' => '','item' =>$returnItem],200);
        } catch (\Exception $ex) {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        }        
    }    
}

