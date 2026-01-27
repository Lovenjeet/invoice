<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id');
            $table->foreignId('bill_to_id');
            $table->foreignId('ship_to_id');
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->string('terms')->default('DDP');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_value', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('total_boxes')->default(0);
            $table->decimal('total_gw', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
