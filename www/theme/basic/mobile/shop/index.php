<?php
include_once('./_common.php');

define("_INDEX_", TRUE);

include_once(G5_THEME_MSHOP_PATH.'/shop.head.php');
?>


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
	<?php if ($is_member) { ?>
	<hr class="_line">
	<div class="_defaultLayout">
		<div class="_topArea">
			<div class="lef">
				<h3>내가 구매한 상품</h3>
			</div>
			<div class="rig">
				<a href="/shop/orderinquiry.php" class="more"><span>전체보기</span></a>
			</div>
		</div>

		<div class="_buyList">
			<?php
			include_once(G5_MSHOP_SKIN_PATH.'/main.purchased.skin.php');
			?>
		</div>
	</div>
	<?php } ?>

<?php
include_once(G5_THEME_MSHOP_PATH.'/shop.tail.php');