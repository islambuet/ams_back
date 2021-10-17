<?php
    namespace App\Helpers;
    
    
    use Illuminate\Support\Facades\DB;
    class ConfigurationHelper
    {
        public static $config = array();
        public static function load_config()
        {
            
            $results = DB::table(TABLE_SYSTEM_CONFIGURATIONS)->where('status', SYSTEM_STATUS_ACTIVE)->get();
            // // $results = SystemConfiguration::where('status', 'Active')
            // //                                 ->get();
            // print_r($results);
            foreach($results as $result){
                self::$config[$result->purpose]=$result->config_value;
            }
        }
        public static function isApiOffline()
        {
            return isset(self::$config['SITE_OFF_LINE'])&&(self::$config['SITE_OFF_LINE']==1)?true:false;
        }
    }
    