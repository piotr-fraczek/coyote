<?php

namespace Coyote\Http\Resources\Api;

use Carbon\Carbon;
use Coyote\Http\Resources\AssetsResource;
use Coyote\Http\Resources\UserResource;
use Coyote\Microblog;
use Coyote\Services\UrlBuilder;
use Coyote\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $html
 * @property User $user
 * @property Microblog[] $comments
 * @property int $parent_id
 * @property \Coyote\Models\Asset $assets
 * @property int $comments_count
 * @property array voters_json
 */
class MicroblogResource extends JsonResource
{
    /**
     * DO NOT REMOVE! This will preserver keys from being filtered in data
     *
     * @var bool
     */
    protected $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $only = $this->resource->only(['id', 'votes', 'views', 'text', 'html', 'parent_id']);

        return array_merge(
            $only,
            [
                'url'           => $this->parent_id ? UrlBuilder::microblogComment($this->resource, true) : UrlBuilder::microblog($this->resource, true),
                'created_at'    => $this->created_at->toIso8601String(),
                'updated_at'    => $this->created_at->toIso8601String(),
                'comments'      => $this->when(
                    $this->isNotComment(),
                    function () {
                        $collection = static::collection($this->comments);
                        $collection->preserveKeys = true;

                        return $collection;
                    },
                    []
                ),
                'user'          => UserResource::make($this->user),

                'permissions'   => [
                    'update'    => $this->when($request->user(), fn () => $request->user()->can('update', $this->resource), false)
                ],

                'comments_count'=> $this->when($this->comments_count, $this->comments_count),

                $this->mergeWhen(array_has($this->resource, ['is_voted', 'is_subscribed']), function () {
                    return $this->resource->only(['is_voted', 'is_subscribed']);
                }),

                // @todo do zmiany na assets
                'media'         => $this->whenLoaded('assets', fn () => AssetsResource::collection($this->assets), [])
            ]
        );
    }

    public function preserverKeys()
    {
        if ($this->isNotComment()) {
            $this->resource->setRelation('comments', $this->resource->comments->keyBy('id'));
        }
    }

    private function isNotComment(): bool
    {
        return ! $this->parent_id && $this->resource->relationLoaded('comments');
    }
}
