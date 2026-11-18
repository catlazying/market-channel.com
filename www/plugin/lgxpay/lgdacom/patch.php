<?php

	$configPath = "C:/lgdacom"; //LG���÷������� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

    /*
     * [LG���÷��� ȯ������ UPDATE]
     *
     * �� �������� LG���÷��� ȯ�������� UPDATE �մϴ�.(�������� ������.)
     */
    $CST_PLATFORM   = isset($_POST["CST_PLATFORM"]) ? $_POST["CST_PLATFORM"] : '';
    $CST_MID        = isset($_POST["CST_MID"]) ? $_POST["CST_MID"] : '';
    $LGD_MID        = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;

    if( $CST_PLATFORM == null || $CST_PLATFORM == "" ) {
        echo "[TX_PING error] �Ķ���� ����<br>";
        return;
    }
    if( $LGD_MID == null || $LGD_MID == "" ) {
        echo "[TX_PING error] �Ķ���� ����<br>";
        return;
    }

    require_once("./XPayClient.php");
    $xpay = new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);

    echo "patch result = ".$xpay->Patch("lgdacom.conf");