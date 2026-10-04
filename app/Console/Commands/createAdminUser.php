<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or Update an Admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('Enter admin email');
        $username = $this->ask('Enter admin username');
        $name = $this->ask('Enter admin name');
        $password = $this->secret('Enter admin password');

        $admin = Admin::updateOrCreate(
            ['email' => $email], // شرط پیدا کردن (اگر این ایمیل بود)
            [                    // مقادیری که باید ست شوند
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
            ]
        );
        $admin->status = 1;
        $admin->update();

        $this->info("Admin processed successfully: {$admin->email}");
        return 0;
    }
}
