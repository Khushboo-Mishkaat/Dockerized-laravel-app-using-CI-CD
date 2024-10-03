<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSubscription;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $newRegistrations = User::where('created', '>=', now()->subDays(30))->count();

        $totalSubscribedUsers = UserSubscription::where('expirationDate', '>', now())
        ->distinct('userCognitoId')
        ->count('userCognitoId');
    
        return Inertia::render('Dashboard', [
            'totalUsers' => $totalUsers,
            'newRegistrations' => $newRegistrations,
            'totalSubscribedUsers' => $totalSubscribedUsers,
        ]);
    }
}
