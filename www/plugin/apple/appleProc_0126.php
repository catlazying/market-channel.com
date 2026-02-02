<?php
// https://market-channel.com/plugin/apple/appleProc.php
logSave("start");
// logSave(json_encode($_POST));
/// 로그저장
$saveTxt = json_encode($_REQUEST);
logSave($saveTxt);
/*
composer require firebase/php-jwt

*/

// require_once 'vendor/autoload.php';
// use Firebase\JWT\JWT;
// use Firebase\JWT\JWK;

// 1. 설정 정보
$team_id = 'AAAK9TQ334';
$client_id = 'marketchannel.login'; // App ID가 아닌 Services ID
$key_id = '472X6DSM4T';
$key_file_path = './AuthKey_472X6DSM4T.p8'; // 보안 주의
$redirect_uri = 'https://market-channel.com/plugin/apple/appleProc.php';

// 애플에서 보낸 POST 데이터 수신
$auth_code = $_POST['code'] ?? null;
$user_info = $_POST['user'] ?? null; // 최초 1회 로그인 시에만 이름/이메일 포함됨


/// 로그저장
$saveTxt = json_encode($_POST);
logSave($saveTxt);


if (!$auth_code) {
    die("인증 코드가 없습니다.");
}

// 2. Client Secret (JWT) 생성 함수
function generateClientSecret($team_id, $client_id, $key_id, $key_path) {
    $private_key = file_get_contents($key_path);
    $payload = [
        'iss' => $team_id,
        'iat' => time(),
        'exp' => time() + 3600,
        'aud' => 'https://appleid.apple.com',
        'sub' => $client_id,
    ];
    return JWT::encode($payload, $private_key, 'ES256', $key_id);
}

// 3. Apple Token Endpoint에 검증 요청
$client_secret = generateClientSecret($team_id, $client_id, $key_id, $key_file_path);

$ch = curl_init('https://appleid.apple.com/auth/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'authorization_code',
    'code' => $auth_code,
    'redirect_uri' => $redirect_uri,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
]));

$response = json_decode(curl_exec($ch), true);

if (isset($response['error'])) {
    die("토큰 요청 실패: " . $response['error']);
}

// 4. id_token 디코딩하여 사용자 정보 추출
// id_token은 JWT 형태이며, 이를 디코딩하면 사용자의 유니크한 'sub'값을 알 수 있습니다.
$id_token = $response['id_token'];
$token_parts = explode('.', $id_token);
$user_data = json_decode(base64_decode($token_parts[1]), true);

/// 로그저장
$saveTxt = json_encode($user_data);
logSave($saveTxt);

// 결과 출력
$apple_user_id = $user_data['sub']; // 애플에서 부여한 사용자 고유 번호
$email = $user_data['email'] ?? '';


echo "사용자 고유 ID: " . $apple_user_id . "<br>";
echo "이메일: " . $email;

// 이후 DB 처리 (기존 회원인지 확인 후 로그인 세션 생성 등)


function logSave($saveTxt){
    $openFile = $_SERVER['DOCUMENT_ROOT']."/data/tmp/".date("Ymd").".log";
    $fp = fopen($openFile, "a+");
    fwrite($fp, $saveTxt."\n");
    fclose($fp);
}

