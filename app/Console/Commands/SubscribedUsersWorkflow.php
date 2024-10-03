<?php

namespace App\Console\Commands;

use App\Jobs\NotificationJob;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class SubscribedUsersWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subscribed-users-workflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to subscribed users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Subscribed Users Workflow command started.');

        // Retrieve the active workflow for subscribed users
        $workflow = DB::table('user_workflows')
            ->where('name', 'app:subscribed-users-workflow')
            ->where('is_active', 1)
            ->first();

        if (!$workflow) {
            Log::warning('No workflow found for app:subscribed-users-workflow.');
            return;
        }

        // Get subscribed users
        $subcribedUserFcmTokens = $this->getSubscribedUsers();

        if ($subcribedUserFcmTokens->isEmpty()) {
            Log::info('No subscribed users found.');
            return;
        }

        $notificationTitle = $workflow->notification_title;
        $notificationText = $workflow->notification_text;

        // Dispatch notifications to subscribed users
        NotificationJob::dispatch($notificationTitle, $notificationText, $subcribedUserFcmTokens);
        Log::info('Notifications sent to subscribed users successfully.');
    }

    /**
     * Fetch users with active subscriptions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSubscribedUsers()
    {
        $subscribedUsersCognitoid = UserSubscription::where('expirationDate', '>', now())
            ->distinct('userCognitoId')
            ->pluck('userCognitoId');
            $fcmTokens = DB::table('usersTokens')
            ->whereIn('userCognitoId', $subscribedUsersCognitoid) 
            ->pluck('token');
    
        return $fcmTokens;
    }
}
