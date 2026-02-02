<?php

// JWT를 위한 Base64Url 인코딩 도우미 함수
function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}
function getGoogleOAuthToken() {
    // 1. 서비스 계정 JSON 파일 경로
    $serviceAccountPath = $_SERVER['DOCUMENT_ROOT'].'/adm/notification/market-channel-de339-firebase-adminsdk-fbsvc-41af331df3.json';

    if (!file_exists($serviceAccountPath)) {
        error_log("FCM Error: Service Account file not found.");
        return null;
    }

    $jsonKey = json_decode(file_get_contents($serviceAccountPath), true);
    $tokenUrl = "https://oauth2.googleapis.com/token";



    // 2. JWT 헤더 및 페이로드 구성
    $header = base64UrlEncode(json_encode(["alg" => "RS256", "typ" => "JWT"]));
    $iat = time();
    $exp = $iat + 3600;
    $claimSet = base64UrlEncode(json_encode([
        "iss" => $jsonKey["client_email"],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => $tokenUrl,
        "exp" => $exp,
        "iat" => $iat
    ]));

    // 3. 서명 생성 (RSA SHA-256)
    $data = "$header.$claimSet";
    $privateKey = openssl_pkey_get_private($jsonKey["private_key"]);

    if (!$privateKey) {
        error_log("FCM Error: Invalid private key.");
        return null;
    }

    openssl_sign($data, $signature, $privateKey, "sha256WithRSAEncryption");
    $signedJWT = "$data." . base64UrlEncode($signature);

    // 4. OAuth 2.0 토큰 요청
    $postData = http_build_query([
        "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
        "assertion" => $signedJWT
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        error_log("FCM Curl Error: " . curl_error($ch));
        return null;
    }
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result["access_token"])) {
        return $result["access_token"];
    } else {
        error_log("FCM Auth Error: " . ($result["error_description"] ?? "Unknown error"));
        return null;
    }
}

function sendFCMMessage($accessToken, $deviceToken, $title, $body, $imageUrl, $linkUrl,$_fcm_device) {
    $projectId = 'market-channel-de339';  //  Firebase 프로젝트 ID
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    if ($_fcm_device=='ios') {
        $data = [
            "message" => [
                "token" => $deviceToken,
                "data" => [
                    "title" => $title,
                    "body" => $body,
                    "image" => $imageUrl,
                    "targetUrl" => $linkUrl
                ],
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                    "image" => $imageUrl
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => "10", // 즉시 전송 (중요!)
                        "apns-push-type" => "alert" // 알림 형태 명시
                    ],
                    "payload" => [
                        "aps" => [
                            "sound" => "default", // 푸시 올 때 소리 재생
                            "badge" => 1,         // 앱 아이콘에 숫자 1 표시
                            "content-available" => 1, // 백그라운드 업데이트 허용
                            "mutable-content" => 1 // 이미지 Extension이 작동함
                            // "category" => "NEW_MESSAGE_CATEGORY"
                        ]
                    ]
                ]
            ]
        ];
    }else{
        $data = [
            "message" => [
                "token" => $deviceToken,
                "data" => [
                    "title" => $title,
                    "body" => $body,
                    "image" => $imageUrl,
                    "link" => $linkUrl
                ],
                // "notification" => [
                //     "title" => $title,
                //     "body" => $body,
                //     "image" => $imageUrl
                // ],
            ]
        ];

    }
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    // print_r($headers);
    // echo "<pre>";
    // print_r($data);
    if(!empty($data)){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$httpCode, $response];
    }
}
/*
    $data = [
        "message" => [
            "token" => $deviceToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
                "image" => $imageUrl
            ],
            "data" => [
                "title" => $title,
                "body" => $body,
                "image" => $imageUrl,
                "link" => $linkUrl
            ],
            "android" => [
                "priority" => "high",
                "notification" => [
                    "sound" => "default",
                    "click_action" => "OPEN_ACTIVITY"
                ]
            ],
            "apns" => [
                "payload" => [
                    "aps" => [
                        "sound" => "default",
                        "category" => "NEW_MESSAGE_CATEGORY"
                    ]
                ]
            ]
        ]
    ];
*/