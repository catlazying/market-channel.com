<?php
$sub_menu = "900800";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$notificationCronTable = "fcm_notification_cron";

if(!empty($_POST['idx'])){ $saveIdx = $_POST['idx']; }

if(!empty($_POST['fcm_device'])){ $fcm_device = $_POST['fcm_device']; }
if(!empty($_POST['fcm_sender'])){ $fcm_sender = $_POST['fcm_sender']; }
if(!empty($_POST['fcm_title'])){ $fcm_title = strip_tags(clean_xss_attributes($_POST['fcm_title'])); }
if(!empty($_POST['fcm_message'])){ $fcm_message = $_POST['fcm_message']; }
if(!empty($_POST['fcm_image'])){ $fcm_image = $_POST['fcm_image']; }
if(!empty($_POST['fcm_link'])){ $fcm_link = $_POST['fcm_link']; }
if(!empty($_POST['fcm_result'])){ $fcm_result = $_POST['fcm_result']; }
if(!empty($_POST['user_token'])){ $user_token = $_POST['user_token']; }
if(!empty($_POST['user_id'])){ $user_id = $_POST['user_id']; }
if(!empty($_POST['send_date'])){ $send_date = $_POST['send_date']; }
if(!empty($_POST['send_type'])){ $send_type = $_POST['send_type']; }
if(!empty($_POST['reservationTime'])){ $reservationTime = $_POST['reservationTime']; }
if(!empty($_POST['send_target'])){ $send_target = $_POST['send_target']; }

$addParamAry = array();
if(!empty($fcm_device )){ $addParamAry[] = " fcm_device ='$fcm_device' "; }
if(!empty($fcm_sender )){ $addParamAry[] = " fcm_sender ='$fcm_sender' "; }
if(!empty($fcm_title )){ $addParamAry[] = " fcm_title ='$fcm_title' "; }
if(!empty($fcm_message )){ $addParamAry[] = " fcm_message ='$fcm_message' "; }
if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
if(!empty($fcm_result )){ $addParamAry[] = " fcm_result ='$fcm_result' "; }
if(!empty($user_token )){ $addParamAry[] = " user_token ='$user_token' "; }
if(!empty($user_id )){ $addParamAry[] = " user_id ='$user_id' "; }
if(!empty($send_date )){ $addParamAry[] = " send_date ='$send_date' "; }
if(!empty($send_type )){ $addParamAry[] = " send_type ='$send_type' "; }
if(!empty($reservationTime )){ $addParamAry[] = " reservationTime ='$reservationTime' "; }


if ($w == '') {
    $addParams = ",".implode(",",$addParamAry);

    if($send_target=='ALL'){

        // 토큰 회원만큼 돌려
        $qry = sql_query("select * from fcm_notification_tokens where is_active =1 ");
        while($res = sql_fetch_array($qry)) {
            $user_id = $res['user_id'];
            $user_token = $res['push_token'];
            if(!empty($user_id) && !empty($user_token)){
                $sql = " insert {$notificationCronTable} set rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW(), viewYn='Y', user_id ='$user_id', user_token ='$user_token', ecnt=0 ".$addParams;
                sql_query($sql);
            }
        }
    }else{
        $sql = " insert {$notificationCronTable}
        set rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW(), viewYn='Y', ecnt=0 ".$addParams;
        sql_query($sql);
    }
    // $ma_id = sql_insert_id();

} elseif ($w == 'u') {
    $addParams = ",".implode(",",$addParamAry);
    $sql = " update {$notificationCronTable}
                set mdatetime=NOW() $addParams
                where idx = '{$saveIdx}' ";
    sql_query($sql);

} elseif ($w == 'd') {
    $delNumber = $_POST['number'];
    if(is_array($delNumber)){
        $addWhere = "idx in (".implode(",",$delNumber).")";
    }else{
        $addWhere = "idx = $delNumber ";
    }
    $sql = " update {$notificationCronTable} SET viewYn='N' where ".$addWhere;
    sql_query($sql);

}

goto_url("./fcm_send_list.php");

/*
안드로이드
https://team-platform.tistory.com/27

ios
https://seonghooony.tistory.com/65
https://ios-development.tistory.com/1282


https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages?hl=ko&_gl=1*1irscp8*_up*MQ..*_ga*MTQzNzE1MTkwNi4xNzQzMzk4NTA3*_ga_CW55HF8NVT*MTc0MzM5ODUwNi4xLjAuMTc0MzM5ODUzOC4wLjAuMA..
*/