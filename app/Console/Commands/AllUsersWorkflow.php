<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class AllUsersWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-users-workflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('All Users Workflow command started.');

        $workflow = DB::table('user_workflows')
            ->where('name', 'app:all-users-workflow')
            ->where('is_active', 1)
            ->first();
        if (!$workflow) {
            Log::warning('No workflow found for app:guest-users-workflow.');
            return;
        };
        $fcmTokens = DB::table('usersTokens')
            ->pluck('token');

        if ($fcmTokens->isEmpty()) {
            Log::info('No FCM tokens found for users.');
            return;
        }

        $notificationTitle = $workflow->notification_title;
        $notificationText = $workflow->notification_text;

        // Dispatch notification to all users
        NotificationJob::dispatch($notificationTitle, $notificationText, $fcmTokens);
        Log::info('Notifications sent to all users successfully.');
    }
}
