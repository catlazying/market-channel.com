<?
include($_SERVER["DOCUMENT_ROOT"].'/common.php');
include($_SERVER["DOCUMENT_ROOT"].'/adm/notification/pushSendFunc.php');




// $_fcm_device = trim($_POST['device']);
// $_deviceToken = trim($_POST['pushToken']);
// $_title = trim($_POST['title']);
// $_contents = trim($_POST['contents']);
// $_goUrl = trim($_POST['goUrl']);
// $_imgUrl = trim($_POST['imgUrl']);

$num = 3;
$sql = "select * from fcm_notification_cron where idx>='$num'";

$qry = sql_query($sql);
while($val = sql_fetch_array($qry)) {

  $_fcm_device = trim($val['fcm_device']);
  $_deviceToken = trim($val['user_token']);
  $_title = trim($val['fcm_title']);
  $_contents = trim($val['fcm_message']);
  $_goUrl = trim($val['fcm_link']);
  $_imgUrl = trim($val['fcm_image']);


  echo "<pre>";
  echo "--------------------------------------";
  echo "Access Token   : {$accessToken}\n";
  echo "Device Token   : {$_deviceToken}\n";
  echo "Title          : {$_title}\n";
  echo "Contents       : {$_contents}\n";
  echo "Image URL      : {$_imgUrl}\n";
  echo "Go URL         : {$_goUrl}\n";
  echo "FCM Device     : {$_fcm_device}\n";
  echo "--------------------------------------";
  echo "</pre>";

  if(!empty($_title)){
    // Bearer Token 발급
    $accessToken = getGoogleOAuthToken();

    list($httpCode, $response) = sendFCMMessage($accessToken, $_deviceToken, $_title, $_contents, $_imgUrl, $_goUrl,$_fcm_device);

    echo "<pre>";
    echo "Response Code: $httpCode\n";
    echo "Response: $response\n";
  }
}
/*
결과값을 받으면
fcm_notification_tokens 기준

  `user_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '사용 여부',
  `last_push_at` datetime DEFAULT NULL COMMENT '마지막 푸시 수신 시간',

  업데이트


  http://market-channel.com/adm/notification/pushSendApi.php
*/
//print json_encode(array("code" => $httpCode, "msg" => $response));

/*
안드로이드
https://team-platform.tistory.com/27

ios
https://seonghooony.tistory.com/65
https://ios-development.tistory.com/1282


https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages?hl=ko&_gl=1*1irscp8*_up*MQ..*_ga*MTQzNzE1MTkwNi4xNzQzMzk4NTA3*_ga_CW55HF8NVT*MTc0MzM5ODUwNi4xLjAuMTc0MzM5ODUzOC4wLjAuMA..
*/