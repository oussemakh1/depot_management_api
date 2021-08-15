<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("provider_id");
            $table->string("status");
            $table->integer("quantity");
            $table->date("recivedat");
            $table->float("price");
            $table->float("discount");
            $table->float("total");
            $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
            $table->foreign("provider_id")->references("id")->on("providers")->onDelete("cascade");
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
        Schema::dropIfExists('invoices');
    }
}
