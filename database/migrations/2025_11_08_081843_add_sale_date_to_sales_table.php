<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'sales_date')) {
                $table->renameColumn('sales_date', 'sale_date');
            } else {
                $table->dateTime('sale_date')->nullable()->after('customer_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'sale_date')) {
                $table->renameColumn('sale_date', 'sales_date');
            }
        });
    }
};
