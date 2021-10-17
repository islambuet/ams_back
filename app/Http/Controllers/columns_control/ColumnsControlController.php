<?php
namespace App\Http\Controllers\columns_control;

use App\Http\Controllers\RootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ColumnsControlController extends RootController
{
    public $permissions;
    public function __construct(Request $request)
    {
        parent::__construct($request);        
    }    
    public function saveItem(Request $request)
    {
        $itemOld=array();
        $itemNew=array();
        $itemNew['user_id']=$this->user['id'];
        $itemNew['url']=$request->url;
        $itemNew['method']=$request->method;
        $itemNew['hidden_columns']=json_encode($request->hidden_columns?$request->hidden_columns:[]);
        $result = DB::table(TABLE_SYSTEM_USER_HIDDEN_COLUMNS)->select('id','url','method','hidden_columns')
        ->where('url',$itemNew['url'])
        ->where('method',$itemNew['method'])
        ->first();
        if($result){
            $itemOld=$result;
        }              
        DB::beginTransaction();
        try
        {

            $dataHistory=array();
            $dataHistory['table_name']=TABLE_SYSTEM_USER_HIDDEN_COLUMNS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;
            if($itemOld)
            {
                $itemNew['updated_by']=$this->user['id'];
                $itemNew['updated_at']=Carbon::now();
                DB::table(TABLE_SYSTEM_USER_HIDDEN_COLUMNS)->where('id',$itemOld->id)->update($itemNew);

                unset($itemNew['updated_by']);

                $dataHistory['table_id']=$itemOld->id;
                $dataHistory['action']=DB_ACTION_EDIT;
            } 
            else 
            {
                $itemNew['created_by']=$this->user['id'];
                $itemNew['created_at']=Carbon::now();              
                $id = DB::table(TABLE_SYSTEM_USER_HIDDEN_COLUMNS)->insertGetId($itemNew);
                $itemNew['id']=$id;                
                unset($itemNew['created_by']);

                $dataHistory['table_id']=$id;
                $dataHistory['action']=DB_ACTION_ADD;
            }
            $dataHistory['data_old']=json_encode($itemOld);
            $dataHistory['data_new']=json_encode($itemNew);
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user['id'];


            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);
            DB::commit();
            return response()->json(['error' => '','item' =>$itemNew],200);
        } 
        catch (\Exception $ex) 
        {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        }
    }
    
}

