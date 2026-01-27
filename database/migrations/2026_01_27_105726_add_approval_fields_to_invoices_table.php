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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('unc_number')->nullable()->after('remarks');
            $table->string('approval_email')->nullable()->after('unc_number');
            $table->enum('status', ['draft', 'approved'])->default('draft')->after('approval_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['unc_number', 'approval_email', 'status']);
        });
    }
};
