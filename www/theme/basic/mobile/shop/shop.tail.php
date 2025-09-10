<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$admin = get_admin("super");

// 사용자 화면 우측과 하단을 담당하는 페이지입니다.
// 우측, 하단 화면을 꾸미려면 이 파일을 수정합니다.
?>

	<?php if(!defined('_INDEX_')) {?>
		</div>
	</div>
	<?php } ?>


	<footer class="_footer">
		<div class="fnb">
			<a href="/bbs/content.php?co_id=company">마켓채널소개</a>
			<a href="/bbs/content.php?co_id=provision">이용약관</a>
			<a href="/bbs/content.php?co_id=privacy">개인정보처리방침</a>
		</div>
		<ul class="info">
			<li>회사명 : 씨에버상사</li>
			<li>대표자명 : 김연수, 이정필</li>
			<li>사업자등록번호 : <span>107-87-71715</span></li>
			<li>통신판매업 : 제 2025-인천중구-0453 호</li>
		</ul>
		<p class="safe">
			씨에버상사는 고객님이 현금 결제한 금액에 대해 국민은행과 채무지급보증계약을 체결하여 안전거래를 보장하고 있습니다.
		</p>
		<ul class="cs">
			<li>고객센터 : <span>070-7695-4560</span></li>
			<li>이메일 : tldpqj4939@daum.net</li>
			<li>상담운영시간 : 09:00 ~ 16:00 (월 ~ 금)</li>
			<li>휴 무 : 토요일, 일요일 휴무 / 공휴일 : 별도 공지</li>
			<li>고객센터 운영시간 외에는 카카오톡 채널을 이용해주세요.</li>
		</ul>
	</footer>

</div>




<?php
$sec = get_microtime() - $begin_time;
$file = $_SERVER['SCRIPT_NAME'];

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<script src="<?php echo G5_JS_URL; ?>/sns.js"></script>

<?php
include_once(G5_THEME_PATH.'/tail.sub.php');