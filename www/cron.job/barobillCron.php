#!/usr/local/php73/bin/php
<?php
    ob_start();   // 🔥 출력 버퍼 시작
$_SERVER['DOCUMENT_ROOT'] = "/home/hosting_users/seaever/www";
$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
$_SERVER['HTTP_USER_AGENT'] = "shell";
$_SERVER['SERVER_NAME'] = "market-channel.com";
$_SERVER['HTTPS'] = "on";

include($_SERVER["DOCUMENT_ROOT"].'/common.php');

// $BaroService_URL = 'https://testws.baroservice.com/BANKACCOUNT.asmx?WSDL';    //테스트베드용
// $CERTKEY = '1640DB58-6D1A-463B-9D7B-B5273DE67F83';
$BaroService_URL = 'https://ws.baroservice.com/BANKACCOUNT.asmx?WSDL';		//실서비스용
$CERTKEY = 'C206B6E4-47C7-4640-B822-DB32EC75EF7D';
$StartDate = date("Ymd");
$EndDate = date("Ymd");
$BaroService_BANKACCOUNT = new SoapClient($BaroService_URL, array(
    'trace' => 'true',
    'encoding' => 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
));
// ---------------------------------------------------------------------------------------------------
// API 레퍼런스 : https://dev.barobill.co.kr/docs/references/계좌조회-API#GetDailyBankAccountTransLog
// ---------------------------------------------------------------------------------------------------

$CorpNum = '1078771715';
$ID = 'tldpqj4939-';
$BankAccountNum = '22060104349054';

$TransDirection = 2;
$CountPerPage = 10;
$CurrentPage = 1;
$OrderDirection = 2;

$Result = $BaroService_BANKACCOUNT->GetPeriodBankAccountTransLog([
    'CERTKEY' => $CERTKEY,
    'CorpNum' => $CorpNum,
    'ID' => $ID,
    'BankAccountNum' => $BankAccountNum,
    'StartDate' => $StartDate,
    'EndDate' => $EndDate,
    'TransDirection' => $TransDirection,
    'CountPerPage' => $CountPerPage,
    'CurrentPage' => $CurrentPage,
    'OrderDirection' => $OrderDirection,
])->GetPeriodBankAccountTransLogResult;

// print_r($Result);
// logSave(date("Y-m-d H:i:s")." : ".json_encode($Result),'barobillCron');

if ($Result->CurrentPage < 0) { // 실패
    // echo $Result->CurrentPage;
    // echo "None Result.\nend.";
} else { // 호출 성공
    $BankAccountLogs = array();
    if (isset($Result->BankAccountLogList) && isset($Result->BankAccountLogList->BankAccountTransLog)) {
        if (!is_array($Result->BankAccountLogList->BankAccountTransLog)) {
            $BankAccountLogs = array($Result->BankAccountLogList->BankAccountTransLog);
        } else {
            $BankAccountLogs = $Result->BankAccountLogList->BankAccountTransLog;
        }
    }
    // logSave("\n\n".date("Y-m-d H:i:s")." ------- ",'barobillCron');
    logSave(json_encode($BankAccountLogs),'barobillCron');
    // print_r($BankAccountLogs);

    foreach ((array)$BankAccountLogs as $BankAccountLog) {
        // print_r($BankAccountLog);
        $Deposit = $BankAccountLog->Deposit; // 입금액
        $TransRemark = $BankAccountLog->TransRemark1; // 입금자명
        $TransRefKey = $BankAccountLog->TransRefKey; // 결제키

        /// 결제완료 처리
        $sql = "select od_id, mb_id from {$g5['g5_shop_order_table']} where od_deposit_name = '$TransRemark' and od_cart_price = '$Deposit' and od_status = '주문' ";
        $row = sql_fetch($sql);
        $tmp_cart_id = $row['od_id'];
        $tmp_mb_id = $row['mb_id'];

        if(!empty($tmp_cart_id)){
            $sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as od_price,
                        COUNT(distinct it_id) as cart_count
                        from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' ";
            $row = sql_fetch($sql);
            $tot_ct_price = $row['od_price'];
            $cart_count = $row['cart_count'];

            if($Deposit==$tot_ct_price) {
                // 장바구니 상태변경
                $sql = "update {$g5['g5_shop_cart_table']} set ct_status = '입금' , ct_point = '0' where od_id = '$tmp_cart_id' and ct_select = '1'";
                sql_query($sql);

                // 주문서에 입금시 update
                $od_shop_memo = "\n".date("Y-m-d H:i:s")." 결제확인 [".$TransRefKey."]\n";
                $sql = "update {$g5['g5_shop_order_table']} set od_receipt_price = od_misu, od_misu = 0, od_receipt_time = '".G5_TIME_YMDHIS."', od_status = '입금', od_shop_memo = CONCAT(od_shop_memo, '$od_shop_memo') where od_id = '$tmp_cart_id'";
                sql_query($sql);

                //notification_send // 배송완료 주문완료  입금확인
                $send_type = '입금확인';
                $user_id = $tmp_mb_id;
                $mb_nick = get_member($user_id, 'mb_nick');
                $prodName = array();
                $sql = " select it_name,ct_qty from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' ";
                $qry = sql_query($sql);
                while($val = sql_fetch_array($qry)) {
                    $prodName[] = $val['it_name']." ".$val['ct_qty']." / ";
                }
                $data = [
                    '{{주문번호}}' => $tmp_cart_id,
                    '{{상호명}}' => isset($mb_nick['mb_nick']) ? $mb_nick['mb_nick'] : '',
                    '{{상품명}}' => implode(",", (array)$prodName)
                ];
                notification_send($send_type ,$user_id ,$data);
            }
        }
    }
}

$html = ob_get_contents(); // 또는 ob_get_clean()
ob_end_clean();            // 버퍼 비우고 출력 안 함

if(!empty($html)){
    logSave("end-".date("Y-m-d H:i:s"),'barobillCron');
    logSave($html,'barobillCron');
}
#end of file

// echo "-end\n";