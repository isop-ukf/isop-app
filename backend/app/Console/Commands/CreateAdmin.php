<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Create Admin ===');

        // Načítanie údajov interaktívne
        $firstName = $this->ask('First name');
        $lastName = $this->ask('Last name');
        $email = $this->ask('E-mail');

        // Kontrola duplicity emailu
        if (User::where('email', $email)->exists()) {
            $this->error('A user with the same email already exists.');
            return 1;
        }

        $password = $this->secret('Enter password');
        $phone = $this->ask('Enter phone number');

        // Vytvorenie používateľa
        $user = User::create([
            'name' => $firstName . ' ' . $lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
            'active' => true,
            'role' => 'ADMIN',
            'phone' => $phone,
        ]);

        $this->info("\n Admin {$user->first_name} {$user->last_name} created.");
        return 0;
    }
}
