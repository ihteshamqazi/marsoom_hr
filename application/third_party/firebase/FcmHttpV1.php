<?php
class FcmHttpV1 {
    private $credentialsPath;
    private $tokenUri = 'https://oauth2.googleapis.com/token';
    private $scope = 'https://www.googleapis.com/auth/firebase.messaging';

    public function __construct($jsonPath) {
        $this->credentialsPath = $jsonPath;
    }

    private function generateJwt() {
        $creds = json_decode(file_get_contents($this->credentialsPath), true);
        $header = ['alg'=>'RS256','typ'=>'JWT'];
        $now = time();
        $claims = [
            'iss'=>$creds['client_email'],
            'scope'=>$this->scope,
            'aud'=>$this->tokenUri,
            'iat'=>$now,
            'exp'=>$now + 3600,
        ];

        $segments = [
            $this->base64url(json_encode($header)),
            $this->base64url(json_encode($claims))
        ];
        $signature = '';
        openssl_sign(implode('.', $segments), $signature, $creds['private_key'], 'SHA256');
        $segments[] = $this->base64url($signature);

        return implode('.', $segments);
    }

    public function getAccessToken() {
        $jwt = $this->generateJwt();
        $response = json_decode($this->post($this->tokenUri, [
            'grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'=>$jwt
        ]), true);

        return $response['access_token'] ?? null;
    }

    public function sendMessage($projectId, $token, $title, $body) {
        $accessToken = $this->getAccessToken();
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $payload = [
            'message'=>[
                'token'=>$token,
                'notification'=>['title'=>$title,'body'=>$body]
            ]
        ];

        return $this->post($url, json_encode($payload), [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json; charset=utf-8'
        ]);
    }

    private function post($url, $data, $headers = []) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    private function base64url($data) {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($data));
    }
}
?>
