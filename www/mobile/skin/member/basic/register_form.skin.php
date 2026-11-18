<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(empty($member['mb_name']) && !empty($_SESSION['name'])){
	$member['mb_name'] = $_SESSION['name'];
}
if(empty($member['mb_email']) && !empty($_SESSION['email'])){
	$member['mb_email'] = $_SESSION['email'];
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_javascript('<script src="'.G5_JS_URL.'/jquery.register_form.js"></script>', 0);
if ($config['cf_cert_use'] && ($config['cf_cert_simple'] || $config['cf_cert_ipin'] || $config['cf_cert_hp']))
		add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 0);
?>
<div class="register">
		<form name="fregisterform" id="fregisterform" action="<?php echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="w" value="<?php echo $w ?>">
		<input type="hidden" name="url" value="<?php echo $urlencode ?>">
		<input type="hidden" name="agree" value="<?php echo $agree ?>">
		<input type="hidden" name="agree2" value="<?php echo $agree2 ?>">
		<input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
		<input type="hidden" name="cert_no" value="">
		<?php if (isset($member['mb_sex'])) { ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php } ?>
		<?php if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))) { // 닉네임수정일이 지나지 않았다면 ?>
		<input type="hidden" name="mb_nick_default" value="<?php echo get_text($member['mb_nick']) ?>">
		<input type="hidden" name="mb_nick" value="<?php echo get_text($member['mb_nick']) ?>">
		<?php } ?>

		<div class="form_01">
				<h2>회원정보입력</h2>
				<ul>
					<li>
							<label for="reg_mb_id" class="sound_only">아이디 (필수)</label>
							<div class="adress">
								<input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" class="frm_input full_input <?php echo $required ?> <?php echo $readonly ?>" minlength="3" maxlength="20" <?php echo $required ?> <?php echo $readonly ?> placeholder="사업자등록번호를 - 없이 입력해주세요">
								<?php if(empty($member['mb_id'] )) { ?>
								<button id="btn_id_check" type="button" class="btn_frmline" onclick="checkBusinessNumber(this.form.mb_id.value)">인증하기</button>
								<input type="hidden" name="id_check" value="">
								<?php } ?>
							</div>
							<span class="frm_info" id="msg_mb_id"></span>
							<span class="frm_info" id="bn_check_info" >
								* 사업자등록번호가 아이디로 사용 됩니다
							</span>
							<span class="frm_error" style="display:none;" id="bn_check_error">
								* 사업자등록번호가 조회되지 않습니다 (- 제외하고 직접입력해주세요)
							</span>
					</li>
					<li class="password">
							<label for="reg_mb_password" class="sound_only">비밀번호를 입력해주세요 (필수)</label>
							<input type="password" name="mb_password" id="reg_mb_password" class="frm_input full_input <?php echo $required ?>" minlength="3" maxlength="20" <?php echo $required ?> placeholder="비밀번호를 입력해주세요">

					</li>
					<li>
							<label for="reg_mb_password_re" class="sound_only">비밀번호를 확인해주세요 (필수)</label>
							<input type="password" name="mb_password_re" id="reg_mb_password_re" class="frm_input full_input <?php echo $required ?>" minlength="3" maxlength="20" <?php echo $required ?>  placeholder="비밀번호를 확인해주세요">

							<span class="frm_info">비밀번호는 영문, 숫자,특수문자(~!@#$%^&*()_+-=) 조합 4자 이상 입니다.</span>
							<?/* 감춤처리 <!--
							<span class="frm_error">* 비밀번호 규칙에 맞지 않습니다</span>
							--> */ ?>
					</li>
				</ul>
		</div>

		<div class="form_01">
				<h2>개인정보 입력</h2>
				<ul>
						<li>
								<?php
								$desc_name = '';
								$desc_phone = '';
							if ($config['cf_cert_use']) {
										$desc_name = ' - 본인확인 시 자동입력';
										$desc_phone = ' - 본인확인 시 자동입력';

										if (!$config['cf_cert_simple'] && !$config['cf_cert_hp'] && $config['cf_cert_ipin']) {
												$desc_phone = '';
										}

										if($config['cf_cert_simple']) {
												echo '<button type="button" id="win_sa_kakao_cert" class="btn_frmline btn win_sa_cert" data-type="">간편인증</button>'.PHP_EOL;
										}
										if($config['cf_cert_hp'])
												echo '<button type="button" id="win_hp_cert" class="btn_frmline btn">휴대폰 본인확인</button>'.PHP_EOL;
										if ($config['cf_cert_ipin'])
												echo '<button type="button" id="win_ipin_cert" class="btn_frmline btn">아이핀 본인확인</button>'.PHP_EOL;

										echo '<span class="cert_req">(필수)</span>';
									echo '<noscript>본인확인을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>'.PHP_EOL;
							}
							?>
							<?php
							if ($config['cf_cert_use'] && $member['mb_certify']) {
									switch  ($member['mb_certify']) {
												case "simple":
														$mb_cert = "간편인증";
														break;
												case "ipin":
														$mb_cert = "아이핀";
														break;
												case "hp":
														$mb_cert = "휴대폰";
														break;
										}
							?>
							<div id="msg_certify">
									<strong><?php echo $mb_cert; ?> 본인확인</strong><?php if ($member['mb_adult']) { ?> 및 <strong>성인인증</strong><?php } ?> 완료
							</div>
						<?php } ?>
						</li>
					<li class="rgs_name_li">
							<input type="text" id="reg_mb_name" name="mb_name" value="<?php echo get_text($member['mb_name']) ?>" <?php echo $required ?> <?php echo $name_readonly; ?> class="frm_input full_input <?php echo $required ?> <?php echo $name_readonly ?>" placeholder="이름을 입력해주세요">

					</li>

					<?php if ($config['cf_use_hp'] || ($config["cf_cert_use"] && ($config['cf_cert_hp'] || $config['cf_cert_simple']))) {  ?>
					<li>
								<label for="reg_mb_hp" class="sound_only">휴대폰번호<?php if (!empty($hp_required)) { ?> (필수)<?php } ?><?php echo $desc_phone ?></label>

								<input type="text" name="mb_hp" value="<?php echo get_text($member['mb_hp']) ?>" id="reg_mb_hp" <?php echo $hp_required; ?> <?php echo $hp_readonly; ?> class="frm_input full_input <?php echo $hp_required; ?> <?php echo $hp_readonly; ?>" maxlength="20" placeholder="휴대폰번호를 입력해주세요">
							<?php if ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple'])) { ?>
							<input type="hidden" name="old_mb_hp" value="<?php echo get_text($member['mb_hp']) ?>">
							<?php } ?>

					</li>
					<?php } ?>

					<?php if ($req_nick) { ?>
						<li>
							<label for="reg_mb_nick" class="sound_only">상호명 (필수)</label>

							<span class="frm_info">
									공백없이 한글,영문,숫자만 입력 가능 (한글2자, 영문4자 이상)
									<?/* 감춤처리 <!-- <br>
									닉네임을 바꾸시면 앞으로 <?php echo (int)$config['cf_nick_modify'] ?>일 이내에는 변경 할 수 없습니다.
									--> */?>
							</span>
							<input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>">
							<input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>" id="reg_mb_nick" required class="frm_input full_input required nospace" maxlength="20" placeholder="상호명을 입력해주세요">
							<span id="msg_mb_nick"></span>
						</li>
					<?php } ?>

					<li>
								<?php if ($config['cf_use_email_certify']) {  ?>
								<span class="frm_info">
										<?php if ($w=='') { echo "E-mail 로 발송된 내용을 확인한 후 인증하셔야 회원가입이 완료됩니다."; }  ?>
										<?php if ($w=='u') { echo "E-mail 주소를 변경하시면 다시 인증하셔야 합니다."; }  ?>
								</span>
								<?php }  ?>
								<input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
								<input type="email" name="mb_email" value="<?php echo isset($member['mb_email'])?$member['mb_email']:''; ?>" id="reg_mb_email" required class="frm_input email required" size="50" maxlength="100" placeholder="이메일 주소를 입력해주세요">

							<span class="frm_info">
								* 세금계산서를 수령할 이메일 주소를 입력해주세요
							</span>
							<?/* 감춤처리 <!--
							<span class="frm_error">
								* 이메일 규칙에 맞지 않습니다
							</span>
							--> */ ?>
					</li>

					<?php if ($config['cf_use_homepage']) { ?>
					<li>
							<label for="reg_mb_homepage" class="sound_only">홈페이지<?php if ($config['cf_req_homepage']){ ?> (필수)<?php } ?></label>
							<input type="text" name="mb_homepage" value="<?php echo get_text($member['mb_homepage']) ?>" id="reg_mb_homepage" class="frm_input full_input <?php echo $config['cf_req_homepage']?"required":""; ?>" maxlength="255" <?php echo $config['cf_req_homepage']?"required":""; ?> placeholder="홈페이지<?php if ($config['cf_req_homepage']){ ?> (필수)<?php } ?>">
					</li>
					<?php } ?>

					<?php if ($config['cf_use_tel']) { ?>
					<li>
							<label for="reg_mb_tel" class="sound_only">전화번호<?php if ($config['cf_req_tel']) { ?> (필수)<?php } ?></label>
							<input type="text" name="mb_tel" value="<?php echo get_text($member['mb_tel']) ?>" id="reg_mb_tel" class="frm_input full_input <?php echo $config['cf_req_tel']?"required":""; ?>" <?php if ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple']) && $member['mb_certify']) { echo "readonly"; } ?> maxlength="20" <?php echo $config['cf_req_tel']?"required":""; ?> placeholder="전화번호<?php if ($config['cf_req_tel']) { ?> (필수)<?php } ?>">
					</li>
					<?php } ?>
				</ul>
				<div class="_topArea mt20">
				</div>
				<ul>
					<?php if ($config['cf_use_addr']) { ?>
					<li>
						<div class="adress">
							<input type="text" name="mb_zip" value="<?php echo $member['mb_zip1'].$member['mb_zip2']; ?>" id="reg_mb_zip" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input <?php echo $config['cf_req_addr']?"required":""; ?>" size="5" maxlength="6" placeholder="사업장 주소">
							<button type="button" class="btn_frmline" onclick="win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소검색</button>
						</div>
						<input type="text" name="mb_addr1" value="<?php echo get_text($member['mb_addr1']) ?>" id="reg_mb_addr1" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input frm_address <?php echo $config['cf_req_addr']?"required":""; ?>" size="50" placeholder="기본 주소"><br>
						<input type="text" name="mb_addr2" value="<?php echo get_text($member['mb_addr2']) ?>" id="reg_mb_addr2" class="frm_input frm_address" size="50" placeholder="상세주소">
						<br>
						<input type="text" name="mb_addr3" value="<?php echo get_text($member['mb_addr3']) ?>" id="reg_mb_addr3" class="frm_input frm_address" size="50" readonly="readonly" placeholder="참고항목">
						<input type="hidden" name="mb_addr_jibeon" value="<?php echo get_text($member['mb_addr_jibeon']); ?>">
						<?php if( $apps == "IOS"){?>
							<button onclick="requestCamera()" type="button" class="btn_cancel">사업자등록증 촬영하기</button>
							<img id="preview" style="width: 200px;" />
							<input type="hidden" name="mb_profile">

							<script>
								function requestCamera() {
									if (window.webkit && window.webkit.messageHandlers.openCamera) {
										window.webkit.messageHandlers.openCamera.postMessage("");
									} else {
										alert("Native 앱 환경이 아닙니다.");
									}
								}
								window.onImageCaptured  = function (base64Data) {
									var imgBase = "data:image/jpeg;base64," + base64Data;
									const imageElement = document.getElementById('preview');
									imageElement.src = imgBase;
									document.querySelector('input[name="mb_profile"]').value = imgBase;
								}
							</script>
 						<?php  } ?>

					</li>
					<?php } ?>
				</ul>
				<?php if(G5_DELIVERY_YN == 'Y') { ?>
					<div class="_topArea mt20">
						<div class="rig">
							<label class="_lab"><input type="checkbox" name="same_address"><i></i> <span>사업장 주소와 동일함</span></label>
						</div>
					</div>
					<ul>
						<li>
							<div class="adress">
								<input type="text" name="mb_2" value="<?php echo $member['mb_2']; ?>" id="reg_mb_zip" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input <?php echo $config['cf_req_addr']?"required":""; ?>" size="5" maxlength="6" placeholder="기본 배송지 주소">
								<button type="button" class="btn_frmline" onclick="win_zip('fregisterform', 'mb_2', 'mb_3', 'mb_4', 'mb_5', 'mb_6');">주소검색</button>
							</div>
							<input type="text" name="mb_3" value="<?php echo get_text($member['mb_3']) ?>" id="reg_mb_3" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input frm_address <?php echo $config['cf_req_addr']?"required":""; ?>" size="50" placeholder="기본 주소"><br>
							<input type="text" name="mb_4" value="<?php echo get_text($member['mb_4']) ?>" id="reg_mb_4" class="frm_input frm_address" size="50" placeholder="상세주소">
							<br>
							<input type="text" name="mb_5" value="<?php echo get_text($member['mb_5']) ?>" id="reg_mb_5" class="frm_input frm_address" size="50" readonly="readonly" placeholder="참고항목">
							<input type="hidden" name="mb_6" value="<?php echo get_text($member['mb_6']); ?>">

						</li>
					</ul>
					<script>
						$(function(){
							$('input[name="same_address"]').on('change', function(){
								if($(this).is(':checked')) {
									$('input[name="mb_2"]').val($('input[name="mb_zip"]').val());
									$('input[name="mb_3"]').val($('input[name="mb_addr1"]').val());
									$('input[name="mb_4"]').val($('input[name="mb_addr2"]').val());
									$('input[name="mb_5"]').val($('input[name="mb_addr3"]').val());
									$('input[name="mb_6"]').val($('input[name="mb_addr_jibeon"]').val());
								} else {
									$('input[name="mb_2"]').val('');
									$('input[name="mb_3"]').val('');
									$('input[name="mb_4"]').val('');
									$('input[name="mb_5"]').val('');
									$('input[name="mb_6"]').val('');
								}
							});
						});
					</script>
				<?php } ?>
				<div class="_topArea mt20">
				</div>
				<div class="form_01">
					<h2>환불정보를 입력해주세요</h2>
					<ul>
						<li>
							<label for="reg_mb_7" class="sound_only">은행명</label>
							<input type="text" name="mb_7" id="reg_mb_7" class="frm_input full_input" placeholder="은행명" value="<?php echo get_text($member['mb_7']); ?>">
						</li>
						<li>
							<label for="reg_mb_8" class="sound_only">계좌번호</label>
							<input type="text" name="mb_8" id="reg_mb_8" class="frm_input full_input" placeholder="계좌번호" value="<?php echo get_text($member['mb_8']); ?>">
						</li>
					</ul>
					<span class="frm_info">
						* 주문취소 및 환불 시 꼭 필요한 정보 입니다
					</span>
				</div>
				<div class="_topArea mt20">
				</div>

				<?php if ($w == "" && $config['cf_use_recommend']) { ?>
				<ul>
					<li>
						<label for="reg_mb_recommend" class="sound_only">추천인아이디</label>
						<input type="text" name="mb_recommend" id="reg_mb_recommend" class="frm_input full_input" placeholder="추천인아이디">
					</li>
				</ul>
				<?php } ?>

				<input type="hidden" name="mb_mailling" value="1" <?php echo ($agree_email=='1' || $member['mb_mailling'])?'checked':''; ?>>
				<?php if ($config['cf_use_hp']) { ?>
					<input type="hidden" name="mb_sms" value="1" <?php echo ($agree_sms=='1' || $member['mb_sms'])?'checked':''; ?>>
				<?php } ?>

		</div>

		<div class="form_01">
				<h2>자동등록방지</h2>
		<ul>
			<?/* 감춤처리 <!--
			<!-- <?php if ($config['cf_use_signature']) { ?>
					<li>
							<label for="reg_mb_signature" class="sound_only">서명<?php if ($config['cf_req_signature']){ ?> (필수)<?php } ?></label>
							<textarea name="mb_signature" id="reg_mb_signature" class="<?php echo $config['cf_req_signature']?"required":""; ?>" <?php echo $config['cf_req_signature']?"required":""; ?> placeholder="서명<?php if ($config['cf_req_signature']){ ?> (필수)<?php } ?>"><?php echo $member['mb_signature'] ?></textarea>
					</li>
					<?php } ?>

					<?php if ($config['cf_use_profile']) { ?>
					<li>
							<label for="reg_mb_profile" class="sound_only">자기소개</label>
							<textarea name="mb_profile" id="reg_mb_profile" class="<?php echo $config['cf_req_profile']?"required":""; ?>" <?php echo $config['cf_req_profile']?"required":""; ?> placeholder="자기소개"><?php echo $member['mb_profile'] ?></textarea>
					</li>
					<?php } ?>

					<?php if ($config['cf_use_member_icon'] && $member['mb_level'] >= $config['cf_icon_level']) { ?>
					<li class="filebox">
									<input type="text" class="fileName" readonly="readonly" placeholder="회원아이콘">
							<label for="reg_mb_icon" class="btn_file"><span class="sound_only">회원아이콘</span>이미지선택</label>
							<input type="file" name="mb_icon" id="reg_mb_icon" class="uploadBtn">
							<span class="frm_info">
									이미지 크기는 가로 <?php echo $config['cf_member_icon_width'] ?>픽셀, 세로 <?php echo $config['cf_member_icon_height'] ?>픽셀 이하로 해주세요.<br>
									gif, jpg, png파일만 가능하며 용량 <?php echo number_format($config['cf_member_icon_size']) ?>바이트 이하만 등록됩니다.
							</span>
							<?php if ($w == 'u' && file_exists($mb_icon_path)) { ?>
							<img src="<?php echo $mb_icon_url ?>" alt="회원아이콘">
							<input type="checkbox" name="del_mb_icon" value="1" id="del_mb_icon">
							<label for="del_mb_icon">삭제</label>
							<?php } ?>
					</li>
					<?php } ?>

					<?php if ($member['mb_level'] >= $config['cf_icon_level'] && $config['cf_member_img_size'] && $config['cf_member_img_width'] && $config['cf_member_img_height']) {  ?>
					<li class="reg_mb_img_file filebox">
						<input type="text" class="fileName" readonly="readonly" placeholder="회원이미지">
							<label for="reg_mb_img" class="btn_file"><span class="sound_only">회원이미지</span>이미지선택</label>
							<input type="file" name="mb_img" id="reg_mb_img" class="uploadBtn">
							<span class="frm_info">
									이미지 크기는 가로 <?php echo $config['cf_member_img_width'] ?>픽셀, 세로 <?php echo $config['cf_member_img_height'] ?>픽셀 이하로 해주세요.<br>
									gif, jpg, png파일만 가능하며 용량 <?php echo number_format($config['cf_member_img_size']) ?>바이트 이하만 등록됩니다.
							</span>
							<?php if ($w == 'u' && file_exists($mb_img_path)) {  ?>
							<img src="<?php echo $mb_img_url ?>" alt="회원아이콘">
							<input type="checkbox" name="del_mb_img" value="1" id="del_mb_img">
							<label for="del_mb_img">삭제</label>
							<?php }  ?>
					</li>
					<?php } ?>

					<li class="chk_box">
						<input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <?php echo ($w=='' || $member['mb_mailling'])?'checked':''; ?> class="selec_chk">
							<label for="reg_mb_mailling">
								<span></span>
								<b class="sound_only">메일링서비스</b>
							</label>
							<span class="chk_li">정보 메일을 받겠습니다.</span>
					</li>

					<?php if ($config['cf_use_hp']) { ?>
					<li class="chk_box">
							<input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <?php echo ($w=='' || $member['mb_sms'])?'checked':''; ?> class="selec_chk">
						<label for="reg_mb_sms">
								<span></span>
								<b class="sound_only">SMS 수신여부</b>
							</label>
							<span class="chk_li">휴대폰 문자메세지를 받겠습니다.</span>
					</li>
					<?php } ?>

					<?php if (isset($member['mb_open_date']) && $member['mb_open_date'] <= date("Y-m-d", G5_SERVER_TIME - ($config['cf_open_modify'] * 86400)) || empty($member['mb_open_date'])) { // 정보공개 수정일이 지났다면 수정가능 ?>
					<li class="chk_box">
							<input type="checkbox" name="mb_open" value="1" id="reg_mb_open" <?php echo ($w=='' || $member['mb_open'])?'checked':''; ?> class="selec_chk">
						<label for="reg_mb_open">
							<span></span>
							<b class="sound_only">정보공개</b>
						</label>
							<span class="chk_li">다른분들이 나의 정보를 볼 수 있도록 합니다.</span>
							<span class="frm_info">
									정보공개를 바꾸시면 앞으로 <?php echo (int)$config['cf_open_modify'] ?>일 이내에는 변경이 안됩니다.
							</span>
							<input type="hidden" name="mb_open_default" value="<?php echo $member['mb_open'] ?>">
					</li>
					<?php } else { ?>
					<li>
							<span  class="frm_label">정보공개</span>
							<input type="hidden" name="mb_open" value="<?php echo $member['mb_open'] ?>">

							<span class="frm_info">
									정보공개는 수정후 <?php echo (int)$config['cf_open_modify'] ?>일 이내, <?php echo date("Y년 m월 j일", isset($member['mb_open_date']) ? strtotime("{$member['mb_open_date']} 00:00:00")+$config['cf_open_modify']*86400:G5_SERVER_TIME+$config['cf_open_modify']*86400); ?> 까지는 변경이 안됩니다.<br>
									이렇게 하는 이유는 잦은 정보공개 수정으로 인하여 쪽지를 보낸 후 받지 않는 경우를 막기 위해서 입니다.
							</span>
					</li>
					<?php } ?>

					<?php
					//회원정보 수정인 경우 소셜 계정 출력
					if( $w == 'u' && function_exists('social_member_provider_manage') ){
							social_member_provider_manage();
					}
					?>

					<?php if ($w == "" && $config['cf_use_recommend']) { ?>
					<li>
							<label for="reg_mb_recommend" class="sound_only">추천인아이디</label>
							<input type="text" name="mb_recommend" id="reg_mb_recommend" class="frm_input full_input" placeholder="추천인아이디">
					</li>
					<?php } ?> -->
		--> */?>


					<li class="is_captcha_use">
							<?php echo captcha_html(); ?>
					</li>
			</ul>
		</div>

		<div class="btn_confirm">
				<a href="<?php echo G5_URL; ?>/" class="btn_cancel">취소</a>
				<button type="submit" id="btn_submit" class="btn_submit" accesskey="s"><?php echo $w==''?'회원가입':'정보수정'; ?></button>
		</div>
		</form>

		<script>
		$(function() {
				$("#reg_zip_find").css("display", "inline-block");
				var pageTypeParam = "pageType=register";

				<?php if($config['cf_cert_use'] && $config['cf_cert_simple']) { ?>
				// 이니시스 간편인증
				var url = "<?php echo G5_INICERT_URL; ?>/ini_request.php";
				var type = "";
				var params = "";
				var request_url = "";

				$(".win_sa_cert").click(function() {
						if(!cert_confirm()) return false;
						type = $(this).data("type");
						params = "?directAgency=" + type + "&" + pageTypeParam;
						request_url = url + params;
						call_sa(request_url);
				});
				<?php } ?>
				<?php if($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
				// 아이핀인증
				var params = "";
				$("#win_ipin_cert").click(function() {
						if(!cert_confirm()) return false;
						params = "?" + pageTypeParam;
						var url = "<?php echo G5_OKNAME_URL; ?>/ipin1.php"+params;
						certify_win_open('kcb-ipin', url);
						return;
				});

				<?php } ?>
				<?php if($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
				// 휴대폰인증
				var params = "";
				$("#win_hp_cert").click(function() {
						if(!cert_confirm()) return false;
						params = "?" + pageTypeParam;
						<?php
						switch($config['cf_cert_hp']) {
								case 'kcb':
										$cert_url = G5_OKNAME_URL.'/hpcert1.php';
										$cert_type = 'kcb-hp';
										break;
								case 'kcp':
										$cert_url = G5_KCPCERT_URL.'/kcpcert_form.php';
										$cert_type = 'kcp-hp';
										break;
								case 'lg':
										$cert_url = G5_LGXPAY_URL.'/AuthOnlyReq.php';
										$cert_type = 'lg-hp';
										break;
								default:
										echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오");';
										echo 'return false;';
										break;
						}
						?>
						certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>"+params);
						return;
				});
				<?php } ?>
		});

		// 인증체크
		function cert_confirm()
		{
				var val = document.fregisterform.cert_type.value;
				var type;

				switch(val) {
						case "simple":
								type = "간편인증";
								break;
						case "ipin":
								type = "아이핀";
								break;
						case "hp":
								type = "휴대폰";
								break;
						default:
								return true;
				}

				if(confirm("이미 "+type+"으로 본인확인을 완료하셨습니다.\n\n이전 인증을 취소하고 다시 인증하시겠습니까?"))
						return true;
				else
						return false;
		}

		// submit 최종 폼체크
		function fregisterform_submit(f)
		{
				// 회원아이디 검사
				if($('[name="id_check"]').val()	 != f.mb_id.value) {
					alert('사업자등록번호 인증하기를 해주십시오.');
					f.mb_id.focus();
					return false;
				}
				if (f.w.value == "") {
					var msg = reg_mb_id_check();
					if (msg) {
							alert(msg);
							f.mb_id.select();
							return false;
					}
				}

				// 비밀번호를 영문숫자 특수문자(~!@#$%^&*()_+-=) 혼합 4글자 이상으로 검사
				var reg = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+-=]).{4,}$/;
				if (!reg.test(f.mb_password.value) && f.mb_password.value.length > 0) {
						alert('비밀번호는 영문자, 숫자, 특수문자를 혼합하여 4글자 이상 입력하십시오.');
						f.mb_password.focus();
						return false;
				}

				if (f.w.value == '') {
						if (f.mb_password.value.length < 4) {
								alert('비밀번호를 4글자 이상 입력하십시오.');
								f.mb_password.focus();
								return false;
						}
				}

				if (f.mb_password.value != f.mb_password_re.value) {
						alert('비밀번호가 같지 않습니다.');
						f.mb_password_re.focus();
						return false;
				}

				if (f.mb_password.value.length > 0) {
						if (f.mb_password_re.value.length < 4) {
								alert('비밀번호를 4글자 이상 입력하십시오.');
								f.mb_password_re.focus();
								return false;
						}
				}

				// 이름 검사
				if (f.w.value=='') {
						if (f.mb_name.value.length < 1) {
								alert('이름을 입력하십시오.');
								f.mb_name.focus();
								return false;
						}
				}

				<?php if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
				// 본인확인 체크
				if(f.cert_no.value=="") {
						alert("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
						return false;
				}
				<?php } ?>

				// 닉네임 검사
				if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
						var msg = reg_mb_nick_check();
						if (msg) {
								alert(msg);
								f.reg_mb_nick.select();
								return false;
						}
				}

				// E-mail 검사
				if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
						var msg = reg_mb_email_check();
						if (msg) {
								alert(msg);
								f.reg_mb_email.select();
								return false;
						}
				}

				<?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {  ?>
				// 휴대폰번호 체크
				var msg = reg_mb_hp_check();
				if (msg) {
						alert(msg);
						f.reg_mb_hp.select();
						return false;
				}
				<?php } ?>

				if (typeof f.mb_icon != "undefined") {
						if (f.mb_icon.value) {
								if (!f.mb_icon.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
										alert("회원아이콘이 이미지 파일이 아닙니다.");
										f.mb_icon.focus();
										return false;
								}
						}
				}

				if (typeof f.mb_img != "undefined") {
						if (f.mb_img.value) {
								if (!f.mb_img.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
										alert("회원이미지가 이미지 파일이 아닙니다.");
										f.mb_img.focus();
										return false;
								}
						}
				}

				if (typeof(f.mb_recommend) != 'undefined' && f.mb_recommend.value) {
						if (f.mb_id.value == f.mb_recommend.value) {
								alert('본인을 추천할 수 없습니다.');
								f.mb_recommend.focus();
								return false;
						}

						var msg = reg_mb_recommend_check();
						if (msg) {
								alert(msg);
								f.mb_recommend.select();
								return false;
						}
				}

				<?php echo chk_captcha_js(); ?>

				document.getElementById("btn_submit").disabled = "disabled";

				return true;
		}

		var uploadFile = $('.filebox .uploadBtn');
		uploadFile.on('change', function(){
			if(window.FileReader){
				var filename = $(this)[0].files[0].name;
			} else {
				var filename = $(this).val().split('/').pop().split('\\').pop();
			}
			$(this).siblings('.fileName').val(filename);
		});

		// 사업자등록번호 인증 API 예제
		// 출처: 공공데이터포털 https://www.data.go.kr/
		// https://www.data.go.kr/data/15012622/openapi.do
		// b_stt_cd : 사업자상태코드 (01: 정상, 02: 휴업, 03: 폐업, 04: 말소)
		function checkBusinessNumber(b_no) {
			var msg = reg_mb_id_check(b_no);
			if (msg) {
					alert(msg);
					return false;
			}
			$('[name="id_check"]').val('');
			b_no = b_no.replace(/-/g, ''); // 하이픈 제거
			var data = {
				"b_no": [b_no]
			};
			$.ajax({
				url: "https://api.odcloud.kr/api/nts-businessman/v1/status?serviceKey=61e139f30e4b68ea414aa7a1df396f9f3acf6ac882a4b71456e3e2cd5b32f63f",
				type: "POST",
				data: JSON.stringify(data), // json 을 string으로 변환하여 전송
				dataType: "JSON",
				contentType: "application/json",
				accept: "application/json",
				success: function(result) {

					if(result.data[0]['b_stt_cd'] === '01') {
						// 정상처리시
						$('#bn_check_info').text('사업자등록번호가 정상적으로 인증되었습니다.').css('color', 'blue');
						$('#bn_check_error').hide();
						$('#msg_mb_id').text('');
						$('[name="id_check"]').val(result.data[0]['b_no']);
						$('#btn_id_check').text('인증완료');
					}else if(result.data[0]['b_stt_cd'] === '02' || result.data[0]['b_stt_cd'] === '03' || result.data[0]['b_stt_cd'] === '04') {

						var status_text = '';
						if(result.data[0]['b_stt_cd'] === '03') {
							status_text = '폐업';
						} else if(result.data[0]['b_stt_cd'] === '02') {
							status_text = '휴업';
						} else if(result.data[0]['b_stt_cd'] === '04') {
							status_text = '말소';
						}
						$('#msg_mb_id').text('사업자상태코드가 ' + status_text + ' 입니다.').css('color', 'red');
						$('#bn_check_info').hide();
						$('#bn_check_error').hide();
					} else {
						// 정상처리가 아닐시
						$('#msg_mb_id').text(result.data[0]['tax_type']);
						$('#bn_check_info').hide();
						$('#bn_check_error').show();
					}
				},
				error: function(result) {
					console.log(result.responseText); //responseText의 에러메세지 확인
				}
			});
		}
		//변경시 id_check 초기화
		$(function() {
			$('#reg_mb_id').on('input', function() {
				$('#btn_id_check').text('인증하기');
				$('[name="id_check"]').val('');
				$('#bn_check_info').show();
				$('#bn_check_error').hide();
				$('#msg_mb_id').text('');
			});
		});

		</script>
</div>