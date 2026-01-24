<?php
require_once './_common.php';

$notificationSaveTable = "fcm_notification_tokens";

// 저장, 업데이트, 삭제 처리

// POST 변수 세팅
$action = (isset($_POST['action'])) ? $_POST['action'] : '';
$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$device_id = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';
$push_token = isset($_POST['push_token']) ? trim($_POST['push_token']) : '';
$platform = isset($_POST['platform']) ? trim($_POST['platform']) : '';
$app_version = isset($_POST['app_version']) ? trim($_POST['app_version']) : '';
$is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;

// 저장
if ($action === 'create') {
    if ($user_id && $device_id && $push_token && $platform) {
        // 기존 토큰(디바이스) 존재 체크 후 있으면 업데이트, 없으면 insert
        $row = sql_fetch("SELECT idx FROM {$notificationSaveTable} WHERE device_id = '".sql_real_escape_string($device_id)."'");

        if ($row && $row['idx']) {
            // update
            $sql = "UPDATE {$notificationSaveTable}
                    SET
                        user_id = '".sql_real_escape_string($user_id)."',
                        push_token = '".sql_real_escape_string($push_token)."',
                        platform = '".sql_real_escape_string($platform)."',
                        app_version = '".sql_real_escape_string($app_version)."',
                        is_active = '{$is_active}',
                        updated_at = NOW()
                    WHERE device_id = '".sql_real_escape_string($device_id)."'";
            $result = sql_query($sql);
            $message = $result ? "업데이트 성공" : "업데이트 실패";
        } else {
            // insert
            $sql = "INSERT INTO {$notificationSaveTable}
                        (user_id, device_id, push_token, platform, app_version, is_active, created_at, updated_at)
                    VALUES
                        (
                            '".sql_real_escape_string($user_id)."',
                            '".sql_real_escape_string($device_id)."',
                            '".sql_real_escape_string($push_token)."',
                            '".sql_real_escape_string($platform)."',
                            '".sql_real_escape_string($app_version)."',
                            '{$is_active}',
                            NOW(),
                            NOW()
                        )";
            $result = sql_query($sql);
            $message = $result ? "저장 성공" : "저장 실패";
        }
    } else {
        $message = "필수값 누락";
    }
    echo json_encode(array('result' => isset($result) && $result, 'message'=> $message));
    exit;
}

// 삭제
if ($action === 'delete') {
    $idx = isset($_POST['idx']) ? intval($_POST['idx']) : 0;
    if ($idx) {
        $sql = "DELETE FROM {$notificationSaveTable} WHERE idx = '{$idx}'";
        $result = sql_query($sql);
        $message = $result ? "삭제 성공" : "삭제 실패";
    } else {
        $message = "idx 누락";
    }
    echo json_encode(array('result'=> isset($result) && $result, 'message'=> $message));
    exit;
}

