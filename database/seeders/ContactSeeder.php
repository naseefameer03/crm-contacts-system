<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $statuses = ['Lead', 'Customer', 'Prospect'];
        $companies = ['Google', 'Amazon', 'Tesla', 'Apple', 'Meta'];

        $chunks = 1000;
        $total = 1000000;

        for ($i = 0; $i < $total / $chunks; $i++) {
            $data = [];
            for ($j = 0; $j < $chunks; $j++) {
                $data[] = [
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'company' => $faker->randomElement($companies),
                    'status' => $faker->randomElement($statuses),
                    'created_at' => $faker->dateTimeBetween('-2 years'),
                    'updated_at' => now()
                ];
            }
            DB::table('contacts')->insert($data);
        }
    }
}
