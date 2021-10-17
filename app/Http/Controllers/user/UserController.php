<?php
namespace App\Http\Controllers\user;

use App\Http\Controllers\RootController;

use App\Helpers\TaskHelper;
use App\Helpers\TokenHelper;
use App\Helpers\UserHelper;
use App\Helpers\UploadHelper;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Carbon\Carbon;

class UserController extends RootController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    
    public function initialize(Request $request)
    {
        $response=array();
        $response['error']='';
        $user=UserHelper::getLoggedUser();
        if($user){
            //$user['tasks']=TaskHelper::getUserTasks($this->userGroupRole,$this->language);
            $user['tasks']=TaskHelper::getUserTasks($this->user['userGroupRole']);//$this->user and logged user same
            $user['infos']=($this->user['infos']?json_decode($this->user['infos'],true):array());
            $user['profile_picture_url']=array_key_exists('profile_picture',$user['infos'])?UploadHelper::getFilePathUrl($user['infos']['profile_picture']):'';            
            $response['user']=$user;
        }
        return response()->json($response, 200);
    }
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $response=array();
        $response['error'] = '';
        $userFound = User::where('username', $username)->first();
        if($userFound)
        {
            if(Hash::check($password, $userFound->password)){
                if($userFound->status == SYSTEM_STATUS_ACTIVE){
                    if(Auth::attempt($request->only(['username', 'password'])))
                    {
                        $user = Auth::user();
                        $user['authToken'] = Auth::user()->createToken('ip:'.$request->server('REMOTE_ADDR').';User agent:'.$request->server('HTTP_USER_AGENT'))->plainTextToken;
                        $user['tasks']=TaskHelper::getUserTasks(UserHelper::getCurrentUser()['userGroupRole']);                       
                        $user['infos']=($user['infos']?json_decode($user['infos'],true):array());
                        $user['profile_picture_url']=array_key_exists('profile_picture',$user['infos'])?UploadHelper::getFilePathUrl($user['infos']['profile_picture']):'';            
                        $response['user']=$user;
                        return response()->json($response, 200);
                    }else
                    {
                        $response['error'] = 'INVALID_CREDENTIALS';
                        $response['errorMessage'] = __('user.INVALID_CREDENTIALS');
                        return response()->json($response, 401);
                    }
                }else{
                    $response['error'] = 'USER_NOT_FOUND';
                    $response['errorMessage'] = __('user.USER_NOT_FOUND');
                    return response()->json($response, 404);
                }
            }else{
                $response['error'] = 'INVALID_CREDENTIALS';
                $response['errorMessage'] = __('user.INVALID_CREDENTIALS');
                return response()->json($response, 404);
            }
        }else{
            $response['error'] = 'USER_NOT_FOUND';
            $response['errorMessage'] = __('user.USER_NOT_FOUND');
            return response()->json($response, 404);
        }
    }
    public function logout(Request $request)
    {
        $user=Auth::guard('sanctum')->user();
        if($user)
        {
            $logout = $user->tokens()->delete();
            if($logout){
                return response()->json(['error' => ''], 200);
            }else{
                return response()->json(['error' => 'USER_LOGOUT_FAILED','errorMessage'=>__('user.USER_LOGOUT_FAILED')], 401);
            }
        }
        else
        {
            return response()->json(['error' => 'USER_NOT_FOUND','errorMessage'=>__('user.USER_NOT_FOUND')], 404);
        }

    }
    public function profilePicture(Request $request)
    {
       
        $save_token=TokenHelper::getSaveToken($request->save_token,$this->user['id']);
        $uploaded_files=UploadHelper::upload('profile-pictures/'.$this->user['id'],['image']);
        if($uploaded_files['status']){
            if(!array_key_exists('profile_picture',$uploaded_files['datas'])){
                return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('validation.data_not_exists',['attribute'=>'profile_picture'])], 416);                
            }
        }
        else{
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>$uploaded_files['errors']], 416);
        }
        $infos=($this->user['infos']?json_decode($this->user['infos'],true):array());
        $itemId=$this->user['id'];
        $itemOld=array();
        $itemOld['infos']['profile_picture']=array_key_exists('profile_picture',$infos)?$infos['profile_picture']:'';
        
        $itemNew=array();
        $itemNew['infos']=$infos;
        $itemNew['infos']['profile_picture']=$uploaded_files['datas']['profile_picture']['path'];
        DB::beginTransaction();
        try{

            $dataHistory=array();
            $dataHistory['table_name']=TABLE_USERS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;
            
            $itemNew['updated_by']=$this->user['id'];
            $itemNew['updated_at']=Carbon::now();
            DB::table(TABLE_USERS)->where('id',$itemId)->update($itemNew);
            $dataHistory['table_id']=$itemId;
            $dataHistory['action']=DB_ACTION_EDIT;
            
            
            unset($itemNew['updated_by'],$itemNew['created_by'],$itemNew['created_at'],$itemNew['updated_at']);

            $dataHistory['data_old']=json_encode($itemOld);
            $dataHistory['data_new']=json_encode(array('infos'=>['profile_picture'=>$itemNew['infos']['profile_picture']]));
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user['id'];

            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);
            TokenHelper::updateSaveToken($save_token);
            DB::commit();
            return response()->json(['error'=>'','profile_picture_url'=>UploadHelper::getFilePathUrl($uploaded_files['datas']['profile_picture']['path'])], 200);
        } catch (\Exception $ex) {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        }
    }
    public function ChangePassword(Request $request)
    {
        $save_token=TokenHelper::getSaveToken($request->save_token,$this->user['id']);
        $itemId=$this->user['id'];
        $itemNew=$request->item;
        $validation_rule=array();            
        $validation_rule['password_old']=['required'];
        $validation_rule['password_new']=['required','min:3','max:255','alpha_dash'];
        $validator = Validator::make($itemNew, $validation_rule);
        if ($validator->fails()) {
            return response()->json(['error' => 'VALIDATION_FAILED','errorMessage' => $validator->errors()], 416);
        }
        $result = DB::table(TABLE_USERS)->select('password')->find($itemId);
        if(!(Hash::check($itemNew['password_old'],$result->password))){
            return response()->json(['error'=>'VALIDATION_FAILED','errorMessage'=>__('Incorrect Old Password')], 416);
        }
        $itemOld=array();
        $itemOld['password']=$result->password;

        $itemNew=array();
        $itemNew['password']=Hash::make($request->item['password_new']);       
        DB::beginTransaction();
        try{

            $dataHistory=array();
            $dataHistory['table_name']=TABLE_USERS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;
            
            $itemNew['updated_by']=$this->user['id'];
            $itemNew['updated_at']=Carbon::now();
            DB::table(TABLE_USERS)->where('id',$itemId)->update($itemNew);
            $dataHistory['table_id']=$itemId;
            $dataHistory['action']=DB_ACTION_EDIT;            
            unset($itemNew['updated_by'],$itemNew['created_by'],$itemNew['created_at'],$itemNew['updated_at']);

            $dataHistory['data_old']=json_encode($itemOld);
            $dataHistory['data_new']=json_encode($itemNew);
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user['id'];

            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);
            TokenHelper::updateSaveToken($save_token);
            DB::commit();
            
            return response()->json(['error' => ''],200);
        } catch (\Exception $ex) {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        } 
        
    }
}
