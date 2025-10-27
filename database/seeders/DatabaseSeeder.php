<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Available seeders:');
        $this->command->warn(' php artisan db:seed --class=PrimarySeeder');
        $this->command->warn(' php artisan db:seed --class=DummySeeder');
        $this->command->info('Enjoy!');
    }
}
