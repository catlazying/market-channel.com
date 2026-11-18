<?php
include_once('./_common.php');
// od_id=2025122415371626%2C2025122415322847
$od_id = $_REQUEST['od_id'];

$od_id_list = array();
if ($od_id) {
    if (is_array($od_id)) {
        $od_id_list = $od_id;
    } else {
        $od_id_list = urldecode($od_id);
        $od_id_list = array_filter(explode(",", $od_id));
    }
}

// // echo $od_id_list;
// echo "<pre>";
$outAry = null;
$sql = "select * from g5_shop_order Where od_id in('".implode("','",$od_id_list)."') order by od_id desc ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    // print_r($row);
    // 주문번호에 - 추가
    switch(strlen($row['od_id'])) {
        case 16:
            $disp_od_id = substr($row['od_id'],0,8).'-'.substr($row['od_id'],8);
            break;
        default:
            $disp_od_id = substr($row['od_id'],0,6).'-'.substr($row['od_id'],6);
            break;
    }
    $invoice_date = '';
    // strtotime returns false for invalid date/time
    if (!is_null_time($row['od_invoice_time'])) {
        $ts = strtotime($row['od_invoice_time']);
        if ($ts !== false && $row['od_invoice_time'] != '0000-00-00 00:00:00' && $row['od_invoice_time'] != '') {
            $invoice_date = date("Y-m-d", $ts);
        }
    }

    $mbAry = get_member($row['mb_id']);

    // $outAry[$i]['od_date'] = $disp_od_id;
    $outAry[$i]['od_id'] = $disp_od_id;
    $outAry[$i]['od_price'] = $row['od_cart_price'];
    $outAry[$i]['od_memo'] = $row['od_memo'];
    $outAry[$i]['invoice_date'] = $invoice_date;

    // 사업자번호 형태 3-2-5로 나누기 (예: 674-16-00175)
    if (isset($mbAry['mb_id']) && preg_match('/^(\d{3})(\d{2})(\d{5})$/', $mbAry['mb_id'], $matches)) {
        $outAry[$i]['com_biznum'] = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    } else {
        $outAry[$i]['com_biznum'] = $mbAry['mb_id'];
    }
    $outAry[$i]['com_name'] = $mbAry['mb_nick'];
    $outAry[$i]['com_own'] = $mbAry['mb_name'];
    $outAry[$i]['com_hp'] = $mbAry['mb_hp'];
    $outAry[$i]['com_addr'] = $mbAry['mb_addr1']." ".$mbAry['mb_addr2'];


    // 상품 목록
    $itnameAry = array();
    $totalPrice = 0;
    $totalCount = 0;
    $totalTax = 0;
    $sql = " select C.*, I.it_info_value, I.it_notax from {$g5['g5_shop_cart_table']} C, {$g5['g5_shop_item_table']} I where C.od_id = '".$row['od_id']."' AND C.it_id=I.it_id order by ct_id ";
    $results = sql_query($sql);
    for($s=0; $rows=sql_fetch_array($results); $s++) {
        $infoArr = @unserialize($rows['it_info_value']);
        if ($infoArr && is_array($infoArr)) {
            $weight = isset($infoArr['weight']) ? $infoArr['weight'] : '';
            $size = isset($infoArr['size']) ? $infoArr['size'] : '';
        } else {
            $weight = '';
            $size = '';
        }

        // $itnameAry[$s]['time'] = date("m-d",strtotime($row['od_receipt_time'])); // 월일
        $itnameAry[$s]['time'] = date("m-d"); // 월일
        $itnameAry[$s]['name'] = $rows['it_name'] ." / ".$size; // 품목 및 규격
        $itnameAry[$s]['priceEa'] = number_format($rows['ct_price']); // 단가
        $itnameAry[$s]['qty'] = $rows['ct_qty']; // 수량
        $itnameAry[$s]['price'] = number_format( $rows['ct_price'] * $rows['ct_qty']); // 공급가액
        $itnameAry[$s]['weight'] = $weight;// 단위중량 상세무게.
        if($rows['it_notax']!='1'){
          $itnameAry[$s]['vat'] = number_format( ($rows['ct_price'] * $rows['ct_qty']) * 0.1);
        }
        $totalCount += $rows['ct_qty'];
        $totalPrice += $rows['ct_price'] * $rows['ct_qty'];
        if($rows['it_notax']!='1'){
          $totalTax += ($rows['ct_price'] * $rows['ct_qty']) * 0.1;
        }
    }
    $outAry[$i]['product'] = $itnameAry;
    $outAry[$i]['totalPrice'] = number_format($totalPrice);
    $outAry[$i]['totalCount'] = $totalCount;
    $outAry[$i]['totalTax'] = number_format( round($totalTax) );
    $outAry[$i]['totalAll'] = number_format( $totalPrice + $totalTax ); // 10% 추가

}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <title>거래명세서</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 0;
    }
    html, body {
      height: 100%;
      width: 100%;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      display: flex;
      justify-content: center; /* 중앙 정렬 */
      align-items: center;     /* 세로 중앙 정렬, 필요시 */
    }
    body {
      font-family: 'Malgun Gothic', Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
      width: 100vw;
      min-height: 100vh;
      height: auto !important;
      box-sizing: border-box;
      /* remove default print margin */
      display: flex;
      justify-content: center; /* 중앙 정렬 */
      align-items: flex-start; /* 상단 정렬, 필요시 center로 변경가능 */
    }
    .fullwrapper {
      width: 190mm; /* Safe width inside A4 (210mm - margin) */
      margin: 10mm auto;
      padding: 10mm 10mm 0 10mm;
      box-sizing: border-box;
      min-height: 277mm;
      display: block;
    }
    .wrapper, .wrapper2 {
      background: #fff;
      border: 2px solid #2f6bff;
      box-sizing: border-box;
    }
    .wrapper2 {
      border: 2px solid rgb(248, 55, 21);
    }

    h1, .wrapper2 h1 {
      text-align: center;
      margin: 0;
      color: #2f6bff;
    }
    .wrapper2 h1 {
      color: rgb(248, 55, 21);
    }
    .sub, .wrapper2 .sub {
      text-align: center;
      font-size: 13px;
      color: #2f6bff;
      margin-bottom: 1px;
    }
    .wrapper2 .sub {
      color: rgb(248, 55, 21);
    }

    .top-info, .wrapper2 .top-info {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      color: #2f6bff;
    }
    .wrapper2 .top-info {
      color: rgb(248, 55, 21);
    }
    .top-info div, .wrapper2 .top-info div {
      padding: 1px 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      page-break-inside: auto;
      background: white;
    }
    th, td {
      border: 1px solid #2f6bff;
      padding: 5px 6px;
      font-size: 12px;
    }
    th {
      background: #eef3ff;
      text-align: center;
    }
    .wrapper2 th {
      background: #ffeeef;
      border: 1px solid rgb(248, 55, 21);
    }
    .wrapper2 td {
      border: 1px solid rgb(248, 55, 21);
    }
    .label, .wrapper2 .label {
      width: 48px;
      background: #eef3ff;
      font-weight: 600;
    }
    .wrapper2 .label {
      background: #ffeeef;
    }
    .center { text-align: center; }
    .right { text-align: right; }
    .empty { height: 12px;  }

    .total-box, .wrapper2 .total-box {
      margin-top: 10px;
      border: 1px solid #2f6bff;
      padding: 7px;
      display: flex;
      justify-content: space-between;
      font-weight: 600;
      background: #fcfcff;
    }
    .wrapper2 .total-box {
      border: 1px solid rgb(248, 55, 21);
      background: #fff6f0;
    }
    .memo, .wrapper2 .memo {
      margin-top: 6px;
      border: 1px solid #2f6bff;
      padding: 5px;
      word-break: break-all;
      background: #fcfcff;
      font-size: 12px;
    }
    .wrapper2 .memo {
      border: 1px solid rgb(248, 55, 21);
      background: #fff6f0;
    }

    @media print {
      html, body {
        width: 210mm;
        min-height: 297mm;
        background: none !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-sizing: border-box;
        display: block;
        justify-content: initial;
        align-items: initial;
      }
      .fullwrapper {
        margin: 0 auto;
        padding: 10mm 7mm 0 7mm;
        width: 190mm;
        min-height: 277mm;
        display: block;
      }
      .wrapper, .wrapper2 {
        overflow: hidden;
      }
      .empty {
        page-break-inside: avoid;
      }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    /* Prevent table split at awkward places */
    tr, th, td {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
  </style>


  </style>
</head>
<body>
<?php
    if(is_array($outAry)){
        foreach ($outAry as $row){
?>
<div class="fullwrapper">
<div class="wrapper2">
  <div class="top-info">
    <div></div>
    <div>일자 : <?php echo $row['invoice_date'] ?>&nbsp;&nbsp; No : <?php echo $row['od_id'] ?></div>
  </div>

  <h1>거래명세표</h1>
  <div class="sub">(공급자용)</div>

  <!-- 공급자 / 공급받는자 -->
  <table>
    <tr>
      <th colspan="4">공급자</th>
      <th colspan="4">공급 받는 자</th>
    </tr>
    <tr>
      <td class="label">등록번호</td>
      <td colspan="3"><?php echo $default['de_admin_company_saupja_no'] ?></td>
      <td class="label">등록번호</td>
      <td colspan="3"><?php echo $row['com_biznum'] ?></td>
    </tr>
    <tr>
      <td class="label">상호</td>
      <td><?php echo $default['de_admin_company_name'] ?></td>
      <td class="label">성명</td>
      <td><?php echo $default['de_admin_company_owner'] ?></td>
      <td class="label">상호</td>
      <td><?php echo $row['com_name'] ?></td>
      <td class="label">성명</td>
      <td><?php echo $row['com_own'] ?></td>
    </tr>
    <tr>
      <td class="label">주소</td>
      <td colspan="3"><?php echo $default['de_admin_company_addr'] ?></td>
      <td class="label">주소</td>
      <td colspan="3"><?php echo $row['com_addr'] ?></td>
    </tr>
    <tr>
      <td class="label">전화번호</td>
      <td><?php echo $default['de_admin_company_tel'] ?></td>
      <td class="label">FAX</td>
      <td><?php echo $default['de_admin_company_fax'] ?></td>
      <td class="label">전화번호</td>
      <td><?php echo $row['com_hp'] ?></td>
      <td class="label">FAX</td>
      <td> </td>
    </tr>
  </table>

  <!-- 품목 테이블 -->
  <table>
    <tr>
      <th>월일</th>
      <th>품목 및 규격</th>
      <th>수량</th>
      <th>단위중량</th>
      <th>단가</th>
      <th>공급가액</th>
      <th>부가세</th>
    </tr>
    <?php
        $prodCnt = count($row['product']);
        for($i=0;$i<6;$i++){
            $prodAry = $row['product'][$i];
            if(is_array($prodAry)){
    ?>
        <tr>
        <td class="center"><?php echo $prodAry['time'] ?></td>
        <td><?php echo $prodAry['name'] ?></td>
        <td class="center"><?php echo $prodAry['qty'] ?></td>
        <td class="center"><?php echo $prodAry['weight'] ?></td>
        <td class="right"><?php echo $prodAry['priceEa'] ?></td>
        <td class="right"><?php echo $prodAry['price'] ?></td>
        <td class="right"><?php echo $prodAry['vat'] ?></td>
        </tr>
    <?php
            }else if($prodCnt==$i){
    ?>
    <tr>
      <td class="center"></td>
      <td class="center"><strong>합계</strong></td>
      <td class="center"><strong><?php echo $row['totalCount'] ?></strong></td>
      <td></td>
      <td></td>
      <td class="right"><strong><?php echo $row['totalPrice'] ?></strong></td>
      <td class="right"><strong><?php echo $row['totalTax'] ?></strong></td>
    </tr>
            <?
            }else{
    ?>
        <tr><td class="empty"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    <?php
            }
        }
    ?>
  </table>

  <!-- 합계 영역 -->
  <div class="total-box">
    <div>합계</div>
    <div>
      공급가액 : <?php echo $row['totalPrice'] ?>원&nbsp;&nbsp;&nbsp;
      부가세액 : <?php echo $row['totalTax'] ?>원&nbsp;&nbsp;&nbsp;
      총액 : <?php echo $row['totalAll'] ?>원
    </div>
  </div>

  <!-- 메모 -->
  <div class="memo">
    <strong>메모</strong> : <?php echo $row['od_memo'] ?>
  </div>
</div>
<br>
<div class="wrapper">
  <div class="top-info">
    <div></div>
    <div>일자 : <?php echo $row['invoice_date'] ?>&nbsp;&nbsp; No : <?php echo $row['od_id'] ?></div>
  </div>

  <h1>거래명세표</h1>
  <div class="sub">(공급 받는 자)</div>

  <!-- 공급자 / 공급받는자 -->
  <table>
    <tr>
      <th colspan="4">공급자</th>
      <th colspan="4">공급 받는 자</th>
    </tr>
    <tr>
      <td class="label">등록번호</td>
      <td colspan="3"><?php echo $default['de_admin_company_saupja_no'] ?></td>
      <td class="label">등록번호</td>
      <td colspan="3"><?php echo $row['com_biznum'] ?></td>
    </tr>
    <tr>
      <td class="label">상호</td>
      <td><?php echo $default['de_admin_company_name'] ?></td>
      <td class="label">성명</td>
      <td><?php echo $default['de_admin_company_owner'] ?></td>
      <td class="label">상호</td>
      <td><?php echo $row['com_name'] ?></td>
      <td class="label">성명</td>
      <td><?php echo $row['com_own'] ?></td>
    </tr>
    <tr>
      <td class="label">주소</td>
      <td colspan="3"><?php echo $default['de_admin_company_addr'] ?></td>
      <td class="label">주소</td>
      <td colspan="3"><?php echo $row['com_addr'] ?></td>
    </tr>
    <tr>
      <td class="label">전화번호</td>
      <td><?php echo $default['de_admin_company_tel'] ?></td>
      <td class="label">FAX</td>
      <td><?php echo $default['de_admin_company_fax'] ?></td>
      <td class="label">전화번호</td>
      <td><?php echo $row['com_hp'] ?></td>
      <td class="label">FAX</td>
      <td> </td>
    </tr>
  </table>

  <!-- 품목 테이블 -->
  <table>
    <tr>
      <th>월일</th>
      <th>품목 및 규격</th>
      <th>수량</th>
      <th>단위중량</th>
      <th>단가</th>
      <th>공급가액</th>
      <th>부가세</th>
    </tr>
    <?php
        $prodCnt = count($row['product']);
        for($i=0;$i<6;$i++){
            $prodAry = $row['product'][$i];
            if(is_array($prodAry)){
    ?>
        <tr>
        <td class="center"><?php echo $prodAry['time'] ?></td>
        <td><?php echo $prodAry['name'] ?></td>
        <td class="center"><?php echo $prodAry['qty'] ?></td>
        <td class="center"><?php echo $prodAry['weight'] ?></td>
        <td class="right"><?php echo $prodAry['priceEa'] ?></td>
        <td class="right"><?php echo $prodAry['price'] ?></td>
        <td class="right"><?php echo $prodAry['vat'] ?></td>
        </tr>
    <?php
            }else if($prodCnt==$i){
    ?>
    <tr>
      <td class="center"></td>
      <td class="center"><strong>합계</strong></td>
      <td class="center"><strong><?php echo $row['totalCount'] ?></strong></td>
      <td></td>
      <td></td>
      <td class="right"><strong><?php echo $row['totalPrice'] ?></strong></td>
      <td class="right"><strong><?php echo $row['totalTax'] ?></strong></td>
    </tr>
            <?
            }else{
    ?>
        <tr><td class="empty"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    <?php
            }
        }
    ?>
  </table>

  <!-- 합계 영역 -->
  <div class="total-box">
    <div>합계</div>
    <div>
      공급가액 : <?php echo $row['totalPrice'] ?>원&nbsp;&nbsp;&nbsp;
      부가세액 : <?php echo $row['totalTax'] ?>원&nbsp;&nbsp;&nbsp;
      총액 : <?php echo $row['totalAll'] ?>원
    </div>
  </div>

  <!-- 메모 -->
  <div class="memo">
    <strong>메모</strong> : <?php echo $row['od_memo'] ?>
  </div>
</div>
</div>

<?php
        }
    }
?>

<script>
  window.onload = function() {
  setTimeout(function() {
    // 인쇄시 A4 가로 모드로 자동 전환
    const style = document.createElement("style");
    style.type = "text/css";
    style.media = "print";
    style.innerHTML = `
      @page { size: A4 portrait; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    `;
    document.head.appendChild(style);

    window.print();
  }, 300); // DOM 준비후 약간의 텀을 두고 실행
};
</script>


</body>
</html>
