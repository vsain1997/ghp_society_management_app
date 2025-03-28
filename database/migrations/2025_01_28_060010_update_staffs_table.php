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
        Schema::table('staffs', function (Blueprint $table) {
            //
            $table->string('gender')->nullable()->after('society_id')->comment('Gender of the staff');
            $table->date('dob')->nullable()->after('gender')->comment('Date of birth');
            $table->string('assigned_area')->nullable()->after('dob')->comment('Assigned area for work');
            $table->string('employee_id')->nullable()->after('assigned_area')->comment('Unique employee ID');

            $table->time('shift_from')->nullable()->after('employee_id')->comment('Shift start time');
            $table->time('shift_to')->nullable()->after('shift_from')->comment('Shift end time');
            $table->string('off_days')->nullable()->after('shift_to')->comment('Off days of the week');

            $table->string('emer_name')->nullable()->after('off_days')->comment('Emergency contact name');
            $table->string('emer_relation')->nullable()->after('emer_name')->comment('Relationship with emergency contact');
            $table->string('emer_phone')->nullable()->after('emer_relation')->comment('Emergency contact phone number');

            $table->date('date_of_join')->nullable()->after('emer_phone')->comment('Date of joining');
            $table->date('contract_end_date')->nullable()->after('date_of_join')->comment('Contract end date');
            $table->decimal('monthly_salary', 10, 2)->nullable()->after('contract_end_date')->comment('Monthly salary of the staff');

            $table->string('card_file')->nullable()->after('card_number')->comment('File associated with the staff card');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staffs', function (Blueprint $table) {
            // Dropping the added columns
            $table->dropColumn([
                'gender',
                'dob',
                'assigned_area',
                'employee_id',
                'shift_from',
                'shift_to',
                'off_days',
                'emer_name',
                'emer_relation',
                'emer_phone',
                'date_of_join',
                'contract_end_date',
                'monthly_salary',
                'card_file',
            ]);
        });
    }
};
