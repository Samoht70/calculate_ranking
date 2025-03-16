<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(User::class)->constrained('users');

            $table->date('occurs_at');
            $table->decimal('signed_amount', 10)->default(0);
            $table->decimal('invoiced_amount', 10)->default(0);
            $table->decimal('turnover', 10)->storedAs('signed_amount + invoiced_amount');

            $table->unique(['user_id', 'occurs_at']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
