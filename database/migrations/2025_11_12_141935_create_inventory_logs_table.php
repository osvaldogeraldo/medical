<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'return', 'expired']);
            $table->integer('quantity_change'); // positive for addition, negative for deduction
            $table->integer('stock_after_change');
            $table->string('reference_type')->nullable(); // Sale, Purchase, etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // sale_id, purchase_id, etc.
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_logs');
    }
};
