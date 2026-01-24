<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/iteminfo.lib.php');

$it_id = isset($_GET['it_id']) ? get_search_string(trim($_GET['it_id'])) : '';
$it_seo_title = isset($it_seo_title) ? $it_seo_title : '';

$it = get_shop_item_with_category($it_id, $it_seo_title);
$it_id = $_REQUEST['it_id'] = $it['it_id'];

if (! (isset($it['it_id']) && $it['it_id'])) {
    alert('자료가 없습니다.');
}

if( isset($row['it_seo_title']) && ! $row['it_seo_title'] ){
    shop_seo_title_update($row['it_id']);
}

if (function_exists('check_case_exist_title')) check_case_exist_title($it, G5_SHOP_DIR, true);

if (!($it['ca_use'] && $it['it_use'])) {
    if (!$is_admin)
        alert('판매가능한 상품이 아닙니다.');
}

// 분류 테이블에서 분류 상단, 하단 코드를 얻음
$sql = " select ca_mobile_skin_dir, ca_include_head, ca_include_tail, ca_cert_use, ca_adult_use
           from {$g5['g5_shop_category_table']}
          where ca_id = '{$it['ca_id']}' ";
$ca = sql_fetch($sql);

// 본인인증, 성인인증체크
if(!$is_admin) {
    $msg = shop_member_cert_check($it_id, 'item');
    if($msg)
        alert($msg, G5_SHOP_URL);
}

// 가격변동 내역
$chartPrice = null;
$sql1 = " select it_id, it_price, it_time from {$g5['g5_shop_item_price_table']} where it_time >='".date("Y-m-d H:i:s",strtotime('-3month'))."' AND it_id='$it_id'  order by it_time";

$result1 = sql_query($sql1);
for ($i=0; $row1=sql_fetch_array($result1); $i++) {
    $chartPrice[$i]['it_price'] = $row1['it_price'];
    $chartPrice[$i]['it_time'] = date("Y-m-d",strtotime($row1['it_time']));
}

// 오늘 본 상품 저장 시작
// tv 는 today view 약자
$saved = false;
$tv_idx = (int)get_session("ss_tv_idx");
if ($tv_idx > 0) {
    for ($i=1; $i<=$tv_idx; $i++) {
        if (get_session("ss_tv[$i]") == $it_id) {
            $saved = true;
            break;
        }
    }
}

if (!$saved) {
    $tv_idx++;
    set_session("ss_tv_idx", $tv_idx);
    set_session("ss_tv[$tv_idx]", $it_id);
}
// 오늘 본 상품 저장 끝

// 조회수 증가
if (get_cookie('ck_it_id') != $it_id) {
    sql_query(" update {$g5['g5_shop_item_table']} set it_hit = it_hit + 1 where it_id = '$it_id' "); // 1증가
    set_cookie("ck_it_id", $it_id, 3600); // 1시간동안 저장
}

// 이전 상품보기
$sql = " select it_id, it_name from {$g5['g5_shop_item_table']}
          where it_id > '$it_id'
            and SUBSTRING(ca_id,1,4) = '".substr($it['ca_id'],0,4)."'
            and it_use = '1'
          order by it_id asc
          limit 1 ";
$row = sql_fetch($sql);
if (isset($row['it_id']) && $row['it_id']) {
    $prev_title = '이전상품 <span>'.$row['it_name'].'</span>';
    $prev_href = '<a href="'.shop_item_url($row['it_id']).'" id="siblings_prev">';
    $prev_href2 = '</a>';
} else {
    $prev_title = '';
    $prev_href = '';
    $prev_href2 = '';
}

// 다음 상품보기
$sql = " select it_id, it_name from {$g5['g5_shop_item_table']}
          where it_id < '$it_id'
            and SUBSTRING(ca_id,1,4) = '".substr($it['ca_id'],0,4)."'
            and it_use = '1'
          order by it_id desc
          limit 1 ";
$row = sql_fetch($sql);
if (isset($row['it_id']) && $row['it_id']) {
    $next_title = '다음상품 <span>'.$row['it_name'].'</span>';
    $next_href = '<a href="'.shop_item_url($row['it_id']).'" id="siblings_next">';
    $next_href2 = '</a>';
} else {
    $next_title = '';
    $next_href = '';
    $next_href2 = '';
}

// 관리자가 확인한 사용후기의 개수를 얻음
$sql = " select count(*) as cnt from `{$g5['g5_shop_item_use_table']}` where it_id = '{$it_id}' and is_confirm = '1' ";
$row = sql_fetch($sql);
$item_use_count = $row['cnt'];

// 상품문의의 개수를 얻음
$sql = " select count(*) as cnt from `{$g5['g5_shop_item_qa_table']}` where it_id = '{$it_id}' ";
$row = sql_fetch($sql);
$item_qa_count = $row['cnt'];

if ($default['de_mobile_rel_list_use']) {
    // 관련상품의 개수를 얻음
    $sql = " select count(*) as cnt
               from {$g5['g5_shop_item_relation_table']} a
               left join {$g5['g5_shop_item_table']} b on (a.it_id2=b.it_id)
              where a.it_id = '{$it['it_id']}' and b.it_use='1' ";
    $row = sql_fetch($sql);
    $item_relation_count = $row['cnt'];
}

// 상품품절체크
if(G5_SOLDOUT_CHECK)
    $is_soldout = is_soldout($it['it_id'], true);

// 주문가능체크
$is_orderable = true;
if(!$it['it_use'] || $it['it_tel_inq'] || $is_soldout)
    $is_orderable = false;

if($is_orderable) {
    if(defined('G5_THEME_USE_OPTIONS_TRTD') && G5_THEME_USE_OPTIONS_TRTD){
        $option_item = get_item_options($it['it_id'], $it['it_option_subject'], '');
        $supply_item = get_item_supply($it['it_id'], $it['it_supply_subject'], '');
    } else {
        // 선택 옵션 ( 기존의 tr td 태그로 가져오려면 'div' 를 '' 로 바꾸거나 또는 지워주세요 )
        $option_item = get_item_options($it['it_id'], $it['it_option_subject'], 'div');

        // 추가 옵션 ( 기존의 tr td 태그로 가져오려면 'div' 를 '' 로 바꾸거나 또는 지워주세요 )
        $supply_item = get_item_supply($it['it_id'], $it['it_supply_subject'], 'div');
    }

    // 상품 선택옵션 수
    $option_count = 0;
    if($it['it_option_subject']) {
        $temp = explode(',', $it['it_option_subject']);
        $option_count = count($temp);
    }

    // 상품 추가옵션 수
    $supply_count = 0;
    if($it['it_supply_subject']) {
        $temp = explode(',', $it['it_supply_subject']);
        $supply_count = count($temp);
    }
}

// 스킨경로
$skin_dir = G5_MSHOP_SKIN_PATH;
$ca_dir_check = true;

if($it['it_mobile_skin']) {
    if(preg_match('#^theme/(.+)$#', $it['it_mobile_skin'], $match))
        $skin_dir = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/shop/'.$match[1];
    else
        $skin_dir = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/shop/'.$it['it_mobile_skin'];

    if(is_dir($skin_dir)) {
        $form_skin_file = $skin_dir.'/item.form.skin.php';

        if(is_file($form_skin_file))
            $ca_dir_check = false;
    }
}

if($ca_dir_check) {
    if($ca['ca_mobile_skin_dir']) {
        if(preg_match('#^theme/(.+)$#', $ca['ca_mobile_skin_dir'], $match))
            $skin_dir = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/shop/'.$match[1];
        else
            $skin_dir = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/shop/'.$ca['ca_mobile_skin_dir'];

        if(is_dir($skin_dir)) {
            $form_skin_file = $skin_dir.'/item.form.skin.php';

            if(!is_file($form_skin_file))
                $skin_dir = G5_MSHOP_SKIN_PATH;
        } else {
            $skin_dir = G5_MSHOP_SKIN_PATH;
        }
    }
}

define('G5_SHOP_CSS_URL', str_replace(G5_PATH, G5_URL, $skin_dir));

$g5['title'] = $it['it_name'].' &gt; '.$it['ca_name'];
$naverpay_button_js = '';

include_once(G5_MSHOP_PATH.'/_head.php');
include_once(G5_SHOP_PATH.'/settle_naverpay.inc.php');

// 상단 HTML
echo run_replace('shop_it_mobile_head_html', '<div id="sit_hhtml">'.conv_content($it['it_mobile_head_html'], 1).'</div>', $it);
?>


<?php if($is_orderable) { ?>
<script src="<?php echo G5_JS_URL; ?>/shop.js?ver=<?php echo G5_JS_VER; ?>"></script>
<?php } ?>

<?php
if (G5_HTTPS_DOMAIN)
    $action_url = G5_HTTPS_DOMAIN.'/'.G5_SHOP_DIR.'/cartupdate.php';
else
    $action_url = G5_SHOP_URL.'/cartupdate.php';
?>

<div id="sit">

    <?php
    // 상품 구입폼
    include_once($skin_dir.'/item.form.skin.php');
    ?>
<?php // 가격 변동 차트 (x축: 시간, y축: 가격)
$jsChartData = [];
if(is_array($chartPrice)) {
    foreach($chartPrice as $idx => $row) {
        $day = isset($row['it_time']) ? $row['it_time'] : "";
        $price = isset($row['it_price']) ? (int)$row['it_price'] : 0;
        // x축: 시간($day), y축: 가격($price)
        $jsChartData[] = "['{$day}', {$price}]";
    }
}
$jsChartData[] = "['". date("Y-m-d",strtotime($it['it_update_time']))."', {$it['it_price']}]";
?>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawTimePriceChart);

    function drawTimePriceChart() {
        var dataRows = [
            <?php echo implode(",", $jsChartData); ?>
        ];

        // PHP에서 넘어온 건 JS에서는 문자열이므로 eval로 파싱 필요
        var jsParsedRows = [];
        dataRows.forEach(function(raw){
            // ex: "['2024-06-01', 3000]" => ['2024-06-01', 3000]
            try {
                var arr = eval(raw);
                jsParsedRows.push(arr);
            } catch (e) {
                // 파싱 실패 시 무시
            }
        });

        if(jsParsedRows.length){
            var prices = jsParsedRows.map(function(row){ return row[1]; });
            var minPrice = Math.min.apply(null, prices);
            var maxPrice = Math.max.apply(null, prices);

            var range = maxPrice - minPrice;
            var lowerBound = Math.floor(minPrice - minPrice * 0.1);
            var upperBound = Math.ceil(maxPrice + maxPrice * 0.1);
        } else {
            var lowerBound = 0, upperBound = 10000;
        }

        var data = new google.visualization.DataTable();
        data.addColumn('string', '일자'); // x축: 시간
        data.addColumn('number', '가격'); // y축: 가격
        data.addRows(jsParsedRows);

        // 마지막 데이터(최신 가격) 찾기
        var lastIndex = jsParsedRows.length > 0 ? jsParsedRows.length - 1 : -1;
        var balloonPrice = lastIndex >= 0 ? jsParsedRows[lastIndex][1] : null;
        var balloonDate = lastIndex >= 0 ? jsParsedRows[lastIndex][0] : null;

        var options = {
            title: '',
            curveType: 'function',
            legend: { position: 'none' },
            width: '100%',
            height: '100%',
            chartArea: {
                left: "8%",
                top: "0%",
                width: "84%",
                height: "75%"
            },
            hAxis: {
                // title: '일자'
            },
            vAxis: {
                // title: '가격',
                format: 'decimal',
                viewWindow: {
                    min: lowerBound,
                    max: upperBound
                }
            },
            series: {
                0: {}
            },
            annotations: {
                textStyle: {
                    fontSize: 15,
                    color: '#111',
                    auraColor: 'none',
                    bold: true,
                },
                alwaysOutside: true
            }
        };

        // 말풍선 annotation 데이터를 추가
        // 구글 차트에서 annotation은 별도의 컬럼이 필요하다
        // [date, price, annotation]
        var annotatedRows = jsParsedRows.map(function(row, idx){
            if(idx === lastIndex && balloonPrice !== null) {
                return [row[0], row[1], balloonPrice.toLocaleString() + '원'];
            } else {
                return [row[0], row[1], null];
            }
        });

        var annotatedData = new google.visualization.DataTable();
        annotatedData.addColumn('string', '일자');
        annotatedData.addColumn('number', '가격');
        annotatedData.addColumn({type: 'string', role: 'annotation'});
        annotatedData.addRows(annotatedRows);

        var chart = new google.visualization.LineChart(document.getElementById('line_chart'));
        // 말풍선을 포함한 데이터로 렌더링
        chart.draw(annotatedData, options);
    }
</script>
<section id="sit_rel">
<h2>가격추이</h2>
<div id="line_chart" style="width:100%;min-width:300px;max-width:100%;height:50vw;max-height:500px;min-height:220px;margin:0 auto;"></div>
</div>
</section>
<?php
// 하단 HTML
echo run_replace('shop_it_mobile_tail_html', conv_content($it['it_mobile_tail_html'], 1), $it);

include_once(G5_MSHOP_PATH.'/_tail.php');


