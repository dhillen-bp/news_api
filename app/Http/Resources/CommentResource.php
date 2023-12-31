<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'post_id'           => $this->post_id,
            'comments_content'  => $this->comments_content,
            'user_id'           => $this->user_id,
            'commentator'       => $this->whenLoaded('commentator', function () {
                return $this->commentator->username;
            }),
            'created_at'    => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
