<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_filter',
        'name',
        'is_active',
        'notification_title',
        'notification_text',
        'recurring_duration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    const USER_FILTERS = [
        'PreviouslySubscribed' => 'PreviouslySubscribed',
        'Subscribed' => 'Subscribed',
        'NoSubscription' => 'NoSubscription',
        'Guest' => 'Guest',
        'Inactive' => 'Inactive',
        'All' => 'All',
    ];

    const RECURRING_DURATIONS = [
        'Daily' => 'Daily',
        'Weekly' => 'Weekly',
        'Monthly' => 'Monthly',
    ];
}
