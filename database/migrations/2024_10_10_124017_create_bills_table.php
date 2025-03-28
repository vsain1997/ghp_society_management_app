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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('service_id')
                ->constrained('bill_services')
                ->onDelete('cascade');
            $table->enum('bill_type', ['my_bill', 'maintenance']);
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->foreignId('society_id')
                ->constrained('societies')
                ->onDelete('cascade');
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
