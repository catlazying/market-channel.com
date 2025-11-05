<?php
include_once('./_common.php');

define("_INDEX_", TRUE);

include_once(G5_THEME_MSHOP_PATH.'/shop.head.php');
?>
	<div class="_vis">
		<div class="swiper-wrapper">
			<div class="vis swiper-slide"><a href="#"><img src="<?=G5_URL?>/images/vis1.png" alt=""></a></div>
			<div class="vis swiper-slide"><a href="#"><img src="https://placehold.co/860x360?text=2" alt=""></a></div>
			<div class="vis swiper-slide"><a href="#"><img src="https://placehold.co/860x360?text=3" alt=""></a></div>
			<div class="vis swiper-slide"><a href="#"><img src="https://placehold.co/860x360?text=3" alt=""></a></div>
			<div class="vis swiper-slide"><a href="#"><img src="https://placehold.co/860x360?text=3" alt=""></a></div>
			<div class="vis swiper-slide"><a href="#"><img src="https://placehold.co/860x360?text=3" alt=""></a></div>
			<div class="vis swiper-slide"><a href="#"><img src="https://placehold.co/860x360?text=3" alt=""></a></div>
		</div>
		<div class="page"></div>
	</div>
	<?php echo display_banner('메인비주얼', 'mainbanner.10.skin.php'); ?>

	<div class="_mainLink">
		<?php // 업태별
			$categories = show_shop_category_array(30,'4');
			if( $categories ){
				foreach($categories as $category){
					echo '<a href="'.$category['url'].'">';
					echo '	<div class="img"><img src="'.G5_URL.'/images'.$category['ca_1'].'" alt=""></div>';
					echo '	<div class="txt">'.$category['ca_name'].'</div>';
					echo '</a>';
				}
			}
		?>

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

	<div class="_hotHead">
		<h3>최근 구매 많은 인기상품</h3>
	</div>

	<div class="_hotWrap">
		<div class="roll">
			<div class="swiper-wrapper">
				<?php
				$mobile_main_skin_file = G5_MSHOP_SKIN_PATH . '/main.10.skin.php';
				$mobile_main_list_mod = 4;
				$mobile_main_img_width = 340;
				$mobile_main_img_height = 340;
				$sql = " select * from  {$g5['g5_shop_item_table']} where it_use='1' AND it_soldout!='1' AND it_type4 = '1' order by rand(), it_sum_qty desc  ";
				$list = new item_list($mobile_main_skin_file, $mobile_main_list_mod, 0, $mobile_main_img_width, $mobile_main_img_height);
				$list->set_query($sql);
				echo $list->run();
				?>
				<?/*<!--
				<div class="_item">
					<div class="img">
						<a href="#">
							<img src="https://placehold.co/340x340" alt="">
							<span class="mark">국산</span>
						</a>
					</div>
					<div class="info">
						<div class="subject"><a href="#">최상급 급냉 숫꽃게5kg 최상급 급냉 숫꽃게5kg</a></div>
						<div class="price">
							<strong>10,500</strong>
							<em>원</em>
						</div>
						<div class="per up">
							<i class="ri-triangle-fill"></i>
							<span>25%</span>
						</div>
					</div>
				</div>
				<div class="_item">
					<div class="img">
						<a href="#">
							<img src="https://placehold.co/340x340" alt="">
							<span class="mark">국산</span>
						</a>
					</div>
					<div class="info">
						<div class="subject"><a href="#">최상급 급냉 숫꽃게5kg 최상급 급냉 숫꽃게5kg</a></div>
						<div class="price">
							<strong>10,500</strong>
							<em>원</em>
						</div>
						<div class="per down">
							<i class="ri-triangle-fill"></i>
							<span>25%</span>
						</div>
					</div>
				</div>
				 -->*/?>

			</div>
		</div>
		<div class="page">
		</div>
	</div>

	<hr class="_line">

	<div class="_menuLink">
		<?php // 상품별
			$categories = "";
			$categories = show_shop_category_array(10,'4');
			if( $categories ){
				foreach($categories as $category){
					echo '<a href="'.$category['url'].'">';
					echo '	<div class="img"><img src="'.G5_URL.'/images'.$category['ca_1'].'" alt=""></div>';
					echo '	<div class="txt">'.$category['ca_name'].'</div>';
					echo '</a>';
				}
			}
		?>


		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link1.svg" alt=""></div>
			<div class="txt">연체류</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link2.svg" alt=""></div>
			<div class="txt">새우류</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link3.svg" alt=""></div>
			<div class="txt">갑각류</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link4.svg" alt=""></div>
			<div class="txt">조개류</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link5.svg" alt=""></div>
			<div class="txt">생선류</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link6.svg" alt=""></div>
			<div class="txt">가공제품</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link7.svg" alt=""></div>
			<div class="txt">튀김류</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link8.svg" alt=""></div>
			<div class="txt">냉동식품</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link9.svg" alt=""></div>
			<div class="txt">특수상품</div>
		</a>
		<a href="#">
			<div class="img"><img src="<?=G5_URL?>/images/ico-menu-link10.svg" alt=""></div>
			<div class="txt">기타</div>
		</a>
	</div>

	<div class="_ban">
		<?php echo display_banner('메인중간배너', 'banner.10.skin.php'); ?>
	</div>

	<div class="_defaultLayout">
		<div class="_topArea">
			<div class="lef">
				<h3>마켓채널에서 주목하는 신상!</h3>
			</div>
			<div class="rig">

				<a href="<?php echo shop_type_url('3'); ?>" class="more"><span>전체보기</span></a>
			</div>
		</div>

		<div class="_ovaList">
			<?php
			$mobile_main_skin_file = G5_MSHOP_SKIN_PATH . '/main.10.skin.php';
			$mobile_main_list_mod = 4;
			$mobile_main_img_width = 340;
			$mobile_main_img_height = 340;
			$sql = " select * from  {$g5['g5_shop_item_table']} where it_use='1' AND it_type3 = '1' order by rand() ";
			$list = new item_list($mobile_main_skin_file, $mobile_main_list_mod, 0, $mobile_main_img_width, $mobile_main_img_height);
			$list->set_query($sql);
			echo $list->run();
			?>
		</div>
	</div>

	<hr class="_line">

	<div class="_defaultLayout">
		<div class="_topArea">
			<div class="lef">
				<h3>내가 구매한 상품</h3>
			</div>
			<div class="rig">
				<a href="#" class="more"><span>전체보기</span></a>
			</div>
		</div>

		<div class="_buyList">

			<div class="_item">
				<div class="img">
					<a href="#">
						<img src="https://placehold.co/340x340" alt="">
					</a>
				</div>
				<div class="info">
					<div class="subject"><a href="#">최상급 급냉 숫꽃게5kg 최상급 급냉 숫꽃게5kg</a></div>
					<div class="price">
						<strong>10,500</strong>
						<em>원</em>
					</div>
					<div class="wish">
						<label class="_wish"><input type="checkbox"><i class="ri-star-line"></i><strong>4.2</strong><em>(15)</em></label>
					</div>
					<div class="move">
						<span>무료배송</span>
					</div>
				</div>
			</div>

			<div class="_item">
				<div class="img">
					<a href="#">
						<img src="https://placehold.co/340x340" alt="">
					</a>
				</div>
				<div class="info">
					<div class="subject"><a href="#">최상급 급냉 숫꽃게5kg 최상급 급냉 숫꽃게5kg</a></div>
					<div class="price">
						<strong>10,500</strong>
						<em>원</em>
					</div>
					<div class="wish">
						<label class="_wish"><input type="checkbox" checked><i class="ri-star-line"></i><strong>4.2</strong><em>(15)</em></label>
					</div>
					<div class="move">
						<span>무료배송</span>
					</div>
				</div>
			</div>

			<div class="_item">
				<div class="img">
					<a href="#">
						<img src="https://placehold.co/340x340" alt="">
					</a>
				</div>
				<div class="info">
					<div class="subject"><a href="#">최상급 급냉 숫꽃게5kg 최상급 급냉 숫꽃게5kg</a></div>
					<div class="price">
						<strong>10,500</strong>
						<em>원</em>
					</div>
					<div class="wish">
						<label class="_wish"><input type="checkbox"><i class="ri-star-line"></i><strong>4.2</strong><em>(15)</em></label>
					</div>
					<div class="move">
						<span>무료배송</span>
					</div>
				</div>
			</div>

		</div>
	</div>


<?php
include_once(G5_THEME_MSHOP_PATH.'/shop.tail.php');