<?php

namespace Coyote\Http\Controllers\User;

use Coyote\Http\Resources\UserResource;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelationsController extends BaseController
{
    use SettingsTrait;

    private Cache $cache;

    public function __construct(Cache $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    public function showRelations()
    {
        $users = $this->auth->relations()->with(['relatedUser' => fn (BelongsTo $builder) => $builder->withTrashed()])->get()->pluck('relatedUser');

        return $this->view('user.relations', [
            'users' => UserResource::collection($users)
        ]);
    }

    public function block(int $relatedUserId)
    {
        abort_if($relatedUserId === $this->userId, 500);

        $this->auth->relations()->updateOrInsert(['related_user_id' => $relatedUserId, 'user_id' => $this->userId], ['is_blocked' => true]);
        $this->clearCache();
    }

    public function unblock(int $relatedUserId)
    {
        $this->auth->relations()->where('related_user_id', $relatedUserId)->delete();
        $this->clearCache();
    }

    public function follow(int $relatedUserId)
    {
        abort_if($relatedUserId === $this->userId, 500);

        $this->auth->relations()->updateOrInsert(['related_user_id' => $relatedUserId, 'user_id' => $this->userId], ['is_blocked' => false]);
        $this->clearCache();
    }

    private function clearCache()
    {
        $this->cache->forget('followers:' . $this->userId);
    }
}
