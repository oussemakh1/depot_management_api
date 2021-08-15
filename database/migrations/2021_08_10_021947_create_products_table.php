<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unqiue();
            $table->string("size");
            $table->decimal("weight");
            $table->decimal("cost");
            $table->integer("quantity");
            $table->string("type");
            $table->date("expiredat")->nullable();
            $table->unsignedBigInteger("depot_id");
            $table->foreign("depot_id")->references("id")->on("depots")->onDelete("cascade");
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
        Schema::dropIfExists('products');
    }
}
