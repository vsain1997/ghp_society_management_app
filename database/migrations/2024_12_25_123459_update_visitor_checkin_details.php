<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Rename the table
        Schema::rename('visitor_checkin_details', 'checkin_details');

        // Update the structure of the table
        Schema::table('checkin_details', function (Blueprint $table) {
            // Make visitor_id nullable
            $table->unsignedBigInteger('visitor_id')->nullable()->change();

            // Add parcel_id column and foreign key
            $table->foreignId('parcel_id')->nullable()->constrained('parcels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            // Revert parcel_id addition
            $table->dropForeign(['parcel_id']);
            $table->dropColumn('parcel_id');

            // Make visitor_id non-nullable again
            $table->unsignedBigInteger('visitor_id')->nullable(false)->change();
        });

        // Rename the table back
        Schema::rename('checkin_details', 'visitor_checkin_details');
    }

};
