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

    public function createRecordWithImage(string $text, string $file, string $alt, array $langs = ['fi']): mixed
    {
        $file = file_get_contents($file);
        $blob = $this->bsky->post("https://bsky.social/xrpc/com.atproto.repo.uploadBlob", $file, ['Content-Type: image/jpeg']);

        $params = [
            'collection' => 'app.bsky.feed.post',
            'repo' => $this->bsky->getDid(),
            'record' => [
                'text' => $text,
                'langs' => $langs,
                'createdAt' => date('c'),
                '$type' => 'app.bsky.feed.post',
                'embed' => [
                    '$type' => 'app.bsky.embed.images',
                    'images' => [
                        [
                            'alt' => $alt,
                            'image' => $blob->blob,
                        ],
                    ],
                ],
            ],
        ];
        return $this->bsky->post("https://bsky.social/xrpc/com.atproto.repo.createRecord", $params);
    }
}