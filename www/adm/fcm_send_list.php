<?php
$sub_menu = "900810";
include_once("./_common.php");

$page_size = 20;
$colspan = 6;

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = "푸시 발송내역";

if ($page < 1) $page = 1;

$sql_search = "";

if ($st && trim($sv))
    $sql_search = " and $st like '%$sv%' ";
else
    $sql_search = "";
$sql = "select count(*) as cnt from fcm_notification_set where idx > 0 $sql_search";

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
<input type="hidden" name="st" id="st" value="fcm_title" >
<label for="sv" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="sv" value="<?php echo $sv ?>" id="sv" required class="required frm_input" placeholder="제목검색">
<input type="submit" value="검색" class="btn_submit">
</form>

<div class="btn_fixed_top" >
    <a href="./fcm_push_form.php?page=<?php echo $page?>" class="btn btn_01">푸시 설정 추가</a>
</div>
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">번호</th>
        <th scope="col">구분제목</th>
        <th scope="col">발송구분</th>
        <th scope="col">발송대상</th>
        <th scope="col">발송제목</th>
        <th scope="col">등록일</th>
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
    $qry = sql_query("select * from fcm_notification_set where idx > 0 $sql_search order by idx desc limit $page_start, $page_size");
    while($res = sql_fetch_array($qry)) {
        $bg = 'bg'.($line++%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_numsmall"><?php echo $vnum--?></td>
        <td class="td_left">
            <a href="javascript:void(0);"
               onclick="fcmSendView('<?php echo $res['idx'];?>');"
               style="text-decoration:underline; color:blue; cursor:pointer;">
                <?php echo htmlspecialchars($res['title']); ?>
            </a>
        </td>
        <td class="td_left"><?php echo $res['send_type']?></td>
        <td class="td_left"><?php echo $res['send_target']?></td>
        <td class="td_left"><?php echo $res['fcm_title']?></td>
        <td class="td_datetime"><?php echo date('Y-m-d', strtotime($res['rdatetime']))?></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME']."?st=$st&amp;sv=$sv&amp;page="); ?>


<script>
    function fcmSendView(num){
        location.href = "./fcm_push_form.php?w=u&num="+num;
    }
</script>
<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');