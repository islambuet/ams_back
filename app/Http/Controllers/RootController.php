<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
/**
 * Loading Constants for Database Tables and Varaibles
 */

require_once base_path().'/config/variables/tables/system.php';
require_once base_path().'/config/variables/tables/main.php';
require_once base_path().'/config/variables/system_status.php';
require_once base_path().'/config/variables/db_actions.php';
require_once base_path().'/config/variables/ids.php';


use Illuminate\Http\Request;
use App\Helpers\ConfigurationHelper;
use App\Helpers\UserHelper;

abstract class RootController extends Controller
{
    public $user;
    public function __construct(Request $request)
    {
        ConfigurationHelper::load_config();
        $this->checkApioffline($request);
        $this->user=UserHelper::getCurrentUser();
    }
    private function checkApioffline(Request $request)
    {
        if(ConfigurationHelper::isApiOffline())
        {
            $path=$request->path();
            if(!(
                str_starts_with($path, 'api/user/')||
                str_starts_with($path, 'api/system-configurations/')

            ))
            {
                response()->json([
                    'error'=>'API_OFFLINE',
                    'errorMessage' => __('response.API_OFFLINE')],503)->send();
                    exit;
            }
        }
    }
    /*
	**$data['table_name']	:Save table name
	**$data['table_id']	 	:Action id
	**$data['controller'] 	:Controller Name of the Route
	**$data['method']: 		:Funciton Name of the Controller
	**$data['data_old']		:Previous data
	**$data['data_new']		:New Data
	**$data['action']		:Add/Edit/Delete
	**$data['created_at']	:Creating time
    **$data['created_by']	:Action User

	**$tableHistory			:Name of the history table:='ams_back.system_histories'

	*/
    public function dBSaveHistory($data,$tableHistory){
        //$data['created_at']==xx
        DB::table($tableHistory)->insertGetId($data);
    }
}
