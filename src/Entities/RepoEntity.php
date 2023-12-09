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
        $links = $this->parseLinks($text);
        if ($links != []) {
            $params["record"]["facets"] = [$links];
        }
        return $this->bsky->post("https://bsky.social/xrpc/com.atproto.repo.createRecord", $params);
    }

    public function parseLinks(string $str): ?array
    {
        preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $str, $result, PREG_PATTERN_ORDER);
        if (isset($result[0][0]) && $result[0][0] != []) {
            $uri = $result[0][0];
            // don't use mb_ so we count bytes
            $byteStart = strpos($str, $uri);
            $byteEnd = $byteStart + strlen($uri);
            return [
                "index" => [
                    "byteStart" => $byteStart,
                    "byteEnd" => $byteEnd,
                ],
                "features" => [
                    [
                        '$type' => "app.bsky.richtext.facet#link",
                        "uri" => $uri
                    ]
                ]
            ];
        }
        return [];
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
        $links = $this->parseLinks($text);
        if ($links != []) {
            $params["record"]["facets"] = [$links];
        }
        return $this->bsky->post("https://bsky.social/xrpc/com.atproto.repo.createRecord", $params);
    }
}