<?php
namespace Duukkis\Bsky;

use Duukkis\Bsky\Entities\FeedEntity;

class Bsky
{
    private ?string $did = null;
    private ?string $accessJwt = null;
    private ?string $refreshJwt = null;
    private ?string $email = null;

    public function __construct(
        private string $username,
        private string $password
    )
    {
        if (!$this->loadSession($this->username)) {
            $this->login();
        }
    }

    public function getDid(): ?string
    {
        return $this->did;
    }

    public function getAccessJwt(): ?string
    {
        return $this->accessJwt;
    }

    public function getRefreshJwt(): ?string
    {
        return $this->refreshJwt;
    }

    public function storeSession(array $session): void
    {
        $fileName = "./sessions/" . $session["handle"] . ".store";
        print (serialize($session) . PHP_EOL);
        file_put_contents($fileName, serialize($session));
        $this->did = $session["did"];
        $this->accessJwt = $session["accessJwt"];
        $this->refreshJwt = $session["refreshJwt"];
        $this->email = $session["email"];
    }

    public function loadSession(string $handle): bool
    {
        $fileName = "./sessions/" . $handle . ".store";
        if (!file_exists($fileName)) {
            return false;
        }
        $session = unserialize(file_get_contents($fileName));
        $this->did = $session["did"];
        $this->accessJwt = $session["accessJwt"];
        $this->refreshJwt = $session["refreshJwt"];
        $this->email = $session["email"];
        return true;
    }

    public function login(): void
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bsky.social/xrpc/com.atproto.server.createSession',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"identifier":"'.$this->username.'", "password":"'.$this->password.'"}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $session = json_decode($response,TRUE);
        $this->storeSession($session);
    }

    public function refreshToken(string $uid, string $refreshToken)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bsky.social/xrpc/com.atproto.server.refreshSession',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS =>'{"uid":"'.$uid.'"}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $refreshToken
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $session = json_decode($response,TRUE);
        $this->storeSession($session);
    }

    public function feed(): FeedEntity
    {
        return new FeedEntity($this);
    }

    public function get(string $url, array $params): mixed
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // https://bsky.social/xrpc/app.bsky.feed.getAuthorFeed
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->getAccessJwt()
            ),
        ));
        $response = curl_exec($curl);
        return json_decode($response);
    }

    public function getFeed()
    {

//        if response.status_code == 401:  # Unauthorized
/*        curl_close($curl);
        print_r($response);
        die();
        $homeFeed = $this->client->getTimeline()->feed;

        foreach ($homeFeed as $item) {
            echo "{$item->post->author->displayName} (@{$item->post->author->handle}) says:\n\n";
            echo "{$item->post->record->text}\n\n";
            if (isset($item->post->record->reply)) {
                echo "in reply to {$item->post->record->reply->parent->uri}\n\n";
            }
            echo str_repeat('-', 72);
            echo "\n\n";
        }*/
    }
}


