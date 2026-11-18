<?php
include_once( $_SERVER['DOCUMENT_ROOT'].'/common.php');
    header("Pragma: no-cache");
	ini_set('display_errors', '1');
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// https://market-channel.com/plugin/apple/appleProc.php
// function logSave($saveTxt){
//     $openFile = $_SERVER['DOCUMENT_ROOT']."/data/tmp/".date("Ymd").".log";
//     $fp = fopen($openFile, "a+");
//     fwrite($fp, $saveTxt."\n");
//     fclose($fp);
// }
/// 로그저장
logSave('\nstart\n');
logSave(json_encode($_REQUEST));

require_once $_SERVER['DOCUMENT_ROOT'].'/plugin/php-jwt/src/JWT.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/plugin/php-jwt/src/JWK.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/plugin/php-jwt/src/ExpiredException.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/plugin/php-jwt/src/SignatureInvalidException.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/plugin/php-jwt/src/BeforeValidException.php';

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

// 2. Apple identityToken (프론트에서 전달받음)
$identityToken = isset($_POST['id_token']) ? $_POST['id_token'] : '';

if (!$identityToken) {
    exit('identityToken not found');
}

// 3. Apple 공개키(JWK) 가져오기
$appleKeyUrl = 'https://appleid.apple.com/auth/keys';

$ch = curl_init($appleKeyUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    exit('Failed to fetch Apple public keys');
}

$appleKeys = json_decode($response, true);

// 4. JWT 디코딩
try {
    $decoded = JWT::decode(
        $identityToken,
        JWK::parseKeySet($appleKeys),
        array('RS256')
    );
} catch (Exception $e) {
    exit('JWT decode error: ' . $e->getMessage());
}
/// 로그저장
logSave(json_encode($decoded));

// 5. 필수 값 검증
$clientId = 'marketchannel.login'; // 🔴 본인 Service ID / Bundle ID

if ($decoded->iss !== 'https://appleid.apple.com') {
    exit('Invalid issuer');
}

if ($decoded->aud !== $clientId) {
    exit('Invalid audience');
}

if ($decoded->exp < time()) {
    exit('Token expired');
}

// 6. 사용자 정보
$appleUserId = $decoded->sub;          // 유니크 ID (DB 기준값)
$email       = isset($decoded->email) ? $decoded->email : null;
$isPrivate   = isset($decoded->is_private_email) ? $decoded->is_private_email : null;

// 7. 결과 출력 (테스트용)

// appleProc.php

if (isset($_POST['user'])) {

    $user = $_POST['user'];
    $user = stripcslashes($user);
    $user = json_decode($user,true);

    if (isset($user['name'])) {
        $firstName = $user['name']['firstName'] ?? '';
        $lastName  = $user['name']['lastName'] ?? '';
        $fullName  = trim($lastName . $firstName);
    }

    if (isset($user['email'])) {
        $email = $user['email'];
    }
     // INSERT
    $sql = " INSERT INTO g5_member_apple set ap_apple = '{$appleUserId}', ap_name = '{$fullName}', ap_email = '{$email}', ap_datetime = '".G5_TIME_YMDHIS."' ";
    sql_query($sql);

}else{
    // UPDATE
    // $sql = " SELECT ap_email from g5_member_apple where ap_apple = '{$appleUserId}' ";
    // $row = sql_fetch($sql);
}
/// 로그저장
logSave($sql);

$sql = " SELECT * from {$g5['member_table']} where mb_email = '{$email}' ";
/// 로그저장
logSave($sql);


$row = sql_fetch($sql);
if(!empty($row['mb_id'])){
    // 로그인
    // 애플 로그인 세션 설정 후 로그인 처리
    $_POST['apple_login'] = 1;
    $_POST['mb_id'] = $row['mb_id'];
    $_POST['mb_password'] = $row['mb_password']; // 실사용시에는 보안을 고려, 가상 패스워드/토큰등을 사용

    // /bbs/login_check.php로 POST 요청 (내부 인클루드)
    include_once(G5_BBS_PATH.'/login_check.php');
    exit;
}else{
    //회원가입
    $_SESSION['name'] = $fullName;
    $_SESSION['email'] = $email;
    echo "<script> location.replace('/bbs/register.php'); </script>";
}


# end of document

