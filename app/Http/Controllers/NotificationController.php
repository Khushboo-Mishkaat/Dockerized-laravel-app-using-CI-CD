<?php

namespace App\Http\Controllers;

use App\Jobs\NotificationJob;
use App\Models\Token;
use App\Models\GuestUser;
use Illuminate\Http\Request;
use Log;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $title = $request->input('title');
        $message = $request->input('body');

        // for users
        if ($request->has('user_ids')) {
            $userIds = $request->input('user_ids');
            $fcm_tokens = [];
            foreach ($userIds as $userCognitoId) {
                $tokens = Token::where('userCognitoId', $userCognitoId)->get();
                foreach ($tokens as $token) {
                    if ($token && $token->token) {
                        $fcm_tokens[] = $token->token;
                    } else {
                        Log::warning('No FCM token found for user:', ['userCognitoId' => $userCognitoId]);
                    }
                }
            }

            if (!empty($fcm_tokens)) {
                NotificationJob::dispatch($title, $message, $fcm_tokens);
            } else {
                Log::warning('No FCM tokens to dispatch for users.');
            }
        }

        // for guest users
        if ($request->filled('guest_user_ids')) {
            $guestUserIds = $request->input('guest_user_ids');
            $guest_fcm_tokens = [];
            foreach ($guestUserIds as $guestUserId) {
                $guestUser = GuestUser::where('deviceId', $guestUserId)->first();
                if ($guestUser && $guestUser->fcm_token) {
                    $guest_fcm_tokens[] = $guestUser->fcm_token;
                } else {
                }
            }

            if (!empty($guest_fcm_tokens)) {
                NotificationJob::dispatch($title, $message, $guest_fcm_tokens);
            } else {
                Log::warning('No FCM tokens to dispatch for guest users.');
            }
        }

        return response()->json(['status' => 'Notification is being sent']);
    }
}
