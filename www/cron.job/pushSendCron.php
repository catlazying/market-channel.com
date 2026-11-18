#!/usr/local/php73/bin/php
<?php
    ob_start();   // 🔥 출력 버퍼 시작

	$_SERVER['DOCUMENT_ROOT'] = "/home/hosting_users/seaever/www";
	$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
	$_SERVER['HTTP_USER_AGENT'] = "shell";
	$_SERVER['SERVER_NAME'] = "market-channel.com";
	$_SERVER['HTTPS'] = "on";

    $_POST['fcm_sender'] = "cron";

    include($_SERVER["DOCUMENT_ROOT"].'/common.php');
    include($_SERVER["DOCUMENT_ROOT"].'/adm/notification/pushSendFunc.php');

    // logSave("start-".date("Y-m-d H:i:s"),'pushSendCron');
    // 발송 데이타
    $sql = "select * from fcm_notification_cron WHERE send_date is null AND viewYn='Y' AND ecnt <1 ";
    // $sql = "select * from fcm_notification_cron WHERE viewYn='Y' AND ecnt <1  and user_id ='6741600175'";
    // echo $sql;
    $qry = sql_query($sql);
    while($val = sql_fetch_array($qry)) {
        // echo "<pre>";
        // print_r($val);
        logSave("\n".date("Y-m-d H:i:s")." ------- ",'pushSendCron');
        logSave(json_encode($val),'pushSendCron');

        $_fcm_device = trim(isset($val['fcm_device']) ? (string)$val['fcm_device'] : '');
        $_deviceToken = trim(isset($val['user_token']) ? (string)$val['user_token'] : '');
        $_title = trim(isset($val['fcm_title']) ? (string)$val['fcm_title'] : '');
        $_contents = trim(isset($val['fcm_message']) ? (string)$val['fcm_message'] : '');
        $_goUrl = trim(isset($val['fcm_link']) ? (string)$val['fcm_link'] : '');
        $_imgUrl = trim(isset($val['fcm_image']) ? (string)$val['fcm_image'] : '');

        if(!empty($_title)){
          // Bearer Token 발급
            $accessToken = getGoogleOAuthToken();
            list($httpCode, $response) = sendFCMMessage($accessToken, $_deviceToken, $_title, $_contents, $_imgUrl, $_goUrl,$_fcm_device);
        if ($httpCode == "200"){
            $send_date_val = ",send_date='".date("Y-m-d H:i:s")."'";
        }else{
            $send_date_val = "";
        }
            $response = str_replace(array("\n","\r"),"", isset($response) ? (string)$response : '');
            // logSave("\n결과".date("Y-m-d H:i:s")." ------- ",'pushSendCron');
            // logSave($response,'pushSendCron');
            $addParam = " mdatetime=NOW(),ecnt=ecnt+1, fcm_result='".$response."'".$send_date_val;
            $sql = " update fcm_notification_cron set ".$addParam." where idx=".$val['idx'];
            // echo $sql;
            sql_query($sql);
        }
    }
/*
    /usr/local/php73/bin/php /home/hosting_users/seaever/www/cron.job/pushSendCron.php
*/
$html = ob_get_contents(); // 또는 ob_get_clean()
ob_end_clean();            // 버퍼 비우고 출력 안 함

if(!empty($html)){
    logSave("end-".date("Y-m-d H:i:s"),'pushSendCron');
    logSave($html,'pushSendCron');
}
#end of file

echo "end";