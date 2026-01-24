<head>
<meta charset="utf-8">
<meta name="viewport" id="meta_viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">
<title>마켓채널</title>
<link rel="stylesheet" href="http://market-channel.com/theme/basic/css/mobile_shop.css?ver=2303229">
<link rel="stylesheet" href="http://market-channel.com/css/common.css?ver=2303229">
<link rel="stylesheet" href="http://market-channel.com/css/global.css?ver=2303229">
<link rel="stylesheet" href="http://market-channel.com/js/font-awesome/css/font-awesome.min.css?ver=2303229">
<link rel="stylesheet" href="http://market-channel.com/mobile/skin/shop/basic/style.css?ver=2303229">
<link rel="stylesheet" href="http://market-channel.com/js/owlcarousel/owl.carousel.min.css?ver=2303229">
<!--[if lte IE 8]>
<script src="http://market-channel.com/js/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "http://market-channel.com";
var g5_bbs_url   = "http://market-channel.com/bbs";
var g5_is_member = "";
var g5_is_admin  = "";
var g5_is_mobile = "1";
var g5_bo_table  = "";
var g5_sca       = "";
var g5_editor    = "";
var g5_cookie_domain = "";
var g5_theme_shop_url = "http://market-channel.com/theme/basic/shop";
var g5_shop_url = "http://market-channel.com/shop";
</script>
<script src="http://market-channel.com/js/jquery.min.js?ver=2304171"></script>
<script src="http://market-channel.com/js/common.js?ver=2304171"></script>
<script src="http://market-channel.com/js/wrest.js?ver=2304171"></script>
<script src="http://market-channel.com/js/placeholders.min.js?ver=2304171"></script>
<script src="http://market-channel.com/js/global.js?ver=2304171"></script>
<script src="http://market-channel.com/js/modernizr.custom.70111.js?ver=2304171"></script>
<script src="http://market-channel.com/js/owlcarousel/owl.carousel.min.js?ver=2304171"></script>
<script src="http://market-channel.com/js/shop.list.action.js?ver=2304171"></script>
</head>
<script>
    // 1. 앱에 토큰 요청
    function getAppToken() {
        if (window.AndroidApp) {
            // 안드로이드
            window.AndroidApp.requestToken();
            alert("1");
        } else if (window.webkit && window.webkit.messageHandlers.iosApp) {
            // iOS: 반드시 postMessage() 형식을 사용해야 함
            window.webkit.messageHandlers.iosApp.postMessage("requestToken");
            alert("2");
        }
    }

    window.setPushToken = function(token) {
        // console.log("앱으로부터 토큰 받음:", token);
		// 토큰을 jQuery ajax로 /plugin/etc/tokenProc.php 에 전송
		alert(token);
		if (token) {
			$.ajax({
				url: '/plugin/etc/tokenProc.php',
				type: 'POST',
				data: { action:"create",token: token },
				success: function(response) {
					// 필요시 응답 처리
					console.log(response);
                    alert(response);
				}
			});
		}
    };


    // window.setPushToken = function(token) {
    //     console.log("앱으로부터 토큰 받음:", token);
    //     alert("받은 토큰: " + token); // 테스트를 위해 alert 추가
    // };


    // 1. 네이티브 카메라 호출
    function requestCamera() {
        if (window.webkit && window.webkit.messageHandlers.openCamera) {
            window.webkit.messageHandlers.openCamera.postMessage("");
        } else {
            alert("Native 앱 환경이 아닙니다.");
        }
    }

    // 2. 네이티브에서 촬영 완료 후 호출할 콜백 함수
    window.onImageCaptured  = function (base64Data) {
        // alert("----onImageCaptured--");
        // alert(base64Data);
        const imageElement = document.getElementById('preview');
        imageElement.src = "data:image/jpeg;base64," + base64Data;

        // 서버로 전송하거나 폼 데이터에 추가 가능
        // console.log("이미지 수신 완료");
    }
</script>
<?
    $headers = getallheaders();
    echo "<pre>";
    print_r($headers['user-agent']);


function logSave($saveTxt){
    $filename = $_SERVER["DOCUMENT_ROOT"]."/data/log/log".date("Ymd");

    $fp = fopen($filename, "a+");
    fwrite($fp, $saveTxt."\n");
    fclose($fp);
}
logSave($headers);

    // Android
?><br>
<a href="javascript:getAppToken();">푸시 토큰 동기화11</a><br><br>
<a href="javascript:setPushToken();">푸시 토큰 동기화11</a><br><br>

<a href="/">---home---</a><br><br>
<button onclick="requestCamera()">카메라 열기</button>
<img id="preview" style="width: 100%;" />
<input type="file" name="upload">


<script>
    // getAppToken();
</script>
<?php



exit;
include_once('./_common.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');

$g5['title'] = '영카트4 DB 데이터 이전';
include_once(G5_PATH.'/head.sub.php');

if(get_session('yc4_tables_copied') == 'done')
    alert('DB 데이터 변환을 이미 실행하였습니다. 중복 실행시 오류가 발생할 수 있습니다.', G5_URL);

if($is_admin != 'super')
    alert('최고관리자로 로그인 후 실행해 주십시오.', G5_URL);
?>

<style>
#g4_import p {padding:0 0 10px;line-height:1.8em}
#g4_import_frm {margin:20px 0 30px;padding:30px 0;border:1px solid #e9e9e9;background:#f5f8f9;text-align:center}
#g4_import_frm .frm_input {background-color:#fff !important}
#g4_import_frm .btn_submit {padding:0 10px;height:24px}
</style>

<!-- 상단 시작 { -->
<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div id="skip_to_container"><a href="#container">본문 바로가기</a></div>

    <div id="hd_wrapper">

        <div id="logo">
            <a href="<?php echo G5_URL ?>"><img src="<?php echo G5_IMG_URL ?>/logo.jpg" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

        <fieldset id="hd_sch">
            <legend>사이트 내 전체검색</legend>
            <form name="fsearchbox" method="get" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
            <input type="hidden" name="sfl" value="wr_subject||wr_content">
            <input type="hidden" name="sop" value="and">
            <label for="sch_stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
            <input type="text" name="stx" id="sch_stx" maxlength="20">
            <input type="submit" id="sch_submit" value="검색">
            </form>

            <script>
            function fsearchbox_submit(f)
            {
                var stx = f.stx.value.trim();
                if (stx.length < 2) {
                    alert("검색어는 두글자 이상 입력하십시오.");
                    f.stx.select();
                    f.stx.focus();
                    return false;
                }

                // 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
                var cnt = 0;
                for (var i = 0; i < stx.length; i++) {
                    if (stx.charAt(i) == ' ')
                        cnt++;
                }

                if (cnt > 1) {
                    alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
                    f.stx.select();
                    f.stx.focus();
                    return false;
                }
                f.stx.value = stx;

                return true;
            }
            </script>
        </fieldset>

        <ul id="tnb">
            <?php if ($is_member) {  ?>
            <?php if ($is_admin) {  ?>
            <li><a href="<?php echo G5_ADMIN_URL ?>"><b>관리자</b></a></li>
            <?php }  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">정보수정</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/logout.php">로그아웃</a></li>
            <?php } else {  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/login.php"><b>로그인</b></a></li>
            <?php }  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/qalist.php">1:1문의</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/current_connect.php">접속자 <?php echo connect(); // 현재 접속자수  ?></a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/new.php">새글</a></li>
        </ul>

        <div id="text_size">
            <!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
            <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?php echo G5_URL; ?>/img/ts01.gif" alt="기본"></button>
            <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?php echo G5_URL; ?>/img/ts02.gif" alt="크게"></button>
            <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?php echo G5_URL; ?>/img/ts03.gif" alt="더크게"></button>
        </div>
    </div>

    <hr>

    <nav id="gnb">
        <h2>메인메뉴</h2>
        <ul id="gnb_1dul">
            <li class="gnb_empty">메뉴는 표시하지 않습니다.</li>
        </ul>
    </nav>
</div>
<!-- } 상단 끝 -->

<hr>

<!-- 콘텐츠 시작 { -->
<div id="wrapper">
    <div id="aside">
        <?php // echo outlogin('basic'); // 외부 로그인  ?>
    </div>
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?php echo $g5['title'] ?></div><?php } ?>

        <div id="g4_import">
            <p>
                이 프로그램은 영카트5 설치 후 바로 실행하셔야만 합니다.<br>
                만약 영카트5 사이트를 운영 중에 이 프로그램을 실행하시면 DB 데이터가 망실되거나 데이터의 오류가 발생할 수 있습니다.<br>
                또한 중복해서 실행하실 경우에도 DB 데이터의 오류가 발생할 수 있으니 반드시 한번만 실행해 주십시오.
            </p>
            <p>프로그램을 실행하시려면 영카트4의 config.php 파일 경로를 입력하신 후 확인을 클릭해 주십시오.</p>

            <form name="fimport" method="post" action="./yc4_import_run.php" onsubmit="return fimport_submit(this);">
            <div id="g4_import_frm">
                <label for="file_path">config.php 파일 경로</label>
                <input type="text" name="file_path" id="file_path" required class="frm_input required">
                <input type="submit" value="확인" class="btn_submit">
            </div>
            </form>

            <p>
                경로는 영카트5 설치 루트를 기준으로 영카트4의 config.php 파일의 상대경로입니다.<br>
                예를 들어 영카트4를 웹루트에 설치하셨고 영카트5를 yc5라는 하위 폴더에 설치하셨다면 입력하실 경로는 ../config.php 입니다.
            </p>

        </div>

        <script>
        function fimport_submit(f)
        {
            return confirm('영카트4의 DB 데이터를 이전하시겠습니까?');
        }
        </script>

    </div>
</div>

<!-- } 콘텐츠 끝 -->

<hr>

<!-- 하단 시작 { -->
<div id="ft">
    <div id="ft_catch"><img src="<?php echo G5_IMG_URL; ?>/ft.png" alt="<?php echo G5_VERSION ?>"></div>
    <div id="ft_copy">
        <p>
            Copyright &copy; <b>소유하신 도메인.</b> All rights reserved.<br>
            <a href="#">상단으로</a>
        </p>
    </div>
</div>

<script>
$(function() {
    // 폰트 리사이즈 쿠키있으면 실행
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
dasXGSc3QZW9TGb3cWTyMQ:APA91bF3qHNdsqvX5jT6KZsam1Sx8yojNRb3M_yK_d9hJuTF6wAJXlfE94rE-ZkMnJp2FjJxpuGk55fqJXiYUMmmAnbTTx83acn-gcIto0LJUSkqLn4-dNA
2026-01-23 19:45:01.849 32261-32335 CameraManagerGlobal     app.com.marketchannel                I  Connect