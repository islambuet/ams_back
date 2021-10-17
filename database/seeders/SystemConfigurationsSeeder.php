<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('system_configurations')->insert([
            'purpose' => 'SITE_OFF_LINE',
            'description' => 'Making the application go offline.',
            'config_value' => 0,
            'created_by' => 1,
            'created_at'=>Carbon::now()
        ]);        
    }
}
