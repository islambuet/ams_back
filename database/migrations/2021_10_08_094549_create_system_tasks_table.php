<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumText('name');
            $table->string('type')->default('TASK')->comment('MODULE, TASK');
            $table->integer('parent')->default(0);
            $table->string('url')->nullable();
            $table->string('controller')->nullable();
            $table->string('icon')->nullable();
            $table->integer('ordering')->default(9999);
            $table->string('status', 11)->default('Active')->comment('Active, In-Active, Deleted');
           
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
        Schema::dropIfExists('system_tasks');
    }
}
