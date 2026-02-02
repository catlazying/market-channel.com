<?php
$sub_menu = "900820";
include_once("./_common.php");

$page_size = 20;
$colspan = 8;

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = "푸시 회원목록";

if ($page < 1) $page = 1;

$sql_search = "";

if ($st && trim($sv))
    $sql_search = " and $st like '%$sv%' ";
else
    $sql_search = "";
$sql = "select count(*) as cnt from fcm_notification_tokens where idx > 0 $sql_search";

$total_res = sql_fetch($sql);
$total_count = $total_res['cnt'];

$total_page = (int)($total_count/$page_size) + ($total_count%$page_size==0 ? 0 : 1);
$page_start = $page_size * ( $page - 1 );

$vnum = $total_count - (($page-1) * $page_size);
$line = 0;
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="search_form" id="search_form" action=<?php echo $_SERVER['SCRIPT_NAME'];?> class="local_sch01 local_sch" method="get">
<label for="st" class="sound_only">검색대상</label>
    <select name="st" id="st">
        <option value="user_id">아이디</option>
        <option value="push_token">푸시토큰</option>
        <option value="last_push_at">마지막전송일</option>
    </select>
<label for="sv" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="sv" value="<?php echo $sv ?>" id="sv" required class="required frm_input" placeholder="아이디검색">
<input type="submit" value="검색" class="btn_submit">
</form>

<div class="btn_fixed_top" >
</div>
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">번호</th>
        <th scope="col">회원아이디</th>
        <th scope="col">기기정보</th>
        <th scope="col">앱버전</th>
        <th scope="col">활성화</th>
        <th scope="col">마지막전송일</th>
        <th scope="col">등록일</th>
        <th scope="col" style="width: 80px;">삭제</th>
     </tr>
     </thead>
     <tbody>
    <?php if (!$total_count) { ?>
    <tr>
        <td colspan="<?php echo $colspan?>" class="empty_table" >
            데이터가 없습니다.
        </td>
    </tr>
    <?php
    }
    $qry = sql_query("select * from fcm_notification_tokens where idx > 0 $sql_search order by idx desc limit $page_start, $page_size");
    while($res = sql_fetch_array($qry)) {
        $bg = 'bg'.($line++%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_numsmall" rowspan="2"><?php echo $vnum--?></td>
        <td class="td_left"><?php echo $res['user_id']?></td>
        <td class="td_left"><?php echo $res['platform']?> / <?php echo $res['device_id']?></td>
        <td class="td_left"><?php echo $res['app_version']?></td>
        <td><?php echo $res['is_active']?></td>
        <td class="td_datetime"><?php echo date('Y-m-d h:i', strtotime($res['last_push_at']))?></td>
        <td class="td_datetime"><?php echo date('Y-m-d', strtotime($res['created_at']))?></td>
        <td rowspan="2">
            <a href="./fcm_push_update.php?w=d&amp;co_id=<?php echo $res['idx']; ?>" onclick="return delete_confirm(this);" class="btn btn_02">삭제</a>
        </td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <td class="td_left" colspan="<?php echo ($colspan-1)?>"><?php echo $res['push_token']?></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME']."?st=$st&amp;sv=$sv&amp;page="); ?>


<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
