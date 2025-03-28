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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('type_of_visitor');
            $table->string('visiting_frequency');
            $table->string('visitor_name');
            $table->string('phone');
            $table->integer('no_of_visitors');
            $table->date('date');
            $table->time('time');
            $table->string('vehicle_number')->nullable();
            $table->string('purpose_of_visit');
            $table->string('valid_till');
            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');
            $table->string('image')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->string('added_by_role');
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
