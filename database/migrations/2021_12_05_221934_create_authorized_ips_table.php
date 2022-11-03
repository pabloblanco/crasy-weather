<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizedIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorized_ips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token')->nullable();
            $table->string('ip')->nullable();            
            $table->enum('status', ['enabled', 'disabled', 'trash'])->default('enabled');
            $table->string('propietario', 50)->nullable();   
            $table->enum('api', ['CrazyWeather'])->default('CrazyWeather');
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
        Schema::dropIfExists('authorized_ips');
    }
}