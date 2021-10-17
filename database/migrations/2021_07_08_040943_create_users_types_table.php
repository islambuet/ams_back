<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('prefix', 20);
            $table->integer('ordering')->default(9999);
            $table->string('status', 20)->default('Active')->comment('Active, In-Active');
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
        Schema::dropIfExists('users_types');
    }
}
