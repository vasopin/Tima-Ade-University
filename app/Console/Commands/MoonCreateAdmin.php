<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MoonCreateAdmin extends Command
{
    protected $signature = 'moon:create-admin {--name=} {--email=}';
    protected $description = 'Create a new Super Admin user account for Tima-Ade University';

    public function handle(): int
    {
        $this->info('Tima-Ade University — Create Super Admin');

        $superAdminRole = Role::firstOrCreate(
            ['slug' => Role::SUPER_ADMIN],
            ['name' => 'Super Admin', 'description' => 'Unrestricted super administrator access']
        );

        $name = $this->option('name') ?: $this->ask('Full Name', 'Super Administrator');
        $email = $this->option('email') ?: $this->ask('Email Address');

        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email'],
        ]);
        if ($validator->fails()) {
            $this->error('Invalid email address.');
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');
            return 1;
        }

        // Prompt for password
        while (true) {
            $password = $this->secret('Password (min 8 chars)');
            $confirm = $this->secret('Confirm Password');
            if ($password !== $confirm) {
                $this->error('Passwords do not match. Try again.');
                continue;
            }
            if (!is_string($password) || strlen($password) < 8) {
                $this->error('Password must be at least 8 characters.');
                continue;
            }
            break;
        }

        $phone = $this->ask('Phone Number (optional)');

        $user = User::create([
            'role_id' => $superAdminRole->id,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone,
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->info('Super Admin created: ' . $user->email);
        return 0;
    }
}
