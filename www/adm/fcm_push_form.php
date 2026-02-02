<?php
$sub_menu = "900800";
include_once("./_common.php");

$page_size = 20;
$colspan = 8;

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = "푸시발송 ";

if ($w == 'u' && is_numeric($num)) {
    $write = sql_fetch("select * from fcm_notification_set where idx='$num'");
    $g5['title'] .= '수정';
}
else  {
    $write['idx'] = $num;
    $g5['title'] .= '추가';
}

include_once(G5_ADMIN_PATH.'/admin.head.php');

?>

<form name="book_form" method="post" action="fcm_push_form_update.php">
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
            <th scope="row"><label for="title">제목</label></th>
            <td><input type="text" name="title" id="title" value="<?php echo $write['title']; ?>" class="frm_input"  size="70"></td>
        </tr>
        <tr>
            <th scope="row"><label for="send_type">발송구분</label></th>
            <td>
                <select name="send_type" id="send_type" class="frm_input" style="width: 200px;">
                    <option value="">발송구분 선택</option>
                    <option value="회원가입" <?php echo ($write['send_type']=='회원가입') ? 'selected' : ''; ?>>회원가입</option>
                    <option value="주문완료" <?php echo ($write['send_type']=='주문완료') ? 'selected' : ''; ?>>주문완료</option>
                    <option value="입금확인" <?php echo ($write['send_type']=='입금확인') ? 'selected' : ''; ?>>입금확인</option>
                    <option value="배송완료" <?php echo ($write['send_type']=='배송완료') ? 'selected' : ''; ?>>배송완료</option>
                </select>
            </td>
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
            <th scope="row"><label for="send_type">사용</label></th>
            <td>
                <select name="viewYn" id="viewYn" class="frm_input" style="width: 200px;">
                    <option value="Y" <?php echo ($write['viewYn']=='Y') ? 'selected' : ''; ?>>Yes</option>
                    <option value="N" <?php echo ($write['viewYn']=='N') ? 'selected' : ''; ?>>No</option>
                </select>
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

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
