<?php

namespace Duukkis\Bsky\Entities;

use Duukkis\Bsky\Bsky;

class RepoEntity
{
    public function __construct(private Bsky $bsky)
    {
    }

    /**
     * 'collection' => 'app.bsky.feed.post',
     * 'repo' => $bluesky->getAccountDid(),
     * 'record' => [
     *     'text' => 'Testing #TestingInProduction',
     *     'langs' => ['en'],
     *     'createdAt' => date('c'),
     *     '$type' => 'app.bsky.feed.post',
     * ],
     * @param array $params
     * @return mixed ->uri ->cid
     * @throws \Exception
     */
    public function createRecord(string $text, array $langs = ['fi']): mixed
    {
        $params = [
            'collection' => 'app.bsky.feed.post',
            'repo' => $this->bsky->getDid(),
            'record' => [
                'text' => $text,
                'langs' => $langs,
                'createdAt' => date('c'),
                '$type' => 'app.bsky.feed.post',
            ],
        ];
        return $this->bsky->post("https://bsky.social/xrpc/com.atproto.repo.createRecord", $params);
    }

    /**
     * https://github.com/cjrasmussen/BlueskyApi
     */
}