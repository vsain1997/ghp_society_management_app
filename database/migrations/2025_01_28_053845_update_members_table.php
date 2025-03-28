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
        Schema::table('members', function (Blueprint $table) {
            // Adding new columns
            $table->string('ownership_type')->nullable()->after('aprt_no')->comment('Ownership type of the member');
            $table->string('owner_name')->nullable()->after('ownership_type')->comment('Name of the owner');
            $table->string('emer_name')->nullable()->after('owner_name')->comment('Emergency contact name');
            $table->string('emer_relation')->nullable()->after('emer_name')->comment('Relationship with emergency contact');
            $table->string('emer_phone')->nullable()->after('emer_relation')->comment('Emergency contact phone number');

            // Updating comments on existing columns
            $table->string('aprt_no')->comment('Property number')->change();
            $table->unsignedBigInteger('block_id')->comment('Tower ID')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {

            // Dropping the new columns
            $table->dropColumn([
                'ownership_type',
                'owner_name',
                'emer_name',
                'emer_relation',
                'emer_phone',
            ]);

            // Reverting comments (if necessary, optional)
            $table->string('aprt_no')->comment(null)->change();
            $table->unsignedBigInteger('block_id')->comment(null)->change();

        });
    }
};
