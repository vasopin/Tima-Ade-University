<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UpdateAllUsersPasswordCommand extends Command
{
    protected $signature = 'users:update-all-passwords';
    protected $description = 'Update all existing users to use the password "password" with secure hashing';

    public function handle(): int
    {
        $this->info('Updating all existing user passwords to use "password" with secure hashing...');
        
        $passwordHash = Hash::make('password');
        
        $updatedCount = User::query()->update(['password' => $passwordHash]);
        
        $this->info("Successfully updated {$updatedCount} users with the new password hash.");
        $this->info('All existing user accounts can now authenticate with password: "password"');
        $this->info('Password is securely hashed using bcrypt (BCRYPT_ROUNDS=12)');
        
        return Command::SUCCESS;
    }
}
