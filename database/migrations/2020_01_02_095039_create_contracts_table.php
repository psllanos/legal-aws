<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'contracts', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('client_name');
            $table->string('subject')->nullable();
            $table->string('value')->nullable();
            $table->integer('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('notes')->nullable();
            $table->longText('client_signature')->nullable();
            $table->longText('owner_signature')->nullable();
            $table->string('status')->default('pending');
            $table->integer('created_by');
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
