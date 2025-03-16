<?php

use App\Models\Arena;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arena_ranking', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained('users');
            $table->foreignIdFor(Arena::class)->constrained('arenas');

            $table->dateTime('ranked_at');
            $table->unsignedInteger('ranking');
            $table->decimal('turnover', 10)->default(0);
            $table->tinyInteger('arena_evolution')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
