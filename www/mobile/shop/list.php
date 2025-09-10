<?php
include_once('./_common.php');

// 테마에 list.php 있으면 include
if(defined('G5_THEME_MSHOP_PATH')) {
		$theme_list_file = G5_THEME_MSHOP_PATH.'/list.php';
		if(is_file($theme_list_file)) {
				include_once($theme_list_file);
				return;
		}
		unset($theme_list_file);
}

// 상품 리스트에서 다른 필드로 정렬을 하려면 아래의 배열 코드에서 해당 필드를 추가하세요.
if( isset($sort) && ! in_array($sort, array('it_name', 'it_sum_qty', 'it_price', 'it_use_avg', 'it_use_cnt', 'it_update_time')) ){
		$sort='';
}

$sql = " select *
					 from {$g5['g5_shop_category_table']}
					where ca_id = '$ca_id'
						and ca_use = '1'  ";
$ca = sql_fetch($sql);
if (!$ca['ca_id'])
		alert('등록된 분류가 없습니다.', G5_SHOP_URL);

// 테마미리보기 스킨 등의 변수 재설정
if(defined('_THEME_PREVIEW_') && _THEME_PREVIEW_ === true) {
		$ca['ca_mobile_skin']       = (isset($tconfig['ca_mobile_skin']) && $tconfig['ca_mobile_skin']) ? $tconfig['ca_mobile_skin'] : $ca['ca_mobile_skin'];
		$ca['ca_mobile_img_width']  = (isset($tconfig['ca_mobile_img_width']) && $tconfig['ca_mobile_img_width']) ? $tconfig['ca_mobile_img_width'] : $ca['ca_mobile_img_width'];
		$ca['ca_mobile_img_height'] = (isset($tconfig['ca_mobile_img_height']) && $tconfig['ca_mobile_img_height']) ? $tconfig['ca_mobile_img_height'] : $ca['ca_mobile_img_height'];
		$ca['ca_mobile_list_mod']   = (isset($tconfig['ca_mobile_list_mod']) && $tconfig['ca_mobile_list_mod']) ? $tconfig['ca_mobile_list_mod'] : $ca['ca_mobile_list_mod'];
		$ca['ca_mobile_list_row']   = (isset($tconfig['ca_mobile_list_row']) && $tconfig['ca_mobile_list_row']) ? $tconfig['ca_mobile_list_row'] : $ca['ca_mobile_list_row'];
}

// 본인인증, 성인인증체크
if(!$is_admin) {
		$msg = shop_member_cert_check($ca_id, 'list');
		if($msg)
				alert($msg, G5_SHOP_URL);
}

$g5['title'] = $ca['ca_name'];

include_once(G5_MSHOP_PATH.'/_head.php');

// 스킨경로
$skin_dir = G5_MSHOP_SKIN_PATH;

if($ca['ca_mobile_skin_dir']) {
		if(preg_match('#^theme/(.+)$#', $ca['ca_mobile_skin_dir'], $match))
				$skin_dir = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/shop/'.$match[1];
		else
				$skin_dir = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/shop/'.$ca['ca_mobile_skin_dir'];

		if(is_dir($skin_dir)) {
				$skin_file = $skin_dir.'/'.$ca['ca_mobile_skin'];

				if(!is_file($skin_file))
						$skin_dir = G5_MSHOP_SKIN_PATH;
		} else {
				$skin_dir = G5_MSHOP_SKIN_PATH;
		}
}

define('G5_SHOP_CSS_URL', str_replace(G5_PATH, G5_URL, $skin_dir));
?>

<script>
var g5_shop_url = "<?php echo G5_SHOP_URL; ?>";
</script>
<script src="<?php echo G5_JS_URL; ?>/shop.mobile.list.js"></script>

<div class="_schMenu">
	<form action="#" class="sch">
		<input type="text" placeholder="상품명을 입력해 주세요">
		<button type="submit"><i class="ri-search-line"></i></button>
	</form>
	<div class="menu ty1">
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-link1.png" alt=""></div>
			<div class="txt">한식</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-link2.png" alt=""></div>
			<div class="txt">중식</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-link3.png" alt=""></div>
			<div class="txt">일식</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-link4.png" alt=""></div>
			<div class="txt">양식</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-link5.png" alt=""></div>
			<div class="txt">기타</div>
		</a>
	</div>
	<div class="menu ty2">
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-food1.png" alt=""></div>
			<div class="txt">찜</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-food2.png" alt=""></div>
			<div class="txt">탕</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-food3.png" alt=""></div>
			<div class="txt">볶음</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-food4.png" alt=""></div>
			<div class="txt">조림</div>
		</a>
	</div>
	<div class="menu ty3">
		<a href="#" class="active"><span>전체</span></a>
		<a href="#"><span>새우류</span></a>
		<a href="#"><span>연체류</span></a>
		<a href="#"><span>생선류</span></a>
		<a href="#"><span>게류</span></a>
		<a href="#"><span>가공수산</span></a>
		<a href="#"><span>어패류</span></a>
		<a href="#"><span>특수분위</span></a>
		<a href="#"><span>초밥류</span></a>
		<a href="#"><span>기타냉동</span></a>
		<a href="#"><span>공산품</span></a>
	</div>
</div>




<div id="sct">

	<div class="_shopListHead">
		<div class="all">상품수 : 132개</div>
		<div class="sort">
			<button type="button" onclick="$(this).closest('.sort').toggleClass('active')"><i class="ri-equalizer-2-line"></i></button>
			<div>
				<a href="#">낮은가격순</a>
				<a href="#">높은가격순</a>
				<a href="#">상품명순</a>
			</div>
		</div>
	</div>

	<div class="_shopList">

		<div class="_item2">
			<div class="img">
				<a href="/shop/item.php?it_id=1756864603">
					<img src="https://placehold.co/340x340" alt="">
				</a>
			</div>
			<div class="info">
				<div class="nation">
					<span>말레이시아</span>
					<span>베트남</span>
				</div>
				<div class="subject"><a href="/shop/item.php?it_id=1756864603">블랙타이거새우 (BT)</a></div>
				<div class="sum">
					<dl>
						<dt>가공형태</dt>
						<dd>라운드, IQF /</dd>
					</dl>
					<dl>
						<dt>규격</dt>
						<dd>500g * 10ea / 5kg</dd>
					</dl>
					<dl>
						<dt>단위</dt>
						<dd>BOX</dd>
					</dl>
				</div>
				<div class="price">
					<strong>250,000</strong><em>원</em>
				</div>
				<div class="sum">
					<dl>
						<dt>조회수</dt>
						<dd>2561</dd>
					</dl>
				</div>
			</div>
		</div>

		<div class="_item2">
			<div class="img">
				<a href="/shop/item.php?it_id=1756864603">
					<img src="https://placehold.co/340x340" alt="">
				</a>
			</div>
			<div class="info">
				<div class="nation">
					<span>말레이시아</span>
					<span>베트남</span>
				</div>
				<div class="subject"><a href="/shop/item.php?it_id=1756864603">블랙타이거새우 (BT)</a></div>
				<div class="sum">
					<dl>
						<dt>가공형태</dt>
						<dd>라운드, IQF /</dd>
					</dl>
					<dl>
						<dt>규격</dt>
						<dd>500g * 10ea / 5kg</dd>
					</dl>
					<dl>
						<dt>단위</dt>
						<dd>BOX</dd>
					</dl>
				</div>
				<div class="price">
					<strong>250,000</strong><em>원</em>
				</div>
				<div class="sum">
					<dl>
						<dt>조회수</dt>
						<dd>2561</dd>
					</dl>
				</div>
			</div>
		</div>

		<div class="_item2">
			<div class="img">
				<a href="/shop/item.php?it_id=1756864603">
					<img src="https://placehold.co/340x340" alt="">
				</a>
			</div>
			<div class="info">
				<div class="nation">
					<span>말레이시아</span>
					<span>베트남</span>
				</div>
				<div class="subject"><a href="/shop/item.php?it_id=1756864603">블랙타이거새우 (BT)</a></div>
				<div class="sum">
					<dl>
						<dt>가공형태</dt>
						<dd>라운드, IQF /</dd>
					</dl>
					<dl>
						<dt>규격</dt>
						<dd>500g * 10ea / 5kg</dd>
					</dl>
					<dl>
						<dt>단위</dt>
						<dd>BOX</dd>
					</dl>
				</div>
				<div class="price">
					<strong>250,000</strong><em>원</em>
				</div>
				<div class="sum">
					<dl>
						<dt>조회수</dt>
						<dd>2561</dd>
					</dl>
				</div>
			</div>
		</div>

	</div>




		<?php
		// 상단 HTML
		echo '<div id="sct_hhtml">'.conv_content($ca['ca_mobile_head_html'], 1).'</div>';

		$cate_skin = $skin_dir.'/listcategory.skin.php';
		if(!is_file($cate_skin))
				$cate_skin = G5_MSHOP_SKIN_PATH.'/listcategory.skin.php';
		include $cate_skin;

		// 테마미리보기 베스트상품 재설정
		if(defined('_THEME_PREVIEW_') && _THEME_PREVIEW_ === true) {
				if(isset($theme_config['ca_mobile_list_best_mod']))
						$theme_config['ca_mobile_list_best_mod'] = (isset($tconfig['ca_mobile_list_best_mod']) && $tconfig['ca_mobile_list_best_mod']) ? $tconfig['ca_mobile_list_best_mod'] : 0;
				if(isset($theme_config['ca_mobile_list_best_row']))
						$theme_config['ca_mobile_list_best_row'] = (isset($tconfig['ca_mobile_list_best_row']) && $tconfig['ca_mobile_list_best_row']) ? $tconfig['ca_mobile_list_best_row'] : 0;
		}

		// 분류 Best Item
		$list_mod = (isset($theme_config['ca_mobile_list_best_mod']) && $theme_config['ca_mobile_list_best_mod']) ? (int)$theme_config['ca_mobile_list_best_mod'] : $ca['ca_mobile_list_mod'];
		$list_row = (isset($theme_config['ca_mobile_list_best_row']) && $theme_config['ca_mobile_list_best_row']) ? (int)$theme_config['ca_mobile_list_best_row'] : $ca['ca_mobile_list_row'];
		$limit = $list_mod * $list_row;
		$best_skin = G5_MSHOP_SKIN_PATH.'/list.best.10.skin.php';

		$sql = " select *
								from {$g5['g5_shop_item_table']}
								where ( ca_id like '$ca_id%' or ca_id2 like '$ca_id%' or ca_id3 like '$ca_id%' )
									and it_use = '1'
									and it_type4 = '1'
								order by it_order, it_id desc
								limit 0, $limit ";

		$list = new item_list($best_skin, $list_mod, $list_row, $ca['ca_mobile_img_width'], $ca['ca_mobile_img_height']);
		$list->set_query($sql);
		$list->set_mobile(true);
		$list->set_view('it_img', true);
		$list->set_view('it_id', false);
		$list->set_view('it_name', true);
		$list->set_view('it_price', true);
		echo $list->run();

		// 상품 출력순서가 있다면
		if ($sort != "")
				$order_by = $sort.' '.$sortodr.' , it_order, it_id desc';
		else
				$order_by = 'it_order, it_id desc';

		$error = '<p class="sct_noitem">등록된 상품이 없습니다.</p>';

		// 리스트 스킨
		$skin_file = is_include_path_check($skin_dir.'/'.$ca['ca_mobile_skin']) ? $skin_dir.'/'.$ca['ca_mobile_skin'] : $skin_dir.'/list.10.skin.php';

		if (file_exists($skin_file)) {

				echo '<div id="sct_sortlst">';

				$sort_skin = $skin_dir.'/list.sort.skin.php';
				if(!is_file($sort_skin))
						$sort_skin = G5_MSHOP_SKIN_PATH.'/list.sort.skin.php';
				include $sort_skin;

						// 상품 보기 타입 변경 버튼
				$sub_skin = $skin_dir.'/list.sub.skin.php';
				if(!is_file($sub_skin))
						$sub_skin = G5_MSHOP_SKIN_PATH.'/list.sub.skin.php';

				if(is_file($sub_skin)){
						include $sub_skin;
				}

				echo '</div>';

				// 총몇개
				$items = $ca['ca_mobile_list_mod'] * $ca['ca_mobile_list_row'];
				// 페이지가 없으면 첫 페이지 (1 페이지)
				if ($page < 1) $page = 1;
				// 시작 레코드 구함
				$from_record = ($page - 1) * $items;

				$list = new item_list($skin_file, $ca['ca_mobile_list_mod'], $ca['ca_mobile_list_row'], $ca['ca_mobile_img_width'], $ca['ca_mobile_img_height']);
				$list->set_category($ca['ca_id'], 1);
				$list->set_category($ca['ca_id'], 2);
				$list->set_category($ca['ca_id'], 3);
				$list->set_is_page(true);
				$list->set_mobile(true);
				$list->set_order_by($order_by);
				$list->set_from_record($from_record);
				$list->set_view('it_img', true);
				$list->set_view('it_id', false);
				$list->set_view('it_name', true);
				$list->set_view('it_price', true);
				$list->set_view('sns', true);
				$list->set_view('it_icon', true);
				echo $list->run();

				// where 된 전체 상품수
				$total_count = $list->total_count;
		}
		else
		{
				echo '<div class="sct_nofile">'.str_replace(G5_PATH.'/', '', $skin_file).' 파일을 찾을 수 없습니다.<br>관리자에게 알려주시면 감사하겠습니다.</div>';
		}
		?>

		<?php
		$qstr1 = '';
		if($total_count > $items) {
				$qstr1 .= 'ca_id='.$ca_id;
				$qstr1 .='&sort='.$sort.'&sortodr='.$sortodr;
				$ajax_url = G5_SHOP_URL.'/ajax.list.php?'.$qstr1.'&use_sns=1';
		?>
		<div class="li_more">
				<p id="item_load_msg"><img src="<?php echo G5_SHOP_CSS_URL; ?>/img/loading.gif" alt="로딩이미지" ><br>잠시만 기다려주세요.</p>
				<div class="li_more_btn">
						<button type="button" id="btn_more_item" data-url="<?php echo $ajax_url; ?>" data-page="<?php echo $page; ?>">더보기 +</button>
				</div>
		</div>
		<?php } ?>

		<?php
		// 하단 HTML
		echo '<div id="sct_thtml">'.conv_content($ca['ca_mobile_tail_html'], 1).'</div>';
		?>
</div>

<?php
include_once(G5_MSHOP_PATH.'/_tail.php');

echo "\n<!-- {$ca['ca_mobile_skin']} -->\n";