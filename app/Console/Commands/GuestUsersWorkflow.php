<?php
namespace App\Console\Commands;

use App\Jobs\NotificationJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class GuestUsersWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:guest-users-workflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to guest users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Guest Users Workflow command started.');

        $workflow = DB::table('user_workflows')
            ->where('name', 'app:guest-users-workflow')
            ->where('is_active', 1) 
            ->first();

        if (!$workflow) {
            Log::warning('No workflow found for app:guest-users-workflow.');
            return;
        }

        $fcmTokens = DB::table('guestUsers')
            ->where('isGuest', 1) 
            ->pluck('fcm_token');

        if ($fcmTokens->isEmpty()) {
            Log::info('No FCM tokens found for guest users.');
            return;
        }

        $notificationTitle = $workflow->notification_title;
        $notificationText = $workflow->notification_text;
        NotificationJob::dispatch($notificationTitle, $notificationText, $fcmTokens);
        Log::info('Notifications sent to guest users successfully.');
    }

    /**
     * Function to send notification to a specific FCM token.
     *
     * @param string $token
     * @param string $title
     * @param string $message
     */
    protected function sendNotification(string $token, string $title, string $message)
    {
        // Logic to send notification via FCM or any other service
        // This is just a placeholder, implement your actual notification sending logic
        Log::info('Sending notification', [
            'token' => $token,
            'title' => $title,
            'message' => $message
        ]);

        // Example: NotificationJob::dispatch($title, $message, [$token]);
    }
}
