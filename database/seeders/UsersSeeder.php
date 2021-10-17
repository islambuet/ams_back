<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'username' => '0500454',
                'password' => Hash::make('12345678'),
                'user_group_id' => 1,
                'user_type_id' => 1,
                'name' => json_encode(['en'=>'Shaiful Islam','bn'=>'সাইফুল ইসলাম']),                
                'email' => 'shaiful@shaiful.me',
                'mobile_no' => '01912097849',                
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'username' => '0500455',
                'password' => Hash::make('12345678'),
                'user_group_id' => 2,
                'user_type_id' => 2,
                'name' => json_encode(['en'=>'Shaiful Islam']),                
                'email' => 'sunan@shaiful.me',
                'mobile_no' => '0175000000',                
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'username' => '0500456',
                'password' => Hash::make('12345678'),
                'user_group_id' => 2,
                'user_type_id' => 2,
                'name' => json_encode(['en'=>'Shaiful Islam']), 
                'email' => 'info@shaiful.me',
                'mobile_no' => '0175000002',                
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
        ]);
    }
}
