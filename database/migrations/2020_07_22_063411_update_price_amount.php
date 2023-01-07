<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePriceAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update Price Field in Particular Tables

        // deals
        Schema::table(
            'deals', function (Blueprint $table){
            $table->float('price', 25, 2)->nullable()->change();
        }
        );

        // estimation_products
        Schema::table(
            'estimation_products', function (Blueprint $table){
            $table->float('price', 25, 2)->change();
        }
        );

        // products
        Schema::table(
            'products', function (Blueprint $table){
            $table->float('price', 25, 2)->change();
        }
        );

        // inovice_products
        Schema::table(
            'invoice_products', function (Blueprint $table){
            $table->float('price', 25, 2)->change();
        }
        );

        // Update Amount Field in Particular Tables

        // expenses
        Schema::table(
            'expenses', function (Blueprint $table){
            $table->float('amount', 25, 2)->nullable()->change();
        }
        );

        // invoice_payments
        Schema::table(
            'invoice_payments', function (Blueprint $table){
            $table->float('amount', 25, 2)->change();
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
        // deals
        Schema::table(
            'deals', function (Blueprint $table){
            $table->float('price')->nullable();
        }
        );

        // estimation_products
        Schema::table(
            'estimation_products', function (Blueprint $table){
            $table->float('price');
        }
        );

        // products
        Schema::table(
            'products', function (Blueprint $table){
            $table->float('price');
        }
        );

        // invoice_products
        Schema::table(
            'invoice_products', function (Blueprint $table){
            $table->float('price', 25, 2)->nullable()->change();
        }
        );

        // expense
        Schema::table(
            'expense', function (Blueprint $table){
            $table->float('amount')->nullable();
        }
        );

        // invoice_payments
        Schema::table(
            'invoice_payments', function (Blueprint $table){
            $table->float('amount');
        }
        );
    }
}
