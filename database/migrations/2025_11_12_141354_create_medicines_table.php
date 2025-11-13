<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 128)->unique();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 8, 2);
            $table->decimal('cost_price', 8, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock')->default(10);
            $table->integer('max_stock')->default(100);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('strength')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicines');
    }
};
