<?
include($_SERVER["DOCUMENT_ROOT"].'/common.php');
include($_SERVER["DOCUMENT_ROOT"].'/adm/notification/pushSendFunc.php');

$notificationSaveTable = "fcm_notification_tokens";

$headers = getallheaders();
$ua = isset($headers['user-agent']) ? $headers['user-agent'] : '';

$app_version = '';
$platform = '';
$device_id = '';

if (
    preg_match('/AppVersion\/([^\s]+)\s+Device\/([^\s]+)\s+Android\/([^\s]+)/i', $ua, $matches)
) {
    // 예시: "xxx AppVersion/1.0.2 Device/abcd1234 Android/13"
    $app_version = $matches[1];
    $device_id = $matches[2]." / ".$matches[3];
    $platform = "android";
}

// POST 변수 세팅
$action = (isset($_POST['action'])) ? $_POST['action'] : '';
$push_token = isset($_POST['token']) ? trim($_POST['token']) : '';
$is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;
$user_id = $_SESSION['ss_mb_id'];

// 저장
if ($action === 'create') {
    if ($user_id && $device_id && $push_token && $platform) {
        // 기존 토큰(디바이스) 존재 체크 후 있으면 업데이트, 없으면 insert
        $row = sql_fetch("SELECT idx FROM {$notificationSaveTable} WHERE  user_id = '".sql_real_escape_string($user_id)."' AND push_token = '".sql_real_escape_string($push_token)."' ");

        if ($row && $row['idx']) {
            // $sql = "UPDATE {$notificationSaveTable} SET user_id = '".sql_real_escape_string($user_id)."', push_token = '".sql_real_escape_string($push_token)."', platform = '".sql_real_escape_string($platform)."', app_version = '".sql_real_escape_string($app_version)."', is_active = '{$is_active}', updated_at = NOW() WHERE device_id = '".sql_real_escape_string($device_id)."'";
            // $result = sql_query($sql);
            // $message = $result ? "업데이트 성공" : "업데이트 실패";
        } else {
            $sql = "INSERT INTO {$notificationSaveTable} (user_id, device_id, push_token, platform, app_version, is_active, created_at, updated_at,last_push_at) VALUES ( '".sql_real_escape_string($user_id)."', '".sql_real_escape_string($device_id)."', '".sql_real_escape_string($push_token)."', '".sql_real_escape_string($platform)."', '".sql_real_escape_string($app_version)."', '{$is_active}', NOW(), NOW(), NOW() )"; $result = sql_query($sql);
            $message = $result ? "저장 성공" : "저장 실패";
        }
    } else {
        $message = "필수값 누락";
    }
    echo json_encode(array('result' => isset($result) && $result, 'message'=> $message));
    exit;
}

