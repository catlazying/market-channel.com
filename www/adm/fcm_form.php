<?php
$sub_menu = "900800";
include_once("./_common.php");

$page_size = 20;
$colspan = 8;

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = "푸시발송 ";

if ($w == 'u' && is_numeric($num)) {
    $write = sql_fetch("select * from fcm_notification_cron where idx='$num'");
    $g5['title'] .= '수정';
}
else  {
    $write['idx'] = $num;
    $g5['title'] .= '추가';
}

include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="book_form" method="post" action="fcm_form_update.php">
<input type="hidden" name="w" value="<?php echo $w?>">
<input type="hidden" name="page" value="<?php echo $page?>">
<input type="hidden" name="idx" value="<?php echo $write['idx']; ?>">

    <div class="tbl_frm01 tbl_wrap">

        <table>
        <caption><?php echo $g5['title'];?> 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="user_id">회원아이디</label></th>
            <td><input type="text" name="user_id" id="user_id" value="<?php echo $write['user_id']; ?>" class="frm_input"  size="70"></td>
        </tr>
        <tr>
            <th scope="row"><label for="user_token">회원토큰<strong class="sound_only"> 필수</strong></label></th>
            <td><input type="text" name="user_token" id="user_token" required value="<?php echo get_sanitize_input($write['user_token']); ?>" class="frm_input required" style="width:100%;"></td>
        </tr>

        <tr>
            <th scope="row"><label for="fcm_title">메시지 제목<strong class="sound_only"> 필수</strong></label></th>
            <td><input type="text" name="fcm_title" id="fcm_title" required value="<?php echo get_sanitize_input($write['fcm_title']); ?>" class="frm_input required" style="width:100%;"></td>
        </tr>
        <tr>
            <th scope="row">메세지 내용</th>
            <td id="sms5_emo_add">
                <div class="sms5_box write_wrap">
                    <textarea name="fcm_message" id="fcm_message" class="box_txt box_square" accesskey="m"><?php echo html_purifier($write['fcm_message']); ?></textarea>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="fcm_link">메시지 링크</label></th>
            <td><input type="text" name="fcm_link" id="fcm_link" value="<?php echo $write['fcm_link']; ?>" class="frm_input" style="width:100%;"></td>
        </tr>
        <tr>
            <th scope="row"><label for="fcm_image">이미지</label></th>
            <td><input type="text" name="fcm_image" id="fcm_image" value="<?php echo $write['fcm_image']; ?>" class="frm_input" style="width:100%;"></td>
        </tr>

        <tr>
            <th scope="row"><label for="reservationTime">예약발송일시</label></th>
            <td>
                <input type="radio" name="send_type" id="send_type_now" value="직접" <?php echo (empty($write['reservationTime']) ? 'checked' : ''); ?>> <label for="send_type_now">즉시발송</label>
                <input type="radio" name="send_type" id="send_type_reserve" value="예약" <?php echo (!empty($write['reservationTime']) ? 'checked' : ''); ?>> <label for="send_type_reserve">예약발송</label>
                <input type="text" name="reservationTime" id="reservationTime" value="<?php
                    if (!empty($write['reservationTime'])) {
                        echo $write['reservationTime'];
                    } else {
                        echo ($w == 'u') ? '' : date("Y-m-d H:i");
                    }
                ?>" class="frm_input" size="70" autocomplete="off" readonly>
            </td>
        </tr>

        <?php if ($w == 'u') {?>
        <tr>
            <th scope="row">업데이트</th>
            <td> <?php echo $write['mdatetime']?> </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="확인" class="btn_submit" accesskey="s">
        <a href="./fcm_list.php?<?php echo clean_query_string($_SERVER['QUERY_STRING']); ?>">목록</a>
    </div>
</form>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
<script>
$(function() {
    function toggleReservationTime() {
        var isReserve = $('#send_type_reserve').is(':checked');
        $('#reservationTime').prop('disabled', !isReserve);
    }
    $('#send_type_now, #send_type_reserve').on('change', toggleReservationTime);
    // 페이지 로드 시 초기상태 반영
    toggleReservationTime();
    $("#reservationTime").datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: "HH:mm",
        showSecond: false,
        controlType: 'select',  // slider
        minDate: 0,
        timeText: '시간',
        hourText: '시',
        minuteText: '분',
        secondText: '초'
    });
});
</script>
<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
