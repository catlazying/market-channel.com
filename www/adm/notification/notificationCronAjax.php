<?
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/commonSet.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/dbConnect.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/INC/commonFunc.php');

$code = 0;
$msg = "";
$result = json_encode(array("code" => $code, "msg" => $msg));

$useTable = $notificationCronTable;

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

	if(!empty($postData['fcm_sender'])){ $fcm_sender = valueChk($postData['fcm_sender']); }
	if(!empty($postData['fcm_title'])){ $fcm_title = valueChk($postData['fcm_title']); }
	if(!empty($postData['fcm_message'])){ $fcm_sender = valueChk($postData['fcm_message']); }
	if(!empty($postData['fcm_image'])){ $fcm_image = valueChk($postData['fcm_image']); }
	if(!empty($postData['fcm_link'])){ $fcm_link = valueChk($postData['fcm_link']); }
	if(!empty($postData['fcm_result'])){ $fcm_result = valueChk($postData['fcm_result']); }
	if(!empty($postData['user_token'])){ $user_token = valueChk($postData['user_token']); }
	if(!empty($postData['user_id'])){ $user_token = valueChk($postData['user_id']); }
	if(!empty($postData['send_date'])){ $send_date = valueChk($postData['send_date']); }
		if(!empty($postData['send_type'])){ $send_type = valueChk($postData['send_typesend_type']); }
	if(!empty($postData['reservationTime'])){ $reservationTime = valueChk($postData['reservationTime']); }

    $addParamAry = array();
	if(!empty($fcm_sender )){ $addParamAry[] = " fcm_sender ='$fcm_sender' "; }
	if(!empty($fcm_title )){ $addParamAry[] = " fcm_title ='$fcm_title' "; }
	if(!empty($fcm_message )){ $addParamAry[] = " fcm_message ='$fcm_message' "; }
	if(!empty($fcm_image )){ $addParamAry[] = " fcm_image ='$fcm_image' "; }
	if(!empty($fcm_link )){ $addParamAry[] = " fcm_link ='$fcm_link' "; }
	if(!empty($fcm_result )){ $addParamAry[] = " fcm_result ='$fcm_result' "; }
	if(!empty($user_token )){ $addParamAry[] = " user_token ='$user_token' "; }
	if(!empty($user_id )){ $addParamAry[] = " user_id ='$user_id' "; }
	if(!empty($send_date )){ $addParamAry[] = " send_date ='$send_date' "; }
		if(!empty($send_type )){ $addParamAry[] = " send_type ='$send_type' "; }
	if(!empty($reservationTime )){ $addParamAry[] = " reservationTime ='$reservationTime' "; }
}

if( $mode=="subView" ){
	$selNum = valueChk($postData['selNum']);
}

switch($mode)
{
	case "pushList":
	case "pushListRC":
        $dataAry = array();
        $fcm_sender = valueChk($postData['fcm_sender']);
		if(empty($fcm_sender)) $fcm_sender = "PETEL";
		$addParam = "*";
		$addOrder = " ORDER BY idx ASC ";
		$addWhereLast = "limit 90";
		$addWhere = " WHERE send_date='N' AND viewYn='Y' AND ecnt <1 ";

		if($mode=="pushListRC"){
             $addSql[] = " send_type in ('RESERVE', 'CANCEL') ";
		}else{
			$addSql[] = " send_type not in ('RESERVE', 'CANCEL') ";
		}

 		$addSql[] = " reservationTime <= NOW()  ";
 		$addSql[] = " user_token is not null AND user_token !='' ";
		if(!empty($fcm_sender)){
			$addSql[] = " fcm_sender = '".$fcm_sender."' ";
		}
		if(is_array($addSql)){
			$addWhere .= " AND ".implode(" AND ",$addSql);
		}
		$res = procDb("SELECT", $useTable." a",$addParam,$addWhere,$addOrder,$addWhereLast);
		if(is_array($res)){
			foreach ( $res as $key => $value ){
				$dataAry[$key] = $value;
			}
        }
		$result = json_encode( $dataAry );
	break;
	case "getList":
        $findWord = valueChk($postData['findWord']);
        $findClass = valueChk($postData['findClass']);
		if($findClass=='title'){
			$findClass = "fcm_title";
		}
        $send_type = valueChk($postData['send_type']);
        $sdate = valueChk($postData['sdate']);
        $title = valueChk($postData['title']);

        $addSql = NULL;
        $addWhere = " WHERE idx > 0 AND viewYn='Y' ";
        if(!empty($findWord)){
            $addSql[] = " $findClass like '%".$findWord."%' ";
        }
        if(!empty($send_type)){ $addSql[] = " send_type = '".$send_type."' "; }
        if(!empty($sdate)){ $addSql[] = " rdate = '".$sdate."' "; }
        if(!empty($title)){ $addSql[] = " fcm_title = '".$title."' "; }


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
		if(!empty($title)){
			$addOrder = " ORDER BY idx ASC ";
		}


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
if (empty($diractMode)){
	print $result;
}