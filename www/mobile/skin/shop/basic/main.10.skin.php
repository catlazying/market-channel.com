<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_MSHOP_SKIN_URL.'/style.css">', 0);

// 장바구니 또는 위시리스트 ajax 스크립트
add_javascript('<script src="'.G5_JS_URL.'/shop.list.action.js"></script>', 10);
?>

<!-- 메인상품진열 10 시작 { -->
<?php
$li_width = intval(100 / $this->list_mod);
$li_width_style = ' style="width:'.$li_width.'%;"';
$i=0;

foreach((array) $list as $row){

    if( empty($row) ) continue;

    $item_link_href = shop_item_url($row['it_id']);
    $star_score = $row['it_use_avg'] ? (int) get_star($row['it_use_avg']) : '';
    $is_soldout = is_soldout($row['it_id'], true);   // 품절인지 체크

   if($i % $this->list_mod == 0)
        $li_clear = ' sct_clear';
    else
        $li_clear = '';


            echo "<div class=\"_item <?php echo $li_clear ?>\">\n";
			echo "	<div class=\"img\"> <a href=\"{$item_link_href}\"> \n";
            echo get_it_image($row['it_id'], $this->img_width, $this->img_height, '', '', stripslashes($row['it_name']))."\n";

            $origins = explode(',', $row['it_origin']);
            foreach ($origins as $origin) {
                echo "<span class=\"mark\">".$origin."</span>\n";
            }

            echo " </a> </div>\n";
			echo "	<div class=\"info\">\n";
			echo "		<div class=\"subject\"><a href=\"{$this->href}{$row['it_id']}\">".stripslashes($row['it_name'])."</a></div>\n";
			echo "		<div class=\"price\"> <strong>\n";
            echo display_price(get_price($row), $row['it_tel_inq'])."\n";
            echo "</strong> <em>원</em> </div>\n";
			echo "	</div>\n";
			echo "</div>\n";

    $i++;
}


if($i == 0) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열 10 끝 -->
