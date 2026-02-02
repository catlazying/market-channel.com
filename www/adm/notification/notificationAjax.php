<?
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/commonSet.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/dbConnect.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/commonFunc.php');

$code = 0;
$msg = "";
$result = json_encode(array("code" => $code, "msg" => $msg));

$useTable = $notificationTable;

$useTableTitle = "[메시지 발송]";

if(empty($diractMode)){
    $postJson = file_get_contents("php://input");
    if(!empty($postJson)){
        $postData = json_decode($postJson,true);
        $mode = valueChk($postData['mode']);
    }else{
        $mode = "";
    }
    if(empty($mode)){
        $postData = $_POST;
        $mode = valueChk($postData['mode']);
    }
}else{
    $mode = $diractMode;
    $postData = $_POST;
    if(empty($postData['csid'])){
        $postData['csid'] = $_SESSION['sid'];
    }
}

$csid = valueChk($postData['csid']);
if(!empty($csid) ){
	if($_SESSION['sid'] != $csid ){
		$msg = "잘못된 접근입니다.(".$mode.")";
			$saveTxt = $useTableTitle." ".$msg.":".$_SESSION['sid']."!=".$csid;
			logSave($saveTxt,"Y",$useTable);
		print json_encode(array("code" => $code, "msg" => $msg));
		exit;
	}
}else{
	$msg = "잘못된 sid 값입니다.(".$mode.")";
		$saveTxt = $useTableTitle." ".$msg;
		logSave($saveTxt,"Y",$useTable);
	print json_encode(array("code" => $code, "msg" => $msg));
	exit;
}

if( $mode=="subSave" || $mode=="subMod" ){
	if($_SESSION['sid'] != $csid ){
		$msg = "잘못된 접근입니다.(".$mode.")".$_SESSION['sid'] ."!=". $csid;
			$saveTxt = $useTableTitle." ".$msg;
			logSave($saveTxt,"Y",$useTable);
		print json_encode(array("code" => $code, "msg" => $msg));
		exit;
	}

    if(!empty($postData['idx'])){ $saveIdx = valueChk($postData['idx']); }

	if(!empty($postData['title'])){ $title = valueChk($postData['title']); }
	if(!empty($postData['send_type'])){ $send_type = valueChk($postData['send_type']); }
	if(!empty($postData['send_target'])){ $send_target = valueChk($postData['send_target']); }
	if(!empty($postData['member_select'])){ $member_select = valueChk($postData['member_select']); }
	if(!empty($postData['fcm_target'])){ $fcm_target = valueChk($postData['fcm_target']); }
	if(!empty($postData['fcm_title'])){ $fcm_title = valueChk($postData['fcm_title']); }
	if(!empty($postData['fcm_message'])){ $fcm_message = valueChk($postData['fcm_message']); }
	if(!empty($postData['fcm_image'])){ $fcm_image = valueChk($postData['fcm_image']); }
	if(!empty($postData['fcm_link'])){ $fcm_link = valueChk($postData['fcm_link']); }
	if(!empty($postData['fcm_memo'])){ $fcm_memo = valueChk($postData['fcm_memo']); }

    $addParamAry = array();
	if(!empty($title )){ $addParamAry[] = " title ='$title' "; }
	if(!empty($send_type )){ $addParamAry[] = " send_type ='$send_type' "; }
	if(!empty($send_target )){ $addParamAry[] = " send_target ='$send_target' "; }
	if(!empty($member_select)){ $addParamAry[] = " member_select ='$member_select' "; }
	if(!empty($fcm_target )){ $addParamAry[] = " fcm_target ='$fcm_target' "; }
	if(!empty($fcm_title )){ $addParamAry[] = " fcm_title ='$fcm_title' "; }
	if(!empty($fcm_message )){ $addParamAry[] = " fcm_message ='$fcm_message' "; }
	if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
	if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
	if(!empty($fcm_memo )){ $addParamAry[] = " fcm_memo ='$fcm_memo' "; }
}

if( $mode=="subView" ){
	$selNum = valueChk($postData['selNum']);
}

switch($mode)
{
	case "getList":
        $findWord = valueChk($postData['findWord']);
        $findClass = valueChk($postData['findClass']);
		if($findClass=='title'){
		}

        $addSql = NULL;
        $addWhere = " WHERE idx > 0 AND viewYn='Y' ";
        if(!empty($findWord)){
            $addSql[] = " $findClass like '%".$findWord."%' ";
        }

		if(is_array($addSql)){
			$addWhere .= " AND ".implode(" AND ",$addSql);
		}
		$page = valueChk($postData['page']);
		if(empty($page)){
			$page = 1;
		}
		$pagePerCnt = valueChk($postData['changeList']);
		if(empty($pagePerCnt)){
			$pagePerCnt = 10;
		}
		$pagePerBlock = valueChk($postData['pagePerBlock']);
		if(empty($pagePerBlock)){
			$pagePerBlock = 3;
		}
		$sNumber = $pagePerCnt * ($page - 1);
		$eNumber = $pagePerCnt;
		$addParam = "count(*) as cnt";
		$selRow = procDb("ONEROW", $useTable,$addParam,$addWhere);
		$totalcnt = $selRow['cnt'];
		$number = $totalcnt - $sNumber;
        $resultAry = array();
        $dataAry = array();
		$addParam = "*";
		$addOrder = " ORDER BY idx DESC ";
		$addWhereLast = " LIMIT $sNumber, $eNumber ";
		$res = procDb("SELECT", $useTable." a",$addParam,$addWhere,$addOrder,$addWhereLast);
		if(is_array($res)){
			foreach ( $res as $key => $value ){
				$dataAry[$key] = $value;
				$dataAry[$key]['number'] = $number;

				$number--;
				$code = 1;
			}
        }
        $resultAry['list'] = $dataAry;
		$totalPage = ceil($totalcnt / $pagePerCnt);
		$totalBlock = ceil($totalPage / $pagePerBlock);
		$block = ceil($page / $pagePerBlock);
		$firstPage = ($block - 1) * $pagePerBlock;
		$lastPage = $block * $pagePerBlock;
		$prevPage = $firstPage;
		if($prevPage < 0){
			$prevPage = 0;
		}
		$nextPage = $lastPage + 1;
		if($nextPage > $totalPage){
			$nextPage = $totalPage;
		}
		if($totalBlock <= $block) {
			$lastPage=$totalPage;
		}
        $resultAry['page']['thisPage'] = $page;
        $resultAry['page']['firstPage'] = $firstPage;
        $resultAry['page']['lastPage'] = $lastPage;
        $resultAry['page']['prevPage'] = $prevPage;
        $resultAry['page']['nextPage'] = $nextPage;
        $resultAry['page']['block'] = $block;
		$resultAry['page']['totalPage'] = $totalPage;
        $resultAry['page']['totalBlock'] = $totalBlock;
		$resultAry['code'] = $code;
		$result = json_encode( $resultAry );
	break;

	case "subDel":
		$delNumber = $postData['number'];
		if(is_array($delNumber)){
			$addWhere = "idx in (".implode(",",$delNumber).")";
		}else{
			$addWhere = "idx = $delNumber ";
		}
        $addParam = "viewYn='N'";
		$code = procDb("UPDATE", $useTable,$addParam,$addWhere);
		//로그저장
		$saveTxt = $useTableTitle."삭제 ".$msg."->".$addWhere;
		logSave($saveTxt,"Y",$useTable);
		//로그저장끝
		$result = json_encode(array("code" => $code, "msg" => $msg));
	break;

	case "subView":
		$addParam = "*";
		$addWhere = "idx='$selNum' AND viewYn='Y' ";
		$selRow = procDb("ONEROW", $useTable,$addParam,$addWhere);
		if(is_array($selRow)){
			foreach ( $selRow as $key => $value ){
                if($key!='pwd'){
                    $resultAry[$key] = $value;
                }
			}
		}
		$result = json_encode( $resultAry );
	break;

 	case "subSave":
		if(is_array($addParamAry)){
			$addParams = ",".implode(",",$addParamAry);
		}
		if(!empty($addParams)){
			$addParam = "rdate = CURDATE(), rdatetime = NOW(), mdatetime = NOW()".$addParams;
			$addWhere = "";
			$saveIdx = procDb("INSERT", $useTable,$addParam,$addWhere);
		}

		if(empty($saveIdx)){
			$msg = "저장이 실패 했습니다.";
			$code = 0;
		}else{
			$msg = "저장이 완료되었습니다.";
			$code = 1;
		}
		//로그저장
		$saveTxt = $useTableTitle." ".$msg."->(".$useTable.")".$addParam;
		logSave($saveTxt,"Y",$useTable);
		//로그저장끝
		$result = json_encode(array("code" => $code, "msg" => $msg));
	break;

	case "subMod":
		if(is_array($addParamAry)){
			$addParams = ",".implode(",",$addParamAry);
		}
		if(!empty($addParams)){
			$addParam = " mdatetime=NOW()".$addParams;
			$addWhere = "idx=$saveIdx";
			$selRow = procDb("UPDATE", $useTable,$addParam,$addWhere);
			$msg = "수정되었습니다.";
			$code = 1;

		}else{
			$msg = "수정이 실패 했습니다.";
			$code = 0;
		}
		//로그저장
		$saveTxt = $useTableTitle." ".$msg."->".$addWhere;
		logSave($saveTxt,"Y",$useTable);
		//로그저장끝
		$result = json_encode(array("code" => $code, "msg" => $msg));
	break;

}
print $result;