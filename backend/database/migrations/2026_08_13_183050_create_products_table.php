<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->string('sku')->nullable();
            $table->foreignId('category_id')->constrained('references')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('references')->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('references')->nullOnDelete();
            $table->string('image')->default('default.png');
            $table->decimal('min_quantity', 15, 3)->default(0);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
