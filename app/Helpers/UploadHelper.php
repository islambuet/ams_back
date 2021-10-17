<?php
    namespace App\Helpers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\Storage;

    class UploadHelper
    {
        //request() is laravel request helper
        //$_FILES
        public static $DISK = 'public';
        public static $DISK_LINK = 'files';
        public static $UPLOAD_REMOTE=
            [
                'status'=>false,
                'api_url'=>'http://localhost/upload_api/public/api/upload',
                'site_root_folder'=>'base',
                'site_base_link'=>'http://localhost/upload_api/public'
            ];

        public static function upload($upload_dir='',$fileRule=['image'],$max_size=1024*10)
        {
            if(UploadHelper::$UPLOAD_REMOTE['status']){
                $uploaded_files=UploadHelper::uploadRemote($upload_dir,$fileRule,$max_size);
            }
            else{
                $uploaded_files=UploadHelper::uploadLocal($upload_dir,$fileRule,$max_size);
            }
            return $uploaded_files;                                  
        }
        public static function getUploadValidation($upload_dir='',$fileRule=['image'],$max_size=1024)
        {
            $fileRule[]='max:'.$max_size;        
            $uploaded_files=['status'=>true,'errors'=>[],'datas'=>[]];
            if(sizeof($_FILES)>0)
            {
                foreach ($_FILES as $key=>$file){  
                    //single file attached
                    if(is_string($file['name']) && (strlen($file['name'])>0)){
                        $validation_rule=array();                        
                        $validation_rule[$key]=$fileRule;
                        $validator = Validator::make(request()->all(),$validation_rule);
                        if ($validator->fails()) {
                            $uploaded_files['status']=false;
                            $uploaded_files['errors'][$key]=$validator->errors()->toArray()[$key];
                        }        
                    }                
                    //else if(is_array($file['name']) //skip multiple upload
                    //else //skip file not attached                
                }          
            }  
            return $uploaded_files;          
        }
        public static function getUploadFileName($dir,$file){            
            $pathinfo=pathinfo($file);
            $ext=isset($pathinfo['extension'])?$pathinfo['extension']:'';
            $filename=$pathinfo['filename']?$pathinfo['filename']:'';
            $filename_new=$filename.($ext?'.'.$ext:'');
            $index=1;
            while(Storage::disk(UploadHelper::$DISK)->exists($dir.'/'.$filename_new)) {                
                $filename_new=$filename.($index++).($ext?'.'.$ext:'');
            }
            return $filename_new;
        }
        public static function uploadLocal($upload_dir='',$fileRule=['image'],$max_size=1024){                        
            $uploaded_files=UploadHelper::getUploadValidation($upload_dir,$fileRule,$max_size);
            if($uploaded_files['status'])
            {
                foreach ($_FILES as $key=>$file){  
                    //single file attached
                    if(is_string($file['name']) && (strlen($file['name'])>0)){
                        try 
                        {
                            unset($file['tmp_name']);                           
                            $file['name_uploaded']=UploadHelper::getUploadFileName($upload_dir,request()->file($key)->getClientOriginalName());
                            $file['path'] = UploadHelper::$DISK_LINK.'/'.request()->file($key)->storeAs($upload_dir,$file['name_uploaded'],UploadHelper::$DISK);
                            $uploaded_files['datas'][$key]=$file;
                        }
                        catch (\Exception $ex)
                        {    
                            $uploaded_files['status']=false;
                            $uploaded_files['errors'][$key]=$ex->getMessage();
                        }
                    
                    }              
                }
            }
            return $uploaded_files;
        }
        public static function uploadRemote($upload_dir='',$fileRule=['image'],$max_size=1024){

            $uploaded_files=UploadHelper::getUploadValidation($upload_dir,$fileRule,$max_size);
            if($uploaded_files['status'])
            {
                // create curl resource
                $ch = curl_init();
                // set url
                curl_setopt($ch, CURLOPT_URL, UploadHelper::$UPLOAD_REMOTE['api_url']);

                //set to post data
                curl_setopt($ch, CURLOPT_POST,TRUE);
                $data = array();
                $data['site_root_folder']=UploadHelper::$UPLOAD_REMOTE['site_root_folder'];                                
                $data['upload_dir']=$upload_dir;
                $data['fileRule']=json_encode($fileRule);                
                $data['max_size']=$max_size;
                foreach ($_FILES as $key=>$file){  
                    //single file attached
                    if(is_string($file['name']) && (strlen($file['name'])>0))
                    {
                        //also check max size here
                        $data[$key] = new \CURLFile($file['tmp_name'],$file['type'], $file['name']);
                    }
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                //return the transfer as a string
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);

                // $output contains the output string
                $response = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($http_status==200)
                {
                    $uploaded_files=json_decode(json_decode($response)->data,true);
                }
                else
                {
                    foreach ($_FILES as $key=>$file){  
                        //single file attached
                        if(is_string($file['name']) && (strlen($file['name'])>0))
                        {
                            $uploaded_files['status']=false;
                            $uploaded_files['errors'][$key]=['Store Server unavailable'];
                        }
                    }
                }
                // close curl resource to free up system resources
                curl_close($ch);
            }
            return $uploaded_files;
        }
        public static function getFilePathUrl($path=''){
            if(!$path){
                $path=UploadHelper::$DISK_LINK.'/no_image.png';
            }
            if(UploadHelper::$UPLOAD_REMOTE['status']){
                return UploadHelper::$UPLOAD_REMOTE['site_base_link'].'/'.$path;
            }
            else{
                return env('APP_URL').'/'.$path;
            }
            
        }
    }
