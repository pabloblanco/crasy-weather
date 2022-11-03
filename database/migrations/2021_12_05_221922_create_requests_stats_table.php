<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('success')->nullable();
            $table->string('city', 50)->nullable();  
            $table->json('response')->nullable(); 
            $table->string('ip')->nullable();  
            $table->string('temperature', 20)->nullable(); 
            $table->json('playlist')->nullable();
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
        Schema::dropIfExists('requests_stats');
    }
}