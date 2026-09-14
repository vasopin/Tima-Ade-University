<?php

namespace App\Console\Commands;

// Deprecated duplicate of the MoonCreateAdmin command.
// This file remains for historical reference but has been disabled to avoid duplicate Artisan command signatures.

use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    // Changed signature to avoid conflicting with the primary 'moon:create-admin' command implemented in MoonCreateAdmin.php
    protected $signature = 'moon:create-admin-deprecated';
    protected $description = 'DEPRECATED: Duplicate create-admin command (kept for backward reference)';

    public function handle(): int
    {
        $this->error('This command is deprecated. Use "php artisan moon:create-admin" instead (MoonCreateAdmin).');
        return 1;
    }
}
