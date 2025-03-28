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
        Schema::create('sos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sos_category_id')->constrained('sos_categories')->onDelete('cascade'); // relation to users.id with delete restrict
            $table->foreignId('alert_by')->constrained('users')->onDelete('cascade'); // relation to users.id with delete restrict
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade'); // relation to societies.id with delete restrict
            $table->foreignId('block_id')->constrained('blocks')->onDelete('cascade'); // relation to blocks.id with delete restrict
            $table->string('area');
            $table->text('description')->nullable();
            $table->string('phone');
            $table->integer('floor')->nullable(); // Can be null if not applicable
            $table->string('unit_no')->nullable();
            $table->string('unit_type')->nullable();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['new', 'responding', 'resolved', 'escalated'])->default('new'); // default status as new
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos');
    }
};
