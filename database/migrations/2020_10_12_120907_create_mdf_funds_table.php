<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMdfFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'mdf_funds', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('mdf_id');
            $table->float('amount')->default(0.00);
            $table->unsignedBigInteger('payment_id');
            $table->string('type');
            $table->text('note')->nullable();
            $table->date('date');
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
        Schema::dropIfExists('mdf_funds');
    }
}
