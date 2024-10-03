<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Users</h1>
        <ul class="bg-white shadow-md rounded-lg divide-y divide-gray-200">
            @foreach ($users as $user)
                @php
                    // Determine subscription status
                    $currentDate = now();
                    $expirationDate = \Carbon\Carbon::parse($user->expirationDate);
                    $status = $user->isActivated ? ($currentDate->gt($expirationDate) ? 'Expired' : 'Active') : 'N/A';

                    // Handle subscriptionDate formatting
                    $subscriptionDate = $user->subscriptionDate ? \Carbon\Carbon::parse($user->subscriptionDate)->format('Y-m-d') : 'N/A';
                @endphp
                <li class="p-4 hover:bg-gray-100">
                    <div class="flex flex-wrap justify-between items-start space-y-2">
                        <div class="w-full sm:w-1/4">
                            <span class="font-semibold text-blue-600">Username:</span> <span class="text-green-600">{{ $user->userName ?? 'N/A' }}</span>
                        </div>
                        <div class="w-full sm:w-1/4">
                            <span class="font-semibold text-blue-600">Email:</span> <span class="text-green-600">{{ $user->email ?? 'N/A' }}</span>
                        </div>
                        <div class="w-full sm:w-1/4">
                            <span class="font-semibold text-blue-600">Subscription Status:</span> <span class="text-green-600">{{ $status }}</span>
                        </div>
                        <div class="w-full sm:w-1/4">
                            <span class="font-semibold text-blue-600">Last Subscription Date:</span> <span class="text-green-600">{{ $subscriptionDate }}</span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">
            {{-- Display pagination links --}}
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
</body>
</html>
