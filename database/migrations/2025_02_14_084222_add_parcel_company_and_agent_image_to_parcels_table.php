<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            //
            $table->string('parcel_company_name', 250)->nullable()->after('time');
            $table->string('delivery_agent_image', 250)->nullable()->after('parcel_company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            //
            $table->dropColumn(['parcel_company_name', 'delivery_agent_image']);
        });
    }
};
