<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('system_tasks')->insert([
            [
                'name' => json_encode(['en'=>'System Settings','bn'=>'সিস্টেম সেটিংস্‌']), 
                'type' => 'MODULE',
                'parent' => 0,
                'url' => '',
                'controller' => '',
                'ordering' => 1,
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'name' => json_encode(['en'=>'Modules & Tasks','bn'=>'মডিউল এবং টাস্ক‌']), 
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'modules-tasks',
                'controller' => 'ModulesTasksController',
                'ordering' => 1,
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'name' => json_encode(['en'=>'System Configuration','bn'=>'সিস্টেম কনফিগারেশন']), 
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'system-configurations',
                'controller' => 'SystemConfigurationsController',
                'ordering' => 2,
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'name' => json_encode(['en'=>'Users Groups','bn'=>'ইউজার গোষ্ঠী']), 
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'users-groups',
                'controller' => 'UsersGroupsController',
                'ordering' => 3,
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'name' => json_encode(['en'=>'Users Types','bn'=>'ব্যবহারকারীর ধরণ']), 
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'users-types',
                'controller' => 'UsersTypesController',
                'ordering' => 4,
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'name' => json_encode(['en'=>'Users','bn'=>'ব্যবহারকারী']), 
                'type' => 'TASK',
                'parent' => 0,
                'url' => 'users',
                'controller' => 'UsersController',
                'ordering' => 2,
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],                        
        ]);
    }
}
