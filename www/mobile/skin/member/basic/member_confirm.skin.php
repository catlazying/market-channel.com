<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="mb_confirm" class="mbskin<?php if ($url == 'member_leave.php') { ?> _memLeave<?php }?>">
    <h1><?php echo $g5['title'] ?></h1>

    <p>
        <strong>비밀번호를 한번 더 입력해주세요.</strong>
        <?php if ($url == 'member_leave.php') { ?>
				마캣채널 서비스를 이용해주셔서 감사합니다.<br>
				회원탈퇴 시 아이디를 제외한 모든 회원정보는 삭제 처리 되며 서비스를 이용하시려면 서비스를
				재 가입 하셔야 합니다.<br>
				또한 재 가입 시 이전에 사용한 아이디 (동일한사업자등록번호) 는 사용할 수 없습니다.
        <?php }else{ ?>
        회원님의 정보를 안전하게 보호하기 위해 비밀번호를 한번 더 확인합니다.
        <?php }  ?>
    </p>

    <form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
    <input type="hidden" name="w" value="u">

    <fieldset>

        <span id="mb_confirm_id">회원아이디 : <?php echo $member['mb_id'] ?></span>
        <input type="password" name="mb_password" id="mb_confirm_pw" placeholder="비밀번호(필수)" required class="frm_input" size="15" maxLength="20">
        <input type="submit" value="확인" id="btn_submit" class="btn_submit">
    </fieldset>

    </form>

</div>

<script>
function fmemberconfirm_submit(f)
{
    document.getElementById("btn_submit").disabled = true;

    return true;
}
</script>
