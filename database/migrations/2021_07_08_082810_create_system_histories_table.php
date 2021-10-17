<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_histories', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 150);
            $table->integer('table_id')->comment('Primary key ID of regarding task table');
            $table->string('controller', 150);
            $table->string('method', 150);
            $table->text('data_old')->nullable();
            $table->text('data_new')->nullable();
            $table->string('action', 20)->comment('ADD, UPDATE, DELETE');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_histories');
    }
}
