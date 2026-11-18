<?php
exit;
?>

<head>
<meta charset="utf-8">
<meta name="viewport" id="meta_viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">
<title>마켓채널</title>
<link rel="stylesheet" href="http://market-channel.com/theme/basic/css/mobile_shop.css?ver=2303229">
<link rel="stylesheet" href="http://market-channel.com/css/common.css?ver=260526">

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
// logSave($headers);

    // Android
?><br>

<input name= "1">
<a href="javascript:getAppToken();">푸시 토큰 동기화11</a><br><br>
<a href="javascript:setPushToken();">푸시 토큰 동기화11</a><br><br>

<a href="/">---home---</a><br><br>
<button onclick="requestCamera()">카메라 열기</button>
<img id="preview" style="width: 100%;" />
<input type="file" name="upload">


<script>
    // getAppToken();
</script>
