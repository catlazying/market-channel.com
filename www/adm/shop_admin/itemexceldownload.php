<?php
$sub_menu = '400300';
include_once('./_common.php');

// 상품이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '1024M');

auth_check_menu($auth, $sub_menu, "w");

if ( ! function_exists('utf2euc')) {
    function utf2euc($str) {
        return iconv("UTF-8","cp949//IGNORE", $str);
    }
}
if ( ! function_exists('is_ie')) {
    function is_ie() {
        return isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false);
    }
}
// /adm/shop_admin/itemlist.php 의 검색조건을 적용
if (isset($sfl) && $sfl && !in_array($sfl, array('it_name','it_id','it_maker','it_brand','it_model','it_origin','it_sell_email'))) {
    $sfl = '';
}

$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
}

if ($sca != "") {
    $sql_search .= " $where (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
}

if ($sfl == "")  $sfl = "it_name";

$sql_common = " from {$g5['g5_shop_item_table']} a ,
                     {$g5['g5_shop_category_table']} b
               where (a.ca_id = b.ca_id";
if ($is_admin != 'super')
    $sql_common .= " and b.ca_mb_id = '{$member['mb_id']}'";
$sql_common .= ") ";
$sql_common .= $sql_search;

$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
if (!$row['cnt'])
    alert('다운로드 할 상품이 없습니다.');

if (!$sst) {
    $sst  = "it_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = " select a.* " . $sql_common . $sql_order;
$result = sql_query($sql);

include_once(G5_LIB_PATH.'/PHPExcel.php');

if(!function_exists('column_char')) {
    function column_char($i) {
        return PHPExcel_Cell::stringFromColumnIndex($i);
    }
}

$notice = array('* 상품코드, 기본분류, 상품명은 필수입력
* 상품코드는 중복되지 않아야 함
* 기본분류에는 쇼핑몰에서 사용 중인 분류의 코드를 입력해야함
* 상품설명에는 html 코드 입력가능
* 상품이미지에는 data/item 아래의 경로입력 예를들어 data/item/abcd/item.jpg 일 경우 abcd/item.jpg 만 입력
* 이 설명과 아래 상품코드, 기본분류 등의 텍스틑 삭제해서는 안됨'
, '', '', '', '', '', '', '', '판매 가능:1, 중지:0','', '', '', '', '', '', '과세1,비과세0'
);

$headers = array(
    '상품코드', '기본분류', '2차분류', '3차분류', '상품명',
    '시중가격', '판매가격', '매입가격',
    '판매여부',
    '판매가격2', '판매가격3', '판매가격4', '판매가격5', '판매가격6', '판매가격7', '판매가격8', '판매가격9',
    '원산지', '기본설명', '재고수량', '재고통보수량', '최소구매수량', '최대구매수량', '과세유무', '출력순서',
    '제조사', '브랜드', '모델',
    '상품유형1', '상품유형2', '상품유형3', '상품유형4', '상품유형5', '실시간가격변동', '여분1', '여분2',
    '상품설명', '모바일상품설명',
    '전화문의', '포인트', '포인트타입', '판매자 e-mail',
    '이미지1', '이미지2', '이미지3', '이미지4', '이미지5',
    '이미지6', '이미지7', '이미지8', '이미지9', '이미지10'

);

$widths  = array(15, 10, 10, 10, 30, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 8, 8, 8, 8, 8, 8, 8, 8, 20, 40, 40, 12, 12, 12, 10, 10, 10, 20, 8, 10, 10, 10, 10, 8, 8, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20);
$header_bgcolor = 'FFABCDEF';
$last_char = column_char(count($headers) - 1);
$rows = array();

for ($i=1; $row=sql_fetch_array($result); $i++) {
    $rows[] = array(
        ' '.$row['it_id'],
        $row['ca_id'],
        $row['ca_id2'],
        $row['ca_id3'],
        $row['it_name'],
        $row['it_cust_price'],
        $row['it_price'],
        $row['it_purchase_price'],
        $row['it_use'],
        $row['it_price_2'],
        $row['it_price_3'],
        $row['it_price_4'],
        $row['it_price_5'],
        $row['it_price_6'],
        $row['it_price_7'],
        $row['it_price_8'],
        $row['it_price_9'],
        $row['it_origin'],
        $row['it_basic'],
        $row['it_stock_qty'],
        $row['it_noti_qty'],
        $row['it_buy_min_qty'],
        $row['it_buy_max_qty'],
        $row['it_notax'],
        $row['it_order'],
        $row['it_maker'],
        $row['it_brand'],
        $row['it_model'],
        $row['it_type1'],
        $row['it_type2'],
        $row['it_type3'],
        $row['it_type4'],
        $row['it_type5'],
        $row['it_type6'],
        $row['it_type7'],
        $row['it_type8'],
        $row['it_explan'],
        $row['it_mobile_explan'],
        $row['it_tel_inq'],
        $row['it_point'],
        $row['it_point_type'],
        $row['it_sell_email'],
        $row['it_img1'],
        $row['it_img2'],
        $row['it_img3'],
        $row['it_img4'],
        $row['it_img5'],
        $row['it_img6'],
        $row['it_img7'],
        $row['it_img8'],
        $row['it_img9'],
        $row['it_img10']
    );
}

$data = array_merge(array($notice), array($headers), $rows);

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0)->getStyle( "A2:{$last_char}2" )->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($header_bgcolor);
$excel->setActiveSheetIndex(0)->getStyle( "A:{$last_char}" )->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
foreach($widths as $i => $w) $excel->setActiveSheetIndex(0)->getColumnDimension( column_char($i) )->setWidth($w);
$excel->getActiveSheet()->fromArray($data, NULL, 'A1');

$filename = "itemlist-".date("ymd", time()).".xls";
if (is_ie()) $filename = utf2euc($filename);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header("Cache-Control: max-age=0");

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');
exit;
