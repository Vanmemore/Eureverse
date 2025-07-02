<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine if the user can update the post.
     */
    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }
    public function delete(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }

}
