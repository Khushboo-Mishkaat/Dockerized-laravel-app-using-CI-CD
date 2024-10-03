<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'usersSubscriptions'; 

    protected $fillable = [
        'userCognitoId',
        'subscriptionDate',
        'expirationDate',
        'freeTrial',
    ];

    protected $casts = [
        'subscriptionDate' => 'datetime',
        'expirationDate' => 'datetime',
        'freeTrial' => 'boolean',
    ];
}
