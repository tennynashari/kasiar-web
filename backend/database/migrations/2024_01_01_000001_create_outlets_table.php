<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('business_type', ['retail', 'minimarket', 'fnb'])->default('retail');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('enable_qr_order')->default(false); // F&B feature
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
