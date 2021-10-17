<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersGroupsTable extends Migration
{
    public $actions = [
        '0' => 'VIEW',
        '1' => 'ADD',
        '2' => 'EDIT',
        '3' => 'DELETE',
        '4' => 'PRINT',
        '5' => 'DOWNLOAD',
        '6' => 'COLUMN_HEADER',
        '7' => 'APPROVE',
        '8' => 'FORWARD'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('ordering')->default(9999);
            $table->string('status', 11)->default('Active')->comment('Active, In-Active, Deleted');
            foreach ($this->actions as $key => $action) {
                $table->string('action_' . $key)->default(',')->comment($action);
            }
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_groups');
    }
}
