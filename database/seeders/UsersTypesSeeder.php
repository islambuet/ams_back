<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users_types')->insert([
            [
                'name' => 'Users',
                'prefix' => '050',
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'name' => 'Employee',
                'prefix' => '001',
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ]
        ]);
    }
}
