<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('customers')->onDelete('set null'); // doctor is also a customer
            $table->string('doctor_name');
            $table->string('doctor_license')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pharmacist who processed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
};
