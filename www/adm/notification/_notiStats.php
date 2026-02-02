<?

$notificationCronTable = "fcm_notification_cron";
$apiKey = valueChk($postData['apiKey']);
$title = valueChk($postData["title"]);
$sdate = valueChk($postData["sdate"]);

if($apiKey != $fixApiKey ){
   $msg = "잘못된 API 값입니다.";
   print json_encode(array("code" => $code, "msg" => $msg));
   exit;
}

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
*/
