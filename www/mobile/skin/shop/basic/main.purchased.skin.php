<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가



// g5_shop_cart mb_id
// ct_status 완료
?>
<!-- 상품진열 10 시작 { -->
<?php
// print_r($member);

$showItemAry = array();
$sql = " select * from {$g5['g5_shop_cart_table']} WHERE mb_id = '{$member['mb_id']}' AND ct_status = '완료' GROUP BY it_id ORDER BY ct_time DESC ";
$sql = " select * from {$g5['g5_shop_cart_table']} WHERE mb_id = '{$member['mb_id']}' ORDER BY ct_time DESC ";

$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $showItemAry[$row['it_id']] = array("mb_id"=>$row['mb_id'],"ct_time"=>$row['ct_time'] );
}

if(is_array($showItemAry) && count($showItemAry) > 0 ) {
    foreach($showItemAry as $it_id => $data) {

        $sql = " select * from {$g5['g5_shop_item_table']} where it_id = '$it_id' limit 1 ";
        $row = sql_fetch($sql);
        echo "<div class=\"_item\">";
            echo "<div class=\"img\">";
                echo "<a href=\"".shop_item_url($row['it_id']) ."\">";
                    echo get_it_image($row['it_id'], 340, 340, '', '', stripslashes($row['it_name']))."\n";
                echo "</a>";
            echo "</div>";
            echo "<div class=\"info\">";
                echo "<div class=\"subject\">";
                echo "<a href=\"".shop_item_url($row['it_id']) ."\" class=\"sct_txt\">\n";
                echo stripslashes($row['it_name'])."\n";
                echo "</a>\n";
                echo"</div>";
                echo "<div class=\"price\">";
                    echo "<strong>".display_price(get_price($row), $row['it_tel_inq'])."</strong>";
                    echo "<em>원</em>";
                echo "</div>";
            echo "</div>";
        echo "</div>";

    }
}else{
    echo "<div class=\"_item\">";
    echo "<p class=\"sct_noitem\"> 구매한 상품이 없습니다.</p>\n";
    echo "</div>";
}