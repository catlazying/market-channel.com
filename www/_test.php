<?
include_once("./_common.php");


// $BaroService_URL = 'https://ws.baroservice.com/BANKACCOUNT.asmx?WSDL';		//실서비스용
// $CERTKEY = 'C206B6E4-47C7-4640-B822-DB32EC75EF7D';
// $StartDate = date("Ymd",strtotime("-1day"));
// $EndDate = date("Ymd");
// $BaroService_BANKACCOUNT = new SoapClient($BaroService_URL, array(
//     'trace' => 'true',
//     'encoding' => 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
// ));


// // ---------------------------------------------------------------------------------------------------
// // API 레퍼런스 : https://dev.barobill.co.kr/docs/references/계좌조회-API#GetDailyBankAccountTransLog
// // ---------------------------------------------------------------------------------------------------
// $CorpNum = '1078771715';
// $ID = 'tldpqj4939-';
// $BankAccountNum = '22060104349054';

// $TransDirection = 2;
// $CountPerPage = 10;
// $CurrentPage = 1;
// $OrderDirection = 2;

// $Result = $BaroService_BANKACCOUNT->GetPeriodBankAccountTransLog([
//     'CERTKEY' => $CERTKEY,
//     'CorpNum' => $CorpNum,
//     'ID' => $ID,
//     'BankAccountNum' => $BankAccountNum,
//     'StartDate' => $StartDate,
//     'EndDate' => $EndDate,
//     'TransDirection' => $TransDirection,
//     'CountPerPage' => $CountPerPage,
//     'CurrentPage' => $CurrentPage,
//     'OrderDirection' => $OrderDirection,
// ])->GetPeriodBankAccountTransLogResult;

$od_id = "2026061813525901";

$sql = " select it_name,ct_qty from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and ct_select = '1' ";

echo $sql;

$qry = sql_query($sql);
while($val = sql_fetch_array($qry)) {
    $prodName[] = $val['it_name']." ".$val['ct_qty']." / ";
}

print_r($prodName);
exit;

// print_r($Result);
$user_id = "admin";
$mb_nick = get_member($user_id, 'mb_nick');


print_r($mb_nick);
echo $mb_nick['mb_nick'];

// 배송완료 주문완료  입금확인
$send_type = '주문완료';
$user_id = "admin";
$data = [
    '{{상호명}}' => '상호',
    '{{상품명}}' => '냉동'
];
notification_send2($send_type ,$user_id ,$data);


function notification_send2($send_type, $user_id, $dataAry){
    $sql = " select * from fcm_notification_set where send_type = '".$send_type."' ";
    $row = sql_fetch($sql);
    $send_type = "즉시";
    $fcm_title = strip_tags(clean_xss_attributes($row['fcm_title']));
    $fcm_message = $row['fcm_message'];
    $fcm_image = $row['fcm_image'];
    $fcm_link = $row['fcm_link'];
    $fcm_message = str_replace(array_keys($dataAry), array_values($dataAry), $fcm_message);

    echo $fcm_message;

    // $qry = sql_query("select * from fcm_notification_tokens where user_id ='".$user_id."' ");
    // while($res = sql_fetch_array($qry)) {
    //     $user_id = $res['user_id'];
    //     $user_token = $res['push_token'];
    //     $fcm_device = $res['platform'];
    //     if(!empty($user_id) && !empty($user_token)){

    //         $addParamAry = array();
    //         if(!empty($fcm_device )){ $addParamAry[] = " fcm_device ='$fcm_device' "; }
    //         if(!empty($fcm_title )){ $addParamAry[] = " fcm_title ='$fcm_title' "; }
    //         if(!empty($fcm_message )){ $addParamAry[] = " fcm_message ='$fcm_message' "; }
    //         if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
    //         if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
    //         if(!empty($user_token )){ $addParamAry[] = " user_token ='$user_token' "; }
    //         if(!empty($user_id )){ $addParamAry[] = " user_id ='$user_id' "; }
    //         if(!empty($send_type )){ $addParamAry[] = " send_type ='$send_type' "; }
    //         $addParams = ",".implode(",",$addParamAry);
    //         $sql = " insert fcm_notification_cron set rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW(), viewYn='Y', ecnt=0 ".$addParams;
    //         sql_query($sql);

    //     }
    // }
}


