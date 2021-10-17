<?php
namespace App\Http\Controllers\users;

use App\Http\Controllers\RootController;

use App\Helpers\TaskHelper;
use App\Helpers\TokenHelper;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

class UsersController extends RootController
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
            $response['itemDefault']=TaskHelper::getTableDefaultItem(TABLE_USERS);
            $response['hidden_columns'] =TaskHelper::getHiddenColumns((new \ReflectionClass(__CLASS__))->getShortName(),$this->user['id']);
            if($this->user['user_group_id']==ID_USERGROUP_SUPERADMIN)
            {
                $response['users_groups']= DB::table(TABLE_SYSTEM_USERS_GROUPS)->select('id','name')->orderBy('id', 'ASC')->get()->toArray();
            }
            else{
                $response['users_groups']= DB::table(TABLE_SYSTEM_USERS_GROUPS)->select('id','name')->where('id','!=',ID_USERGROUP_SUPERADMIN)->orderBy('id', 'ASC')->get()->toArray();
            } 
            
            $response['users_types']= DB::table(TABLE_USERS_TYPES)->select('id','name','prefix')->orderBy('id', 'ASC')->get()->toArray();
            $response['itemDefault']['username']=$response['users_types'][0]->prefix;
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
                $perPage=$request->perPage?$request->perPage:2;
            //$page=$request->page?$request->page:2;

            $query=DB::table(TABLE_USERS.' as users');
            $query->select('users.id','users.username','users.user_group_id','users.name_en','users.name_bn','users.email','users.mobile_no','users.ordering','users.status','users.created_at');
            $query->join(TABLE_SYSTEM_USERS_GROUPS.' as users_groups', 'users_groups.id', '=', 'users.user_group_id');
            $query->addSelect('users_groups.name as user_group_name');
            $query->join(TABLE_USERS_TYPES.' as users_types', 'users_types.id', '=', 'users.user_type_id');
            $query->addSelect('users_types.name as user_type_name');
            $query->orderBy('users.ordering', 'ASC');
            $query->orderBy('users.id', 'DESC');
            $query->where('users.status','!=',SYSTEM_STATUS_DELETE);

            //$results=$query->paginate($perPage, ['*'], 'page',$page)->toArray();
            $results=$query->paginate($perPage)->toArray();
            $response['items'] = $results;
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
            $query=DB::table(TABLE_USERS.' as users');
            $query->select('users.id','users.username','users.user_group_id','users.name_en','users.name_bn','users.email','users.mobile_no','users.ordering','users.status','users.created_at');
            $query->join(TABLE_SYSTEM_USERS_GROUPS.' as users_groups', 'users_groups.id', '=', 'users.user_group_id');
            $query->addSelect('users_groups.name as user_group_name');
            $query->join(TABLE_USERS_TYPES.' as users_types', 'users_types.id', '=', 'users.user_type_id');
            $query->addSelect('users_types.name as user_type_name');            
            $query->where('users.id','=',$itemId);
            $result = $query->first();
            if(!$result){
                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.INVALID_ITEM')], 416);
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
        $itemId=$request->id?$request->id:0;
        if($itemId>0) {            
            return $this->saveItemEdit($request);
        } 
        else{
            return $this->saveItemNew($request);           
        }
        
    }
    public function saveItemNew(Request $request){
        $itemOld=array();
        $selctColumns=['name_en','name_bn','username','password','user_group_id','email','mobile_no','status','ordering'];
        if($this->permissions['action_1']!=1) {
            return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED_ADD')], 401);
        }
        $save_token=TokenHelper::getSaveToken($request->save_token,$this->user['id']);

        $itemNew=$request->item;
        unset($itemNew['id'],$itemNew['updated_by'],$itemNew['created_by'],$itemNew['created_at'],$itemNew['updated_at'],$itemNew['user_type_id']);

        if(!$itemNew){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.NO_INPUTS')], 416);
        }

        $validation_rule=array();    
        $validation_rule['name_en']=['required','string','min:3','max:255'];
        $language=App::currentLocale();        
        if($language!='en'){
            $validation_rule['name_'.$language]=['required', 'string','min:3','max:255'];    
        }

        $validation_rule['username']=['required', 'string','min:5','max:255','alpha_dash'];
        $validation_rule['password']=['required','min:3','max:255','alpha_dash'];
        $validation_rule['user_group_id']=['required','numeric'];
        $validation_rule['email']=['nullable','email'];
        $validation_rule['status']=['required',Rule::in([SYSTEM_STATUS_ACTIVE, SYSTEM_STATUS_INACTIVE]),]; 

        $validator = Validator::make($itemNew, $validation_rule);
        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED','errorMessage' => $validator->errors()], 416);
        }
        //checking unwanted input
        foreach($itemNew as $key=>$value){            
            if(!in_array ($key,$selctColumns)){                            
                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.invalid_input',['attribute'=>$key])], 416);
            }
        }

        $result = DB::table(TABLE_USERS)->where('username', $itemNew['username'])->first();
        if($result){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.already_exists',['attribute'=>'username'])], 416);
        }
        if($this->user['user_group_id']==ID_USERGROUP_SUPERADMIN)
        {
            $result = DB::table(TABLE_SYSTEM_USERS_GROUPS)->select('id','name')->where('id', $itemNew['user_group_id'])->first();
        }
        else{
            $result= DB::table(TABLE_SYSTEM_USERS_GROUPS)->select('id','name')->where('id','!=',ID_USERGROUP_SUPERADMIN)->where('id', $itemNew['user_group_id'])->first();
        }
        if(!$result){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.not_exists',['attribute'=>'user_group_id'])], 416);
        }

        DB::beginTransaction();
        try{

            $dataHistory=array();
            $dataHistory['table_name']=TABLE_USERS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;


            $itemNew['user_type_id']=1;
            $itemNew['password']=Hash::make($itemNew['password']);
        
            $itemNew['created_by']=$this->user['id'];            
            $itemNew['created_at']=Carbon::now();
            $id = DB::table(TABLE_USERS)->insertGetId($itemNew);

            $itemNew['id']=$id;
            unset($itemNew['created_by']);

            $dataHistory['table_id']=$id;
            $dataHistory['action']=DB_ACTION_ADD;
            
            $dataHistory['data_old']=json_encode($itemOld);
            $dataHistory['data_new']=json_encode($itemNew);
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user['id'];


            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);

            TokenHelper::updateSaveToken($save_token);
            
            DB::commit();
            unset($itemNew['password']);
            return response()->json(['error' => '','item' =>$itemNew],200);
        } catch (\Exception $ex) {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        }

    }
    public function saveItemEdit(Request $request){
        $itemOld=array();
        $itemId=$request->id?$request->id:0;
        $selctColumns=['name_en','name_bn','username','password','user_group_id','email','mobile_no','status','ordering'];

        if($this->permissions['action_2']!=1) {
            return response()->json(['error'=>'ACCESS_DENIED','errorMessage'=>__('response.ACCESS_DENIED_EDIT')], 401);
        }        
        $result = DB::table(TABLE_USERS)->select($selctColumns)->find($itemId);       
        if(!$result){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.INVALID_ITEM')], 416);
        }
        $itemOld=$result;

        $save_token=TokenHelper::getSaveToken($request->save_token,$this->user['id']);
        
        $itemNew=$request->item;

        unset($itemNew['id'],$itemNew['updated_by'],$itemNew['created_by'],$itemNew['created_at'],$itemNew['updated_at'],$itemNew['user_type_id']);
        $validation_rule=array(); 

        if($itemNew){
            foreach($itemOld as $key=>$oldValue){
                if(array_key_exists($key,$itemNew)){
                    if($key=='password'){
                        if(!$itemNew[$key]){
                            unset($itemNew[$key]);
                            unset($itemOld->$key);
                        }
                        else{
                            $validation_rule['password']=['required','min:3','max:255','alpha_dash'];
                        }
                    }
                    else if($itemOld->$key==$itemNew[$key]){
                        unset($itemNew[$key]);
                        unset($itemOld->$key);
                    } 
                    else{
                        if($key=='name_en'){
                            $validation_rule['name_en']=['min:3','max:255'];
                        }
                        else if($key=='name_bn'){
                            $validation_rule['name_bn']=['min:3','max:255'];
                        }
                        else if($key=='username'){
                            $validation_rule['username']=['min:5','max:255','alpha_dash'];
                            $result = DB::table(TABLE_USERS)->where('username', $itemNew['username'])->first();
                            if($result){
                                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.already_exists',['attribute'=>'username'])], 416);
                            }
                        }
                        else if($key=='user_group_id'){
                            $validation_rule['user_group_id']=['numeric']; 
                            if($this->user['user_group_id']==ID_USERGROUP_SUPERADMIN)
                            {
                                $result = DB::table(TABLE_SYSTEM_USERS_GROUPS)->select('id','name')->where('id', $itemNew['user_group_id'])->first();
                            }
                            else{
                                $result= DB::table(TABLE_SYSTEM_USERS_GROUPS)->select('id','name')->where('id','!=',ID_USERGROUP_SUPERADMIN)->where('id', $itemNew['user_group_id'])->first();
                            }
                            if(!$result){
                                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.not_exists',['attribute'=>'user_group_id'])], 416);
                            }                       
                        }
                        else if($key=='email'){
                            $validation_rule['email']=['nullable','email'];
                            
                        }
                    }
                }
                else{
                    unset($itemOld->$key); //no input
                }

            }
            //checking unwanted input
            foreach($itemNew as $key=>$value){
                if(!in_array($key,$selctColumns)){
                    return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.invalid_input',['attribute'=>$key])], 416);
                }
            }
        }       
        if(!$itemNew){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.NO_INPUTS')], 416);
        }
        if($validation_rule){
            $validator = Validator::make($itemNew, $validation_rule);
            if ($validator->fails()) {
                return response()->json(['error' => 'VALIDATION_FAILED','errorMessage' => $validator->errors()], 416);
            }
        }

        DB::beginTransaction();
        try{

            $dataHistory=array();
            $dataHistory['table_name']=TABLE_USERS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;


            if(isset($itemNew['password'])){
                $itemNew['password']=Hash::make($itemNew['password']);
            }
            $itemNew['updated_by']=$this->user['id'];
            $itemNew['updated_at']=Carbon::now();
            DB::table(TABLE_USERS)->where('id',$itemId)->update($itemNew);

            unset($itemNew['updated_by']);

            $dataHistory['table_id']=$itemId;
            $dataHistory['action']=DB_ACTION_EDIT;

            $dataHistory['data_old']=json_encode($itemOld);
            $dataHistory['data_new']=json_encode($itemNew);
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user['id'];
            
            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);

            TokenHelper::updateSaveToken($save_token);
            
            DB::commit();
            unset($itemNew['password']);
            return response()->json(['error' => '','item' =>$itemNew],200);
        } catch (\Exception $ex) {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        }
        
        
    }
}

