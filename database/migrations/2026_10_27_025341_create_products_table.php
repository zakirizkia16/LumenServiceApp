<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id'); // Field 1
            $table->string('sku')->unique();          // Field 2
            $table->string('name');                   // Field 3
            $table->decimal('price', 12, 2);          // Field 4
            $table->integer('stock');                 // Field 5
            $table->text('description')->nullable();   // Field 6
            $table->enum('status', ['active', 'inactive'])->default('active'); // Field 7
            $table->timestamps();                     // created_at & updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}