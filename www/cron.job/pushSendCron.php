#!/usr/local/php73/bin/php
<?php

	$_SERVER['DOCUMENT_ROOT'] = "/home/hosting_users/seaever/www";
	$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
	$_SERVER['HTTP_USER_AGENT'] = "shell";
	$_SERVER['SERVER_NAME'] = "market-channel.com";
	$_SERVER['HTTPS'] = "on";

    $_POST['fcm_sender'] = "cron";

    include($_SERVER["DOCUMENT_ROOT"].'/common.php');
    include($_SERVER["DOCUMENT_ROOT"].'/adm/notification/pushSendFunc.php');

    logSave("start-".date("Y-m-d H:i:s"),'pushSendCron');
    // 발송 데이타
    $sql = "select * from fcm_notification_cron WHERE send_date is null AND viewYn='Y' AND ecnt <1 ";
    // $sql = "select * from fcm_notification_cron WHERE viewYn='Y' AND ecnt <1  and user_id ='6741600175'";
    // echo $sql;
    $qry = sql_query($sql);
    while($val = sql_fetch_array($qry)) {
        // echo "<pre>";
        // print_r($val);

        $_fcm_device = trim($val['fcm_device']);
        $_deviceToken = trim($val['user_token']);
        $_title = trim($val['fcm_title']);
        $_contents = trim($val['fcm_message']);
        $_goUrl = trim($val['fcm_link']);
        $_imgUrl = trim($val['fcm_image']);

        if(!empty($_title)){
          // Bearer Token 발급
            $accessToken = getGoogleOAuthToken();
            list($httpCode, $response) = sendFCMMessage($accessToken, $_deviceToken, $_title, $_contents, $_imgUrl, $_goUrl,$_fcm_device);

        if ($httpCode == "200"){
            $send_date_val = ",send_date='".date("Y-m-d H:i:s")."'";
        }else{
            $send_date_val = "";
        }
            $response = str_replace(array("\n","\r"),"",$response);
            $addParam = " mdatetime=NOW(),ecnt=ecnt+1, fcm_result='".$response."'".$send_date_val;
            $sql = " update fcm_notification_cron set ".$addParam." where idx=".$val['idx'];
            // echo $sql;
            sql_query($sql);
        }
    }
/*
    /usr/local/php73/bin/php /home/hosting_users/seaever/www/cron.job/pushSendCron.php
*/
logSave("end-".date("Y-m-d H:i:s"),'pushSendCron');
#end of file
