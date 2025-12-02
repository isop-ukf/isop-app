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
    protected $signature = 'user:create-garant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interaktívne vytvorí nového používateľa s rolou admin (garant)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Vytvorenie garanta (admin) ===');

        // Načítanie údajov interaktívne
        $firstName = $this->ask('Zadaj krstné meno');
        $lastName = $this->ask('Zadaj priezvisko');
        $email = $this->ask('Zadaj email');

        // Kontrola duplicity emailu
        if (User::where('email', $email)->exists()) {
            $this->error('Používateľ s týmto emailom už existuje.');
            return 1;
        }

        $password = $this->secret('Zadaj heslo (nebude sa zobrazovať)');
        $phone = $this->ask('Zadaj telefón ');

        // Vytvorenie používateľa
        $user = User::create([
            'name'       => $firstName . ' ' . $lastName,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'phone'      => $phone,
            'role'       => 'ADMIN',
            'password'   => Hash::make($password),
        ]);

        $this->info("\n Garant {$user->first_name} {$user->last_name} bol úspešne vytvorený s rolou ADMIN.");
        $this->info("Email: {$user->email}");

        return 0;
    }
}
