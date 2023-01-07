<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMdfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'mdfs', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('mdf_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status');
            $table->unsignedBigInteger('type');
            $table->unsignedBigInteger('sub_type');
            $table->date('date');
            $table->float('amount', 25, 2)->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('is_complete')->default(0);
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
        Schema::dropIfExists('mdfs');
    }
}
