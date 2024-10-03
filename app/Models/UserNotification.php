<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'notifications';

    // The primary key associated with the table.
    protected $primaryKey = 'id';

    // Indicates if the model should be timestamped.
    public $timestamps = false;

    // The attributes that are mass assignable.
    protected $fillable = [
        'userCognitoId',
        'type',
        'entityId',
        'notification',
        'isRead',
        'created',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'isRead' => 'boolean',
        'created' => 'datetime',
    ];
}
