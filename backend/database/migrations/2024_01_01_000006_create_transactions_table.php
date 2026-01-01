<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no')->unique();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->enum('business_type', ['retail', 'minimarket', 'fnb'])->default('retail'); // Tipe bisnis
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Kasir (nullable for public orders)
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->nullable(); // Nullable for pending orders
            $table->decimal('change_amount', 15, 2)->nullable(); // Nullable for pending orders
            $table->enum('payment_method', ['cash', 'qris', 'transfer', 'ewallet', 'multiple'])->nullable();
            $table->json('payment_details')->nullable(); // Multi payment
            $table->text('notes')->nullable();
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null'); // F&B
            $table->enum('status', ['pending', 'processed', 'delivered', 'completed', 'void', 'refund'])->default('completed');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('transaction_no');
            $table->index('outlet_id');
            $table->index('business_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
