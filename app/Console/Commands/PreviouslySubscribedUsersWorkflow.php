<?php

namespace App\Console\Commands;

use App\Jobs\NotificationJob;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class PreviouslySubscribedUsersWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:previously-subscribed-users-workflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users with expired subscriptions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Previously Subscribed Users Workflow command started.');

        // Retrieve the active workflow for previously subscribed users
        $workflow = DB::table('user_workflows')
            ->where('name', 'app:previously-subscribed-users-workflow')
            ->where('is_active', 1)
            ->first();

        if (!$workflow) {
            Log::warning('No workflow found for app:previously-subscribed-users-workflow.');
            return;
        }

        // Get previously subscribed users with expired subscriptions
        $previouslySubscribedUserscmTokens = $this->getPreviouslySubscribedUsers();

        if ($previouslySubscribedUserscmTokens->isEmpty()) {
            Log::info('No previously subscribed users found.');
            return;
        }

        $notificationTitle = $workflow->notification_title;
        $notificationText = $workflow->notification_text;

        // Dispatch notifications to previously subscribed users
        NotificationJob::dispatch($notificationTitle, $notificationText, $previouslySubscribedUserscmTokens);
        Log::info('Notifications sent to previously subscribed users successfully.');
    }

    /**
     * Fetch users who previously had a subscription (expired subscriptions).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPreviouslySubscribedUsers()
    {
        // Fetch users who had expired subscriptions
        $expiredSubscriptionUsers = UserSubscription::select('userCognitoId')
            ->distinct()
            ->whereIn('userCognitoId', function ($query) {
                $query->select('userCognitoId')
                    ->from('usersSubscriptions')
                    ->groupBy('userCognitoId')
                    ->havingRaw('MAX(expirationDate) < ?', [now()]);
            })
            ->pluck('userCognitoId');
        $fcmTokens = DB::table('usersTokens')
            ->whereIn('userCognitoId', $expiredSubscriptionUsers)
            ->pluck('token');
        return $fcmTokens;
    }
}
