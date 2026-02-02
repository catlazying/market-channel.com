<?php
$sub_menu = '900820';
require_once './_common.php';

if ($w == 'd') {
    auth_check_menu($auth, $sub_menu, "d");
} else {
    auth_check_menu($auth, $sub_menu, "w");
}

check_admin_token();

$co_id = isset($_REQUEST['co_id']) ? preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['co_id']) : '';

 if ($w == "d") {


    $sql = " delete from fcm_notification_tokens where idx = '$co_id' ";
    sql_query($sql);
}

    goto_url("./fcm_push_list.php");
