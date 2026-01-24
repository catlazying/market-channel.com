<?php
include_once('./_common.php');

$type = isset($_REQUEST['type']) ? (int) preg_replace("/[^0-9]/", "", $_REQUEST['type']) : 1;
if ($type === 1)      $g5['title'] = '히트상품';
else if ($type === 2) $g5['title'] = 'MD 추천상품';
else if ($type === 3) $g5['title'] = '신규상품';
else if ($type === 4) $g5['title'] = '인기상품';
else if ($type === 5) $g5['title'] = '특가상품';
else
    alert('상품유형이 아닙니다.');

include_once(G5_MSHOP_PATH.'/_head.php');

// 한페이지에 출력하는 이미지수 = $list_mod * $list_row
// 모바일에서는 계산된 이미지수가 중요함
$list_mod   = $default['de_mobile_listtype_list_mod'];   // 한줄에 이미지 몇개씩 출력? 단, 모바일환경에서는 사용되지 않음.
$list_row   = $default['de_mobile_listtype_list_row'];   // 한 페이지에 몇라인씩 출력?

$img_width  = $default['de_mobile_listtype_img_width'];  // 출력이미지 폭
$img_height = $default['de_mobile_listtype_img_height']; // 출력이미지 높이
?>

<script>
var g5_shop_url = "<?php echo G5_SHOP_URL; ?>";
</script>
<script src="<?php echo G5_JS_URL; ?>/shop.mobile.list.js"></script>

<div class="_schMenu">
    <?php include_once(G5_MSHOP_SKIN_PATH.'/list.search.skin.php'); ?>

	<? if(strlen($ca['ca_id'])=='2'){ ?>
		<div class="menu ty1" >
			<?php // 업태별
				$categories = show_shop_category_array($ca['ca_id'],'4');
				if( $categories ){
					foreach($categories as $category){
						echo '<a href="'.$category['url'].'">';
						echo '	<div class="img"><img src="'.G5_URL.'/images'.$category['ca_1'].'" alt=""></div>';
						echo '	<div class="txt">'.$category['ca_name'].'</div>';
						echo '</a>';
					}
				}
			?>
		</div>
	<? } ?>
	<? if(strlen($ca['ca_id'])>='4'){ ?>
		<div class="menu ty3">
			<a href="/shop/list-<?=substr($ca['ca_id'],0,4)?>" class="<?=(substr($ca['ca_id'],0,4)==$ca['ca_id'])?"active":""?>"><span>전체</span></a>
			<?php // 업태별 strlen($ca['ca_id'])
				$categories = show_shop_category_array(substr($ca['ca_id'],0,4),'6');
				if( $categories ){
					foreach($categories as $category){
						echo '<a href="'.$category['url'].'" class="'.($ca['ca_id']==$category['ca_id']?"active":"").'">';
						echo '	<span>'.$category['ca_name'].'</span>';
						echo '</a>';
					}
				}
			?>
		</div>
	<? } ?>
</div>
<?
	if(G5_LIST_OLDCATE_YN == 'Y') {
		$cate_skin = $skin_dir.'/listcategory.skin.php';
		if(!is_file($cate_skin))
				$cate_skin = G5_MSHOP_SKIN_PATH.'/listcategory.skin.php';
		include $cate_skin;
	}
		// 상단 HTML
		echo '<div id="sct_hhtml">'.conv_content($ca['ca_mobile_head_html'], 1).'</div>';

			echo '<div id="sct_sortlst">';
				echo '<div class="_shopListHead">';
					echo '<div class="all">상품수 : <span class="total_count">0</span>개</div>';
					echo '<div class="sort">';
					// 	$sort_skin = $skin_dir.'/list.sort.skin.php';
					// 	if(!is_file($sort_skin))
					// 			$sort_skin = G5_MSHOP_SKIN_PATH.'/list.sort.skin.php';
					// 	include $sort_skin;

					// if(G5_LIST_SORT_YN=='Y') {
					// 	// 상품 보기 타입 변경 버튼
					// 	$sub_skin = $skin_dir.'/list.sub.skin.php';
					// 	if(!is_file($sub_skin))
					// 			$sub_skin = G5_MSHOP_SKIN_PATH.'/list.sub.skin.php';

					// 	if(is_file($sub_skin)){
					// 			include $sub_skin;
					// 	}
					// }
					echo '</div>';
				echo '</div>';
			echo '</div>';

?>
<div id="listtype">
<?php
// 상품 출력순서가 있다면
$order_by = ' it_order, it_id desc ';
if ($sort != '')
    $order_by = $sort.' '.$sortodr.' , it_order, it_id desc';
else
    $order_by = 'it_order, it_id desc';

if (!$skin)
    $skin = $default['de_mobile_listtype_list_skin'];
else
    $skin = preg_replace('#\.+[\\\/]#', '', strip_tags($skin));

define('G5_SHOP_CSS_URL', G5_MSHOP_SKIN_URL);

// 리스트 유형별로 출력
$list_file = G5_MSHOP_SKIN_PATH.'/'.$skin;
if (file_exists($list_file)) {
    // 총몇개 = 한줄에 몇개 * 몇줄
    $items = $list_mod * $list_row;
    // 페이지가 없으면 첫 페이지 (1 페이지)
    if ($page < 1) $page = 1;
    // 시작 레코드 구함
    $from_record = ($page - 1) * $items;

    $list = new item_list();
    $list->set_type($type);
    $list->set_list_skin($list_file);
    $list->set_list_mod($list_mod);
    $list->set_list_row($list_row);
    $list->set_img_size($img_width, $img_height);
    $list->set_is_page(true);
    $list->set_mobile(true);
    $list->set_order_by($order_by);
    $list->set_from_record($from_record);
    echo $list->run();

    // where 된 전체 상품수
    $total_count = $list->total_count;
    // 전체 페이지 계산
    $total_page  = ceil($total_count / $items);
}
else
{
    echo '<div align="center">'.get_text($skin).' 파일을 찾을 수 없습니다.<br>관리자에게 알려주시면 감사하겠습니다.</div>';
}
?>
</div>
<?php
$qstr .= '&amp;type='.$type.'&amp;sort='.$sort;
echo get_paging($config['cf_mobile_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");
?>
<script>
	jQuery(function($){
		var total_count = <?php echo number_format($total_count); ?>;
		$('.total_count').text(total_count);
	});
</script>
<?php
include_once(G5_MSHOP_PATH.'/_tail.php');