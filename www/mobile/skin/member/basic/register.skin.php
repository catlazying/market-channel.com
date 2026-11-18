<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>
<div class="mbskin">
	<?php
		// 소셜로그인 사용시 소셜로그인 버튼
		@include_once(get_social_skin_path().'/social_register.skin.php');
		?>
	<form name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">
		<p>회원가입을 위해 이용약관과 개인정보취급(처리) 방침에 동의해주시기 바랍니다</p>
		<div id="fregister_chkall" class="chk_all fregister_agree">
			<input type="checkbox" name="chk_all" id="chk_all" class="selec_chk">
			<label for="chk_all"><span></span>회원가입 약관에 모두 동의합니다</label>
		</div>
		<section id="fregister_term">
			<h2>이용약관동의 (필수)</h2>
			<textarea name="textarea" readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
			<fieldset class="fregister_agree">
				<input type="checkbox" name="agree" value="1" id="agree11" class="selec_chk">
				<label for="agree11"><span></span><b class="sound_only">회원가입약관의 내용에 동의합니다.</b></label>
			</fieldset>
		</section>
		<section id="fregister_private">
			<h2>개인정보취급(처리)방침동의 (필수)</h2>
			<div class="tbl_head01 tbl_wrap">
				<table>
					<caption>
						개인정보 수집 및 이용
					</caption>
					<thead>
						<tr>
							<th colspan="2">목적</th>
						</tr>
						<tr>
							<th>항목</th>
							<th>보유기간</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td  colspan="2">이용자 식별 및 본인여부 확인</td>
						</tr>
						<tr>
							<td>아이디, 이름, 비밀번호<?php echo ($config['cf_cert_use'])? ", 생년월일, 휴대폰 번호(본인인증 할 때만, 아이핀 제외), 암호화된 개인식별부호(CI)" : ""; ?></td>
							<td>회원 탈퇴 시까지</td>
						</tr>
						<tr>
							<td colspan="2">고객서비스 이용에 관한 통지,<br>
								CS대응을 위한 이용자 식별</td>
						</tr>
						<tr>
							<td>연락처 (이메일, 휴대전화번호)</td>
							<td>회원 탈퇴 시까지</td>
						</tr>
					</tbody>
				</table>
			</div>
			<fieldset class="fregister_agree">
				<input type="checkbox" name="agree2" value="1" id="agree21" class="selec_chk">
				<label for="agree21"><span></span><b class="sound_only">개인정보 수집 및 이용의 내용에 동의합니다.</b></label>
			</fieldset>
		</section>
		<section id="">
			<h2>마케팅 활용동의 및 광고 수신동의 (선택)</h2>
			<textarea name="textarea" readonly><?php echo get_text($config['cf_markting']) ?></textarea>
			<div class="_priAgrInp mt10">
				<label class="_lab"><input type="checkbox" name="agree_email" value="1"><i></i> <span>이메일 수신</span></label>
				<label class="_lab"><input type="checkbox" name="agree_sms" value="1"><i></i> <span>SMS 수신</span></label>
			</div>

			<div class="fz14 tal mt10">
				* SMS 수신 여부는 광고성 SMS에 대한 수신 설정이며 학원 공지 (강의실 변경 등) 는 수신여부 설정과 관계없이 수신됩니다.
			</div>
		</section>

		<div class="btn_confirm">
			<button type="submit" class="btn_submit">회원가입</button>
		</div>
	</form>
	<script>
		function fregister_submit(f)
		{
				if (!f.agree.checked) {
						alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
						f.agree.focus();
						return false;
				}

				if (!f.agree2.checked) {
						alert("개인정보 수집 및 이용의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
						f.agree2.focus();
						return false;
				}

				return true;
		}

		jQuery(function($){
				// 모두선택
				$("input[name=chk_all]").click(function() {
						if ($(this).prop('checked')) {
								$("input[name^=agree]").prop('checked', true);
						} else {
								$("input[name^=agree]").prop("checked", false);
						}
				});
		});
		</script>
</div>
