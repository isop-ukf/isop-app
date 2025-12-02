<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Internship;
use App\Models\InternshipStatusData;
use App\Models\StudentData;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create a default admin user
        $admin = User::factory()->create([
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '+421907444555',
            'role' => 'ADMIN',
        ]);

        // create employers and companies
        User::factory(20)
            ->create([
                'role' => 'EMPLOYER'
            ])
            ->each(function ($user) {
                Company::factory()->create([
                    'contact' => $user->id
                ]);
            });

        // create students
        User::factory(20)
            ->create([
                'role' => 'STUDENT'
            ])
            ->each(function ($user) use ($admin) {
                $user->update([
                    'email' => fake()->unique()->userName() . '@student.ukf.sk',
                ]);

                StudentData::factory()->create([
                    'user_id' => $user->id
                ]);

                $internship = Internship::factory()->create([
                    'user_id' => $user->id,
                    'company_id' => Company::inRandomOrder()->value('id'),
                ]);

                InternshipStatusData::factory()->create([
                    'internship_id' => $internship->id,
                    'status' => "SUBMITTED",
                    'note' => 'made by seeder',
                    'modified_by' => $admin->id,
                ]);
            });

        // create some random external API keys
        for ($i = 0; $i < 4; $i++) {
            $admin->createToken(fake()->userName());
        }
    }
}
