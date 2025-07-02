<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{   
    
   protected $fillable = [
    'content', 'likes', 'comments', 'user_id', 'image'
    ];

    public function posts()
    {
        return $this->hasMany(\App\Models\Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

}
