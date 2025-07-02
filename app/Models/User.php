<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',       
        'gender',       
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function posts()
    {
        return $this->hasMany(Post::class)->latest(); 
    }

        
    public function friends()
    {
        
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id');
    }

   
    public function following()
    {
        
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'followed_user_id');
    }
   

}
