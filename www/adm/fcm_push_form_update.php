<?php
$sub_menu = "900810";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$notificationSetTable = "fcm_notification_set";

if(!empty($_POST['idx'])){ $saveIdx = $_POST['idx']; }

if(!empty($_POST['title'])){ $title = $_POST['title']; }
if(!empty($_POST['send_type'])){ $send_type = $_POST['send_type']; }
if(!empty($_POST['send_target'])){ $send_target = $_POST['send_target']; }
if(!empty($_POST['fcm_title'])){ $fcm_title = strip_tags(clean_xss_attributes($_POST['fcm_title'])); }
if(!empty($_POST['fcm_message'])){ $fcm_message = $_POST['fcm_message']; }
if(!empty($_POST['fcm_image'])){ $fcm_image = $_POST['fcm_image']; }
if(!empty($_POST['fcm_link'])){ $fcm_link = $_POST['fcm_link']; }
if(!empty($_POST['fcm_memo'])){ $fcm_memo = $_POST['fcm_memo']; }
if(!empty($_POST['viewYn'])){ $viewYn = $_POST['viewYn']; }

$addParamAry = array();
if(!empty($title )){ $addParamAry[] = " title ='$title' "; }
if(!empty($send_type )){ $addParamAry[] = " send_type ='$send_type' "; }
if(!empty($send_target )){ $addParamAry[] = " send_target ='$send_target' "; }
if(!empty($fcm_title )){ $addParamAry[] = " fcm_title ='$fcm_title' "; }
if(!empty($fcm_message )){ $addParamAry[] = " fcm_message ='$fcm_message' "; }
if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
if(!empty($fcm_memo )){ $addParamAry[] = " fcm_memo ='$fcm_memo' "; }
if(!empty($viewYn )){ $addParamAry[] = " viewYn ='$viewYn' "; }

if ($w == '') {
    $addParams = ",".implode(",",$addParamAry);
    $sql = " insert {$notificationSetTable}
             set rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW() ".$addParams;
    sql_query($sql);
    $ma_id = sql_insert_id();

} elseif ($w == 'u') {
    $addParams = ",".implode(",",$addParamAry);
    $sql = " update {$notificationSetTable}
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
    $sql = " update {$notificationSetTable} SET viewYn='N' where ".$addWhere;
    sql_query($sql);

}

goto_url("./fcm_list.php");
