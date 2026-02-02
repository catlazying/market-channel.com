<?


$apiKey = valueChk($postData['apiKey']);
$title = valueChk($postData["title"]);
$sdate = valueChk($postData["sdate"]);

if($apiKey != $fixApiKey ){
   $msg = "잘못된 API 값입니다.";
   print json_encode(array("code" => $code, "msg" => $msg));
   exit;
}
// $title = "페텔x웨거스 SNS 체함단 오픈!";
// $sdate = "2025-05-31";

if (!empty($title) && !empty($sdate) ){
    $addParam = " fcm_device, CASE WHEN send_date = 'N' THEN 'N' ELSE 'Y' END AS send_status, COUNT(*) AS cnt ";
    $addWhere = "fcm_title = '".$title."' AND rdate ='".$sdate."' AND viewYn='Y' ";
    $addOrder = "GROUP BY fcm_device, send_status ORDER BY fcm_device, send_status";
    $selRow = procDb("ARRAY", $notificationCronTable,$addParam,$addWhere,$addOrder);
    $result = json_encode($selRow);
}
print $result;
/*
푸시발송 통계 api 입니다.
http://topmanager.co.kr/API/notiStats.php
apiKey : a8ac50f8115aa34f419d56a61fdd8d50
title : 페텔x웨거스 SNS 체함단 오픈!
sdate : 2025-05-31
이렇게 3개 보내주세요

결과는

[{"fcm_device":"android","send_status":"N","cnt":"1561"},{"fcm_device":"android","send_status":"Y","cnt":"596"},{"fcm_device":"ios","send_status":"N","cnt":"7778"},{"fcm_device":"ios","send_status":"Y","cnt":"1206"}]
이렇게 보냅니다.
$notificationTable = "fcm_notification_list";
$notificationCronTable = "fcm_notification_cron";
*/