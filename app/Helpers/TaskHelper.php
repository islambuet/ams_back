<?php
    namespace App\Helpers;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    class TaskHelper
    {
        public static $MAX_MODULE_ACTIONS=9;
        public static function getUserGroupRole($user_group_id)
        {
            $role=array();
            $query=DB::table(TABLE_SYSTEM_USERS_GROUPS);
            $query->where('id', $user_group_id);
            for($i=0;$i<self::$MAX_MODULE_ACTIONS;$i++)
            {
                $query->addselect('action_'.$i);
                $role['action_'.$i]=',';
            }
            $userGroup=$query->first();
            if($userGroup) {
                for($i=0;$i<self::$MAX_MODULE_ACTIONS;$i++) {
                    $role['action_'.$i]=$userGroup->{'action_'.$i};
                }
            }
            return $role;
        }
        public static function getPermissions($controllerName,$userGroupRole)//forApi
        {
            $permissions = array();
            $task=DB::table(TABLE_SYSTEM_TASKS)->where('controller', $controllerName)->select('id')->first();
            $taskId=$task?$task->id:0;
            for($i=0; $i<self::$MAX_MODULE_ACTIONS; $i++)
            {
                if(strpos($userGroupRole['action_'.$i], ','.$taskId.',')!==false){
                    $permissions['action_'.$i] = 1;
                }
                else
                {
                    $permissions['action_'.$i]=0;
                }
            }
            return $permissions;
        }

        public static function getModulesTasksTableTree()
        {
            $results=DB::table(TABLE_SYSTEM_TASKS)                
                ->orderBy('ordering', 'ASC')
                ->get()->toArray();

            $children=array();
            foreach($results as $result)
            {
                //$children[$result->parent]['ids'][$result->id]=$result->id;
                //$children[$result->parent]['modules'][$result->id]=$result;

                $result=(array)$result;
                $children[$result['parent']]['ids'][$result['id']]=$result['id'];
                $children[$result['parent']]['modules'][$result['id']]=$result;
            }
            $level0=$children[0]['modules'];
            $tree=array();
            $max_level=1;
            foreach ($level0 as $module)
            {
                self::getSubModulesTasksTree($module,'','',1,$max_level,$tree,$children);
            }
            return array('max_level'=>$max_level,'tree'=>$tree);
        }
        public static function getSubModulesTasksTree($module,$parent_class,$prefix,$level,&$max_level,&$tree,$children)
        {
            if($level>$max_level)
            {
                $max_level=$level;
            }
            $tree[]=array('parent_class'=>$parent_class,'prefix'=>$prefix,'level'=>$level,'module_task'=>$module);
            $subs=array();
            if(isset($children[$module['id']]))
            {
                $subs=$children[$module['id']]['modules'];
            }
            if(sizeof($subs)>0)
            {
                foreach($subs as $sub)
                {
                    self::getSubModulesTasksTree($sub,$parent_class.' parent_'.$module['id'],$prefix.'- ',$level+1,$max_level,$tree,$children);
                }
            }
        }
        public static function getUserTasks($userGroupRole)
        {
            $role=array();
            if(strlen($userGroupRole['action_0'])>1)
            {
                $role=explode(',',trim($userGroupRole['action_0'],','));
            }

            $tasks=DB::table(TABLE_SYSTEM_TASKS)                
                ->orderBy('ordering', 'ASC')
                ->where('status',SYSTEM_STATUS_ACTIVE)
                ->get()->toArray();
            $children=array();
            foreach($tasks as $task)
            {
                $task=(array)$task;
                if($task['type']=='TASK')
                {
                    if(in_array($task['id'],$role))
                    {
                        $children[$task['parent']][$task['id']]=$task;
                    }
                }
                else
                {
                    $children[$task['parent']][$task['id']]=$task;
                }
            }
            $tree=array();
            if(isset($children[0]))
            {
                $tree = self::getUserSubTasks($children, $children[0]);
            }
            return $tree;
        }
        public static function getUserSubTasks(&$list, $parent)
        {
            $tree = array();
            foreach ($parent as $key=>$element)
            {
                //$tree[] = $element;
                if(isset($list[$element['id']]))
                {
                    $children=self::getUserSubTasks($list, $list[$element['id']]);
                    if($children)
                    {
                        $element['children'] = $children;
                        $tree[] = $element;
                    }
                }
                else
                {
                    if($element['type']=='TASK')
                    {
                        $tree[] = $element;
                    }
                }
            }
            return $tree;

        }
        public static function getHiddenColumns($controllerName,$user_id,$method='list')//forApi
        {   
            $hidden_columns =array();
            
            $result = DB::table(TABLE_SYSTEM_USER_HIDDEN_COLUMNS.' as hc')
                ->join(TABLE_SYSTEM_TASKS.' as task', 'task.url', '=', 'hc.url')
                ->select('hc.hidden_columns')
                ->where('task.controller',$controllerName)
                ->where('hc.method',$method)
                ->where('hc.user_id',$user_id)
                ->first();           
            if($result)
            {
                $hidden_columns  =json_decode($result->hidden_columns);
            }
            return $hidden_columns;
        }
        public static function getTableDefaultItem($tableName)//forApi
        {   
            $result=explode('.',$tableName);
            $TABLE_SCHEMA='';
            $TABLE_NAME='';
            if(sizeof($result)>1){
                $TABLE_SCHEMA=$result[0];
                $TABLE_NAME=$result[1];
            }
            else{
                $TABLE_SCHEMA=env('DB_DATABASE','basic');
                $TABLE_NAME=$result[0];
            }                        
            $results=DB::table('INFORMATION_SCHEMA.COLUMNS')
                ->select('COLUMN_NAME','COLUMN_DEFAULT')
                ->where('TABLE_SCHEMA','=',$TABLE_SCHEMA)
                ->where('TABLE_NAME','=',$TABLE_NAME)
                ->get()->toArray();                
            $item=array();
            foreach($results as $result){
                if($result->COLUMN_NAME=='id'){
                    $item[$result->COLUMN_NAME]=0;
                }
                else{
                    //$value=$result->COLUMN_DEFAULT;
                    $value='';
                    if($result->COLUMN_DEFAULT){
                        if($result->COLUMN_DEFAULT!="NULL"){
                            if((str_starts_with($result->COLUMN_DEFAULT,"'"))&&(str_ends_with($result->COLUMN_DEFAULT,"'"))){
                                $value=substr($result->COLUMN_DEFAULT,1,strlen($result->COLUMN_DEFAULT)-2);
                            }
                            else{
                                $value=$result->COLUMN_DEFAULT;
                            }                            
                            
                        }
                    }
                    $item[$result->COLUMN_NAME]=$value;
                }
            }
           return $item;
        }

    }
