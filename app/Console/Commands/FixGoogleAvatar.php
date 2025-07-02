<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixGoogleAvatar extends Command
{
    protected $signature = 'fix:google-avatar';
    protected $description = 'Fix Google avatar URLs for existing users';

    public function handle()
    {
        $this->info('Starting to fix Google avatars...');

        $users = User::whereNotNull('google_id')
            ->whereNotNull('avatar')
            ->get();

        $this->info("Found {$users->count()} users with Google avatars.");

        $updatedCount = 0;

        foreach ($users as $user) {
            $avatar = $user->avatar;

            // Cập nhật size avatar
            if (str_contains($avatar, 'googleusercontent.com')) {
                $newAvatar = str_replace(['s96-c', '=s96-c'], ['s200-c', '=s200-c'], $avatar);

                if ($newAvatar !== $avatar) {
                    $user->update(['avatar' => $newAvatar]);
                    $this->line("✓ Updated avatar for user: {$user->email}");
                    $updatedCount++;
                }
            }
        }

        $this->info("Finished! Updated {$updatedCount} user avatars.");
        return Command::SUCCESS;
    }
}
