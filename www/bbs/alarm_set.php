<?php
include_once('./_common.php');
include_once('./_head.php');
?>

<div class="_alarmSet">
	<div class="box">
		<dl>
			<dt>소식받기</dt>
			<dd>마켓채널의 새로운 소식을 만나보세요</dd>
		</dl>
		<div class="set">
			<label><input type="checkbox" checked><i></i></label>
		</div>
	</div>
	<div class="box">
		<dl>
			<dt>구매 및 문의알림</dt>
			<dd>구매정보 및 문의에 대한 알림을 받아보세요</dd>
		</dl>
		<div class="set">
			<label><input type="checkbox"><i></i></label>
		</div>
	</div>
</div>

<div class="_btnConfirm">
	<a href="http://market-channel.com/" class="btn_cancel">취소</a>
	<button type="submit" id="btn_submit" class="btn_submit" accesskey="s">확인</button>
</div>


<?php
include_once('./_tail.php');
?>