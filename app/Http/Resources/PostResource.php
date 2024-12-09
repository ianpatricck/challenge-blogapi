<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_title' => $this->title,
            'post_content' => $this->content,
            'author_id' => $this->user_id,
            'published_at' => $this->created_at,
            'last_update' => $this->updated_at,
            'author' => User::find($this->user_id),
            'comments' => Comment::where('post_id', $this->id)->get(),
            'comment_count' => Comment::where('post_id', $this->id)
                ->get()
                ->count(),
            'like_count' => Like::where('post_id', $this->id)
                ->where('user_id', $this->user_id)
                ->get()
                ->count(),
        ];
    }
}
