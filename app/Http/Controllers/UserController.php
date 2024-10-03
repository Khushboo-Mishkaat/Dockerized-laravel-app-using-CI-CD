<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\GuestUser;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Log;
class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filter = $request->query('filter', 'all');
            $searchQuery = $request->query('search', null); 
            $users = User::query();
            $totalUsers = User::count();

            $newRegistrations = User::where('created', '>=', now()->subDays(30))->count();
            $totalSubscribedUsers = UserSubscription::where('expirationDate', '>', now())
            ->distinct('userCognitoId')
            ->count('userCognitoId');
        
            $guest_users = null;
            if ($searchQuery) {
                $users->where(function ($query) use ($searchQuery) {
                    $query->where('userName', 'like', '%' . $searchQuery . '%')
                          ->orWhere('email', 'like', '%' . $searchQuery . '%');
                });
            }

            switch ($filter) {
                case 'guest':
                    $guest_users = GuestUser::where('isGuest', 1)->get();
                    break;

                case 'registered-no-subscription':
                    $noSubscriptionUsers = User::whereNotIn('cognitoId', UserSubscription::pluck('userCognitoId'))->pluck('cognitoId');
                    $users->whereIn('cognitoId', $noSubscriptionUsers);
                    break;

                case 'previously-subscribed':
                    $expiredSubscriptionUsers = UserSubscription::select('userCognitoId')
                        ->distinct()
                        ->whereIn('userCognitoId', function ($query) {
                            $query->select('userCognitoId')
                                  ->from('usersSubscriptions')
                                  ->groupBy('userCognitoId')
                                  ->havingRaw('MAX(expirationDate) < ?', [now()]);
                        })
                        ->pluck('userCognitoId');
    
                    $users->whereIn('cognitoId', $expiredSubscriptionUsers);
                    break;

                    case 'subscribed':
                        $subscribedUsers = UserSubscription::where('expirationDate', '>', now())
                            ->distinct('userCognitoId')
                            ->pluck('userCognitoId');
                    
                        $users->whereIn('cognitoId', $subscribedUsers);
                        break;
                        
                case 'inactive':
                    $users = $users->where('isActivated', '!=', 1)->whereNotNull('isActivated');
                    break;

                case 'all':
                default:
                    break;
            }

            $paginatedUsers = $users->paginate(20);
            $latestSubscriptions = UserSubscription::select('userCognitoId', 'subscriptionDate', 'expirationDate')
                ->where('expirationDate', '>', now())
                ->orWhere('expirationDate', '<=', now())
                ->get()
                ->groupBy('userCognitoId')
                ->map(function ($subscriptions) {
                    return $subscriptions->sortByDesc('subscriptionDate')->first();
                });

            $totalSubscriptions = UserSubscription::selectRaw('userCognitoId, COUNT(*) as total')
                ->groupBy('userCognitoId')
                ->pluck('total', 'userCognitoId');

            foreach ($paginatedUsers as $user) {
                $userId = $user->cognitoId;
                if ($latestSubscriptions->has($userId)) {
                    $latestSubscription = $latestSubscriptions->get($userId);
                    $user->isSubscribed = $latestSubscription->expirationDate > now() ? 'Active' : 'Expired';
                    $user->lastSubscriptionDate = $latestSubscription->subscriptionDate->format('Y-m-d'); 
                } else {
                    $user->isSubscribed = 'Expired';
                    $user->lastSubscriptionDate = 'N/A'; 
                }
                
                $user->totalNumberOfSubscriptions = $totalSubscriptions->get($userId, 0); // Default to 0 if no subscriptions
            }

            $response = [
                'data' => $paginatedUsers->items(),
                'current_page' => $paginatedUsers->currentPage(),
                'last_page' => $paginatedUsers->lastPage(),
                'per_page' => $paginatedUsers->perPage(),
                'total' => $paginatedUsers->total(),
                'prev_page_url' => $paginatedUsers->previousPageUrl(),
                'next_page_url' => $paginatedUsers->nextPageUrl(),
                'guest_user' => $guest_users,
                'totalUsers' => $totalUsers,
                'newRegistrations' => $newRegistrations,
                'totalSubscribedUsers' => $totalSubscribedUsers,
            ];

            if ($request->wantsJson()) {
                return response()->json($response);
            }

            return Inertia::render('User/All', [
                'users' => $response
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
