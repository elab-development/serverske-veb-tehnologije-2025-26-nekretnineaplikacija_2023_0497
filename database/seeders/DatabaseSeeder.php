<?php
namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kreiraj 5 korisnika, svaki sa po 3 nekretnine
        User::factory(5)->create()->each(function ($user) {
            Property::factory(3)->create(['user_id' => $user->id])
                ->each(function ($property) use ($user) {
                    Inquiry::factory(2)->create([
                        'property_id' => $property->id,
                        'user_id'     => $user->id,
                    ]);
                });
        });
    }
}