<?php
namespace Duukkis\Bsky;

use Duukkis\Bsky\Entities\FeedEntity;
use Duukkis\Bsky\Entities\NotificationEntity;
use Duukkis\Bsky\Entities\RepoEntity;
use Exception;

class Bsky
{
    private ?string $did = null;
    private ?string $accessJwt = null;
    private ?string $refreshJwt = null;

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
        file_put_contents($fileName, serialize($session));
        $this->did = $session["did"];
        $this->accessJwt = $session["accessJwt"];
        $this->refreshJwt = $session["refreshJwt"];
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
        return true;
    }

    public function login(): void
    {
        $session = $this->post(
            'https://bsky.social/xrpc/com.atproto.server.createSession',
            ["identifier" => $this->username, "password" => $this->password],
            false,
            [],
            true,
        );
        print_r($session);die();
        $this->storeSession($session);

/*        $curl = curl_init();
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
        $this->storeSession($session);*/
    }

    public function refreshToken(string $refreshToken): void
    {
        $session = $this->post(
            'https://bsky.social/xrpc/com.atproto.server.refreshSession',
            [],
            false,
            ['Authorization: Bearer ' . $refreshToken],
            true,
        );
        print_r($session);die();
        $this->storeSession($session);
        /*
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bsky.social/xrpc/com.atproto.server.refreshSession',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $refreshToken
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $session = json_decode($response,TRUE);
        $this->storeSession($session);*/
    }

    private function getAuthHeader(): string
    {
        $accessJwt = $this->getAccessJwt();
        if ($accessJwt != null) {
            return 'Authorization: Bearer ' . $this->getAccessJwt();
        }
        return "";
    }

    public function post(
        string $url,
        array $fields,
        bool $auth = true,
        array $headers = [],
        bool $returnArray = false,
        int $retry = 0,
    ): mixed {
        $headers = array_merge(['Content-Type: application/json'], $headers);
        if ($auth) {
            $headers[] = $this->getAuthHeader();
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        if (!$returnArray) {
            $result = json_decode($response);
            if (isset($result->error) && $result->error == "ExpiredToken") {
                // first lets try refresh token
                if ($retry == 0) {
                    $this->refreshToken($this->getRefreshJwt());
                    return $this->post($url, $fields, $auth, $headers, $returnArray, 1);
                } else if ($retry == 1) { // refresh failed, lets try login
                    $this->login();
                    return $this->post($url, $fields, $auth, $headers, $returnArray, 2);
                } else { // refresh and login failed
                    throw new Exception("refresh and login failed");
                }
            }
            return $result;
        }
        return json_decode($response, $returnArray);
    }

    public function feed(): FeedEntity
    {
        return new FeedEntity($this);
    }

    public function repo(): RepoEntity
    {
        return new RepoEntity($this);
    }

    public function notification(): NotificationEntity
    {
        return new NotificationEntity($this);
    }

    public function get(string $url, array $params, int $retry = 0): mixed
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                $this->getAuthHeader(),
            ],
        ]);
        $response = curl_exec($curl);

        $result = json_decode($response);
        if (isset($result->error) && $result->error == "ExpiredToken") {
            // first lets try refresh token
            if ($retry == 0) {
                $this->refreshToken($this->getRefreshJwt());
                return $this->get($url, $params, 1);
            } else if ($retry == 1) { // refresh failed, lets try login
                $this->login();
                return $this->get($url, $params, 2);
            } else { // refresh and login failed
                throw new Exception("refresh and login failed");
            }
        }
        return $result;
    }
}
