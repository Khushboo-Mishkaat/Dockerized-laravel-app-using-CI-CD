<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'usersTokens';

    // The primary key associated with the table.
    protected $primaryKey = 'id';

    // Indicates if the model should be timestamped.
    public $timestamps = false;

    // The attributes that are mass assignable.
    protected $fillable = [
        'userCognitoId',
        'token',
        'created',
        'updated',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'created' => 'datetime',
        'updated' => 'datetime',
    ];
}
