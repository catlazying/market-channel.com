<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>
<form name="frmdetailsearch" class="sch" action="<?php echo G5_SHOP_URL; ?>/search.php" onsubmit="return search_submit(this);">
    <input type="hidden" name="qsort" id="qsort" value="<?php echo $qsort ?>">
    <input type="hidden" name="qorder" id="qorder" value="<?php echo $qorder ?>">
    <input type="hidden" name="qcaid" id="qcaid" value="<?php echo $ca['ca_id'] ?>">
    <input type="hidden" name="qname" value="1">
    <input type="hidden" name="qbasic" value="0">
    <input type="text" name="q" value="<?php echo $q; ?>" id="ssch_q"  maxlength="30" placeholder="상품명을 입력해 주세요">
    <button type="submit"><i class="ri-search-line"></i></button>
</form>
