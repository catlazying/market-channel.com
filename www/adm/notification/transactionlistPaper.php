<?
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/commonSet.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/dbConnect.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/commonFunc.php');

// topmanager.co.kr/API/notification.php
/// 페텔 정보 저장..
$fixApiKey = "a8ac50f8115aa34f419d56a61fdd8d50";
$code = "0";
$postJson = file_get_contents("php://input");

if(!empty($postJson)){
    $postData = json_decode($postJson,true);
//    $apiKey = valueChk($postData['apiKey']);
//}else{
//    $apiKey = "";
}

//if(empty($apiKey)){
//    $apiKey = valueChk($_POST['apiKey']);
//    $postData = $_POST;
//}

//if($apiKey != $fixApiKey ){
//    $msg = "잘못된 API 값입니다.";
//    print json_encode(array("code" => $code, "msg" => $msg));
//    exit;
//}

function pushSave($postData){
    global $notificationTable, $notificationCronTable;
    $fcm_device = $postData["device"];    // 전송기기 android,ios
    $sender     = $postData["sender"];    // 전송사이트		PETEL / HOSTCENTER
    $sendType   = $postData["sendType"];  // 알림분류		RESERVE / CANCEL / CHECKIN_01
    $user_token = $postData["token"];     // 개인토큰
    $userId     = $postData["userId"];    // 아이디
    $userName   = $postData["userName"];  // 예약자명
    $reservationTime  = $postData["reservationTime"]; // 예약발송시간
    $bodyJson   = $postData["bodyJson"];  // 내용JSON
    $fcm_link   = $bodyJson['link'];      // 링크
    $fcm_image  = $bodyJson['image'];     // 이미지

    if(empty($reservationTime)) $reservationTime = date("Y-m-d H:i:s");
    // notificationTable 내용가져오기
        $addParam = "*";
        $addWhere = "fcm_target like '%\"$sender\"%' AND send_type='$sendType' AND viewYn='Y' ";
        $selRow = procDb("ONEROW", $notificationTable,$addParam,$addWhere);
    $fcm_title = $selRow['fcm_title'];
    $fcm_message = $selRow['fcm_message'];

    $fcm_title = str_replace('${userName}',$userName,$fcm_title);
    $fcm_message = str_replace('${userName}',$userName,$fcm_message);

    if(is_array($bodyJson)){
        foreach($bodyJson as $k => $v ){
            $fcm_title = str_replace('${'.$k.'}',$v,$fcm_title);
            $fcm_message = str_replace('${'.$k.'}',$v,$fcm_message);
        }
    }

    $res = array();
    ///  notificationCronTable 저장
    $addParam = "*";
    $addWhere = " fcm_sender ='$sender' AND send_type='$sendType' AND userId ='$userId' AND viewYn='Y' ";
    $selRow = procDb("ONEROW", $notificationCronTable,$addParam,$addWhere);
    if(empty($selRow['idx'])){
        $addParamAry = array();
        $addParamAry[] = " ecnt =0 ";
        $addParamAry[] = " fcm_device ='$fcm_device' ";
        $addParamAry[] = " fcm_sender ='$sender' ";
        $addParamAry[] = " fcm_title ='$fcm_title' ";
        $addParamAry[] = " fcm_message ='$fcm_message' ";
        $addParamAry[] = " send_type ='$sendType' ";
        $addParamAry[] = " reservationTime ='$reservationTime' ";
        if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
        if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
        if(!empty($user_token )){ $addParamAry[] = " user_token ='$user_token' "; }
        if(!empty($userId )){ $addParamAry[] = " userId ='$userId' "; }
        $saveIdx = 0;
        if(!empty($user_token) && is_array($addParamAry)){
        // if( is_array($addParamAry)){
            $addParams = ",".implode(",",$addParamAry);
            $addParam = "rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW(), viewYn='Y', send_date='N' ".$addParams;
            $addWhere = "";
            $saveIdx = procDb("INSERT", $notificationCronTable,$addParam,$addWhere);
        }
        // 저장이후에 발송
        if($saveIdx > 0){
            $diractMode="pushListRC";
            include_once($_SERVER['DOCUMENT_ROOT'].'/INC/notificationCronAjax.php');
            include_once($_SERVER['DOCUMENT_ROOT'].'/admin/notification/pushSendPetelFunc.php');
            $resAry = json_decode($result,true);

            // 발송 데이타
            if (is_array($resAry)){
                foreach ($resAry as $k => $v){
                    // echo $k ."=>". $v."<br>";
                    $_deviceToken = trim($v['user_token']);
                    $_title = trim($v['fcm_title']);
                    $_contents = trim($v['fcm_message']);
                    $_goUrl = trim($v['fcm_link']);
                    $_imgUrl = trim($v['fcm_image']);
                    $accessToken = getGoogleOAuthToken();
                    list($httpCode, $response) = sendFCMMessage($accessToken, $_deviceToken, $_title, $_contents, $_imgUrl, $_goUrl,$_deviceToken);

                    if ($httpCode == "200"){
                        $send_date_val = ",send_date='".date("Y-m-d H:i:s")."'";
                    }else{
                        $send_date_val = "";
                    }
                    $response = str_replace(array("\n","\r"),"",$response);
                    $addParam = " mdatetime=NOW(),ecnt=ecnt+1, fcm_result='".$response."'".$send_date_val;
                    $addWhere = "idx=".$v['idx'];
                    procDb("UPDATE", $notificationCronTable,$addParam,$addWhere);
                }
            }
        }
        // 결과
        $res['userId'] = $userId;
        $res['title']  = $fcm_title;
        $res['body']   = $fcm_message;
        $res['image']  = $fcm_image;
        $res['link']   = $fcm_link;
        $res['error']  = "N";
    }else{
        /// 결과
        $res['error'] = "Y";
    }
    return json_encode($res);
}

function pushEventSave($postData){
    global $notificationCronTable;
    $sender      = $postData["sender"];    // 전송사이트		PETEL / HOSTCENTER
    $sendType    = $postData["sendType"];  // 알림분류		RESERVE / CANCEL / CHECKIN_01
    $fcm_title   = $postData["title"];     // title
    $fcm_message = $postData["content"];    // content
    $fcm_link    = $postData['link'];      // 링크
    $fcm_image   = $postData['image'];     // 이미지
    $bodyJson    = $postData["bodyJson"];  // 내용JSON
    $reservationTime  = $postData["reservationTime"]; // 예약발송시간
    if(empty($reservationTime)) $reservationTime = date("Y-m-d H:i:s");

    if (is_array($bodyJson)) {

        foreach ($bodyJson as $value) {
             $userId = $value['userId']; // 예약자 아이디
             $user_token = $value['token']; // 개인토큰
             $fcm_device = $value['device']; // 전송기기 android,ios

             if (!empty($user_token)) {
                $res = array();
                $addParamAry = array();
                $addParamAry[] = " ecnt =0 ";
                $addParamAry[] = " fcm_device ='$fcm_device' ";
                $addParamAry[] = " fcm_sender ='$sender' ";
                $addParamAry[] = " fcm_title ='$fcm_title' ";
                $addParamAry[] = " fcm_message ='$fcm_message' ";
                $addParamAry[] = " send_type ='$sendType' ";
                $addParamAry[] = " reservationTime ='$reservationTime' ";
                if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
                if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
                if(!empty($user_token )){ $addParamAry[] = " user_token ='$user_token' "; }
                if(!empty($userId )){ $addParamAry[] = " userId ='$userId' "; }

                if(!empty($user_token) && is_array($addParamAry)){
                   $addParams = ",".implode(",",$addParamAry);
                   $addParam = "rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW(), viewYn='Y', send_date='N' ".$addParams;
                   $addWhere = "";
                   $resIdx = procDb("INSERT", $notificationCronTable,$addParam,$addWhere);

                    // 저장이후에 발송 크론에서 안하고 직접발송함
                    if($resIdx > 0 && ($sendType=='RESERVE' || $sendType=='CANCEL' )){
                        $diractMode="pushListRC";
                        include_once($_SERVER['DOCUMENT_ROOT'].'/INC/notificationCronAjax.php');
                        include_once($_SERVER['DOCUMENT_ROOT'].'/admin/notification/pushSendPetelFunc.php');
                        $resAry = json_decode($result,true);
                        // 발송 데이타
                        if (is_array($resAry)){
                            foreach ($resAry as $k => $v){
                                // echo $k ."=>". $v."<br>";
                                $_fcm_device = trim($v['fcm_device']);
                                $_deviceToken = trim($v['user_token']);
                                $_title = trim($v['fcm_title']);
                                $_contents = trim($v['fcm_message']);
                                $_goUrl = trim($v['fcm_link']);
                                $_imgUrl = trim($v['fcm_image']);
                                $accessToken = getGoogleOAuthToken();
                                list($httpCode, $response) = sendFCMMessage($accessToken, $_deviceToken, $_title, $_contents, $_imgUrl, $_goUrl,$_fcm_device);

                                if ($httpCode == "200"){
                                    $send_date_val = ",send_date='".date("Y-m-d H:i:s")."'";
                                }else{
                                    $send_date_val = "";
                                }
                                $response = str_replace(array("\n","\r"),"",$response);
                                $addParam = " mdatetime=NOW(),ecnt=ecnt+1, fcm_result='".$response."'".$send_date_val;
                                $addWhere = "idx=".$v['idx'];
                                procDb("UPDATE", $notificationCronTable,$addParam,$addWhere);
                            }
                        }
                    }

                }
                // 결과
             }
        }
    }
    $result = array();
    if (empty($resIdx)){
        $result['error'] = "Y";
    }else{
        $code = "1";
        $result['error'] = "N";
    }
    return json_encode($result);
}

    if (is_array($postData)) {
        foreach ($postData as $value) {
            if($value['apiKey'] != $fixApiKey ){
                $msg = "잘못된 API 값입니다.";
                print json_encode(array("code" => $code, "msg" => $msg));
                exit;
            }
            $result = pushEventSave($value);
        }
    }

// $postDatas = '{"sender":"PETEL",
// "sendType":"RESERVE",
// "token":"개인토큰",
// "userId":"아이디",
// "userName":"예약자명",
// "bodyJson":{
//     "roomName":"객실명",
//     "booNo":"예약번호",
//     "stayDay":"박수",
//     "stayDate":"숙박기간",
//     "image":"이미지",
//     "link":"링크"}
// }';
/* 이벤트
    apiKey,
    sender,
    sendType,
    title,
    content,
    image,
    link,
    bodyJson [
        {
            userId,
            token
        },
        {
            userId,
            token
        }, ....
    ]
*/
// // echo $postDatas;
// $postData = json_decode($postDatas,true);
//EVENT
// if($postData["sendType"] == "EVENT"){
//    $result = pushEventSave($postData);
// }else{
//     $result = pushSave($postData);
// }

print $result;