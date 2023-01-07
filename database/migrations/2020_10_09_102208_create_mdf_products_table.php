<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMdfProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'mdf_products', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('mdf_id');
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->float('price');
            $table->integer('quantity');
            $table->text('description');
            $table->string('type', 20);
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
        Schema::dropIfExists('mdf_products');
    }
}
