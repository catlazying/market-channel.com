<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$q = isset($_GET['q']) ? clean_xss_tags($_GET['q'], 1, 1) : '';

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
?>


<div id="wrap" class="<?php if(!defined('_INDEX_')) {?>sub<?php }?>">

	<header class="_header">
		<?php if(defined('_INDEX_')) {?>
		<h1 class="logo"><a href="<?=G5_URL?>">MARKET CHANNEL</a></h1>
		<?php }else{?>
		<h1 class="title"><span><?=$g5_head_title2?></span></h1>
		<?php }?>
		<button type="button" class="prev" onclick="history.back(-1)"></button>
		<button type="button" class="mnu _jsMnu"></button>
		<a href="<?php echo G5_SHOP_URL; ?>/cart.php" class="cart"><span><?php echo get_boxcart_datas_count(); ?></span></a>
	</header>

	<nav class="_menu">
		<a href="<?=G5_URL?>" class="home">HOME</a>
		<ul class="menu">
			<li>
				<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10" class="_jsMnu">
					<div class="ico"><img src="<?=G5_URL?>/images/ico-menu1.svg" alt=""></div>
					<div class="txt">카테고리</div>
				</a>
				<a href="<?php echo G5_SHOP_URL; ?>/mypage.php">
					<div class="ico"><img src="<?=G5_URL?>/images/ico-menu2.svg" alt=""></div>
					<div class="txt">배송조회</div>
				</a>
			</li>
			<li>
				<a href="<?php echo G5_SHOP_URL; ?>/cart.php">
					<div class="ico"><img src="<?=G5_URL?>/images/ico-menu3.svg" alt=""></div>
					<div class="txt">장바구니</div>
				</a>
				<a href="<?php echo G5_SHOP_URL; ?>/mypage.php">
					<div class="ico"><img src="<?=G5_URL?>/images/ico-menu4.svg" alt=""></div>
					<div class="txt">마이페이지</div>
				</a>
			</li>
		</ul>
	</nav>

	<nav class="_nav">
		<div class="head">
			<h2 class="logo"><a href="<?=G5_URL?>">MARKET CHANNEL</a></h2>
			<button type="button" class="close"><i class="ri-close-large-line"></i></button>
		</div>
		<div class="bt">
			<?php if ($is_member) { ?>
			<a href="<?php echo G5_SHOP_URL; ?>/mypage.php"><span>마이페이지</span></a>
			<a href="<?php echo G5_BBS_URL ?>/logout.php"><span>로그아웃</span></a>
			<?php } else {  ?>
			<a href="<?php echo G5_BBS_URL ?>/login.php"><span>로그인</span></a>
			<a href="<?php echo G5_BBS_URL ?>/register.php"><span>회원가입</span></a>
			<?php }?>
		</div>
		<div class="gnb">
			<ul>
				<li>
					<a href="/bbs/content.php?co_id=company">마켓채널 소개</a>
				</li>
				<li>
					<a href="#">상품 카테고리</a>
					<ul class="ty1">
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">전체</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">새우류</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">연체류</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">생선류</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">갑각류(꽃게외)</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">어패류(조개외)</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">초밥류</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">가공수산류</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">특수부위</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">정육</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">공산품</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">소스류</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">기타수산</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">냉동야채</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">반찬</a></li>
					</ul>
				</li>
				<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">신규상품</a></li>
				<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">특가상품</a></li>
				<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">MD 추천상품</a></li>
				<li>
					<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">업종별 상품</a>
					<ul class="ty1">
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">찜</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">탕</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">볶음</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">조림</a></li>
					</ul>
				</li>
				<li>
					<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">요리별 상품</a>
					<ul class="ty3">
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">한식</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">중식</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=10">양식 및 기타</a></li>
					</ul>
				</li>
			</ul>
			<ul>
				<li>
					<a href="#">마이페이지</a>
					<ul class="ty4">
						<li><a href="#">내 정보변경</a></li>
						<li><a href="#">결제내역</a></li>
						<li><a href="#">구매내역</a></li>
						<li><a href="<?php echo G5_SHOP_URL; ?>/cart.php">장바구니</a></li>
					</ul>
				</li>
				<li>
					<a href="/bbs/board.php?bo_table=notice">공지사항</a>
				</li>
				<li>
					<a href="/bbs/faq.php?fm_id=1">FAQ</a>
				</li>
				<li>
					<a href="/bbs/write.php?bo_table=qa">문의하기</a>
				</li>
			</ul>
		</div>
	</nav>


	<?php if(!defined('_INDEX_')) {?>
	<div class="_sub">
		<div class="inner">
	<?php } ?>