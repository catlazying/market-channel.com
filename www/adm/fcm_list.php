<?php
$sub_menu = "900800";
include_once("./_common.php");

$page_size = 20;
$colspan = 8;

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = "푸시 발송내역";

if ($page < 1) $page = 1;

$sql_search = "";

if ($st && trim($sv))
    $sql_search = " and $st like '%$sv%' ";
else
    $sql_search = "";
$sql = "select count(*) as cnt from fcm_notification_cron where idx > 0 $sql_search";

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
    <a href="./fcm_form.php?page=<?php echo $page?>" class="btn btn_01">푸시 추가</a>
</div>
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">번호</th>
        <th scope="col">발송구분</th>
        <th scope="col">제목</th>
        <th scope="col">내용</th>
        <th scope="col">발송일시</th>
        <th scope="col">결과</th>
        <th scope="col">예약일시</th>
        <th scope="col">아이디</th>
        <th scope="col">등록(수정)일시</th>
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
    $qry = sql_query("select * from fcm_notification_cron where idx > 0 $sql_search order by idx desc limit $page_start, $page_size");
    while($res = sql_fetch_array($qry)) {
        $bg = 'bg'.($line++%2);
        // $tmp_wr_memo = @unserialize($res['wr_memo']);
        // $dupli_count = (isset($tmp_wr_memo['total']) && $tmp_wr_memo['total']) ? (int) $tmp_wr_memo['total'] : 0;

        $fcm_message = $res['fcm_message'];

    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_numsmall"><?php echo $vnum--?></td>
        <td class="td_left"><?php echo $res['send_type']?></td>
        <td class="td_left">
            <a href="javascript:void(0);"
               onclick="fcmSendView('<?php echo $res['idx'];?>');"
               style="text-decoration:underline; color:blue; cursor:pointer;">
                <?php echo htmlspecialchars($res['fcm_title']); ?>
            </a>
        </td>
        <td class="td_left">
            <a href="javascript:void(0);"
               onclick="showFcmMessage('<?php echo $res['idx'];?>');"
               style="text-decoration:underline; color:blue; cursor:pointer;">
                내용보기
            </a>
            <div id="fcm_message_box_<?php echo $res['idx'];?>" class="fcm-message-box" style="display:none; position:absolute; z-index:9999; background:#fff; border:1px solid #888; padding:12px; box-shadow:0px 4px 14px rgba(0,0,0,0.08); min-width:200px; max-width:400px; word-break:break-all;">
                <div style="font-weight:bold; margin-bottom:8px;">메시지 내용</div>
                <div><?php echo nl2br(htmlspecialchars($fcm_message)); ?></div>
                <div style="text-align:right; margin-top:8px;">
                    <button onclick="hideFcmMessage('<?php echo $res['idx'];?>');event.stopPropagation();" type="button" style="font-size:12px;">닫기</button>
                </div>
            </div>
        </td>
        <td class="td_datetime">
            <?php echo !empty($res['send_date']) ? date('Y-m-d H:i', strtotime($res['send_date'])) : '-'; ?>
        </td>
        <td class="td_left"><?php echo $res['fcm_result']?></td>
        <td class="td_datetime">
            <?php echo !empty($res['reservationTime']) ? date('Y-m-d H:i', strtotime($res['reservationTime'])) : '-'; ?>
        </td>
        <td class="td_left"><?php echo $res['user_id']?></td>
        <td class="td_datetime"><?php echo date('Y-m-d H:i', strtotime($res['rdatetime']))?></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME']."?st=$st&amp;sv=$sv&amp;page="); ?>


<script>
    function fcmSendView(num){
        location.href = "./fcm_form.php?w=u&num="+num;
    }

    // fcm 메시지 레이어박스를 표시
    function showFcmMessage(idx){
        // 모든 메시지 박스 닫기
        document.querySelectorAll('.fcm-message-box').forEach(function(box){ box.style.display='none'; });
        var el = document.getElementById('fcm_message_box_'+idx);
        if(!el) return;
        el.style.display = 'block';

        // 화면 클릭시 박스 닫기 처리
        setTimeout(function(){
            document.addEventListener('mousedown', clickFcmMsgOutside);
        }, 10);

        function clickFcmMsgOutside(e){
            if (!el.contains(e.target)) {
                el.style.display='none';
                document.removeEventListener('mousedown', clickFcmMsgOutside);
            }
        }
    }
    // 닫기 버튼 함수
    function hideFcmMessage(idx){
        var el = document.getElementById('fcm_message_box_'+idx);
        if(el) el.style.display = 'none';
    }
</script>
<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');