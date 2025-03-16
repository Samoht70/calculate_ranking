<?php

namespace Database\Seeders;

use App\Models\Arena;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ArenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arenasThreshold = [0, 20000, 60000, 120000, 200000];

        Arena::factory()
            ->count(5)
            ->sequence(fn(Sequence $sequence) => ['minimum_threshold' => $arenasThreshold[$sequence->index]])
            ->create();
    }
}
