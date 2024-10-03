<?php

namespace App\Console\Commands;

use App\Jobs\NotificationJob;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class NoSubscriptionUsersWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:no-subscription-users-workflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to registered users with no active subscription.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('No Subscription Users Workflow command started.');

        $workflow = DB::table('user_workflows')
            ->where('name', 'app:no-subscription-users-workflow')
            ->where('is_active', 1)
            ->first();

        if (!$workflow) {
            Log::warning('No workflow found for app:no-subscription-users-workflow.');
            return;
        }

        $noSubscriptionUsersFcmTokens = $this->getRegisteredNoSubscriptionUsers();

        if ($noSubscriptionUsersFcmTokens->isEmpty()) {
            Log::info('No registered users found without active subscriptions.');
            return;
        }

        $notificationTitle = $workflow->notification_title;
        $notificationText = $workflow->notification_text;

        NotificationJob::dispatch($notificationTitle, $notificationText, $noSubscriptionUsersFcmTokens);
        Log::info('Notifications sent to registered users with no active subscriptions successfully.');
    }

    /**
     * Fetch registered users with no active subscription.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRegisteredNoSubscriptionUsers()
    {

        Log::info('noSubscriptionUsers started');
        $noSubscriptionUsers = User::whereNotIn('cognitoId', UserSubscription::pluck('userCognitoId'))->pluck('cognitoId');
        $fcmTokens = DB::table('usersTokens')
            ->whereIn('userCognitoId', $noSubscriptionUsers)
            ->pluck('token');

        return $fcmTokens;
    }
}
