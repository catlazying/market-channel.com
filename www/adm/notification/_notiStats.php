<?


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


$homeDir = "/home/topmanager.co.kr/www";

$_SERVER['DOCUMENT_ROOT'] = $homeDir;
$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
$_SERVER['HTTP_USER_AGENT'] = "shell";
$_SERVER['SERVER_NAME'] = "topmanager.co.kr";
$_SERVER['HTTPS'] = "off";

$_POST['fcm_sender'] = "PETEL";
$diractMode="pushList";
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/notificationCronAjax.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/admin/notification/pushSendPetelFunc.php');
$resAry = json_decode($result,true);

// 발송 데이타
if (is_array($resAry)){
    foreach ($resAry as $k => $v){
        // echo $k ."=>". $v."<br>";
        $_fcm_device = trim($v['fcm_device']);
        $_deviceToken = trim($v['user_token']);
        $_title = trim($v['fcm_title']);
        $_contents = trim($v['fcm_message']);
        $_goUrl = trim($v['fcm_link']);
        $_imgUrl = trim($v['fcm_image']);
        $accessToken = getGoogleOAuthToken();
        // echo  $accessToken;
        // echo  "\n";
        // echo  $_deviceToken;
        // echo  "\n";
        // echo  $_title;

        list($httpCode, $response) = sendFCMMessage($accessToken, $_deviceToken, $_title, $_contents, $_imgUrl, $_goUrl,$_fcm_device);

        if ($httpCode == "200"){
            $send_date_val = ",send_date='".date("Y-m-d H:i:s")."'";
        }else{
            $send_date_val = "";
        }
        $response = str_replace(array("\n","\r"),"",$response);
        $addParam = " mdatetime=NOW(),ecnt=ecnt+1, fcm_result='".$response."'".$send_date_val;
        $addWhere = "idx=".$v['idx'];
        $selRow = procDb("UPDATE", $useTable,$addParam,$addWhere);
    }
}