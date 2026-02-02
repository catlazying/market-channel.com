<?php
// market-channel.com//plugin/barobill/GetBankAccountTrans.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

// $BaroService_URL = 'https://testws.baroservice.com/BANKACCOUNT.asmx?WSDL';    //테스트베드용
// $CERTKEY = '1640DB58-6D1A-463B-9D7B-B5273DE67F83';
// $StartDate = "20251129";
// $EndDate = "20251130";
/*
$BaroService_URL = 'https://ws.baroservice.com/BANKACCOUNT.asmx?WSDL';		//실서비스용
$CERTKEY = 'C206B6E4-47C7-4640-B822-DB32EC75EF7D';
$StartDate = date("Ymd",strtotime("-1day"));
$EndDate = date("Ymd");
$BaroService_BANKACCOUNT = new SoapClient($BaroService_URL, array(
    'trace' => 'true',
    'encoding' => 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
));
*/
// ---------------------------------------------------------------------------------------------------
// API 레퍼런스 : https://dev.barobill.co.kr/docs/references/계좌조회-API#GetDailyBankAccountTransLog
// ---------------------------------------------------------------------------------------------------

$CorpNum = '1078771715';
$ID = 'tldpqj4939-';
$BankAccountNum = '99159456054';

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

if ($Result->CurrentPage < 0) { // 실패
    // echo $Result->CurrentPage;
    // echo "None Result.\nend.";
} else { // 호출 성공
    if (!is_array($Result->BankAccountLogList->BankAccountTransLog)) {
        $BankAccountLogs = [$Result->BankAccountLogList->BankAccountTransLog];
    } else {
        $BankAccountLogs = $Result->BankAccountLogList->BankAccountTransLog;
    }

    foreach ($BankAccountLogs as $BankAccountLog) {
        // print_r($BankAccountLog);
        $Deposit = $BankAccountLog->Deposit; // 입금액
        $TransRemark = $BankAccountLog->TransRemark1; // 입금자명
        $TransRefKey = $BankAccountLog->TransRefKey; // 결제키

        /// 결제완료 처리
        $sql = "select od_id from {$g5['g5_shop_order_table']} where od_deposit_name = '$TransRemark' and od_cart_price = '$Deposit' and od_status = '주문' ";
        $row = sql_fetch($sql);
        $tmp_cart_id = $row['od_id'];

        if(!empty($tmp_cart_id)){
            // 주문금액이 상이함
            $sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as od_price,
                        COUNT(distinct it_id) as cart_count
                        from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' ";
            $row = sql_fetch($sql);
            $tot_ct_price = $row['od_price'];
            $cart_count = $row['cart_count'];

            if($Deposit==$tot_ct_price) {
                // 장바구니 상태변경
                $sql = "update {$g5['g5_shop_cart_table']} set ct_status = '입금' , ct_point = '0' where od_id = '$tmp_cart_id' and ct_select = '1' ";
                sql_query($sql);

                // 주문서에 입금시 update
                $od_shop_memo = "\n".date("Y-m-d H:i:s")." 결제확인 [".$TransRefKey."]\n";
                $sql = "update {$g5['g5_shop_order_table']} set od_receipt_price = od_misu, od_misu = 0, od_receipt_time = '".G5_TIME_YMDHIS."', od_status = '입금', od_shop_memo = CONCAT(od_shop_memo, '$od_shop_memo') where od_id = '$tmp_cart_id' ";
                sql_query($sql);
            }
        }
    }
}


#end of file