<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'bio',
        'type_id',
        'breed_id',
        'color_id',
        'age_year',
        'age_month',
        'gender',
        'playfullness',
        'active_level',
        'friendliness'
    ];

    public function hasFriends() // Mengundang menjadi teman
    {
        return $this->belongsToMany(User::class, 'friendship', 'user_id', 'friend_id')
            ->withPivot('status');
    }

    public function belongFriends() // Di undang menjadi teman
    {
        return $this->belongsToMany(User::class, 'friendship', 'friend_id', 'user_id')
            ->withPivot('status');
    }
}
