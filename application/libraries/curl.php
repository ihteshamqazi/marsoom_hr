<?php
//دالة فحص حالة الإرسال بإستخدام CURL
function sendStatus($viewResult=1)
{
	global $arraySendStatus;	
	$url = "http://www.mobily.ws/api/sendStatus.php";$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendStatus);
	return $result;
}

//دالة تغيير كلمة المرور لحساب الإرسال في موقع موبايلي بإستخدام CURL
function changePassword($userAccount, $passAccount, $newPassAccount, $viewResult=1)
{
	global $arrayChangePassword;
	$url = "http://www.mobily.ws/api/changePassword.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&newPassword=".$newPassAccount;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&newPassword=".$newPassAccount;
    }
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayChangePassword);
	return $result;
}

//دالة إسترجاع كلمة المرور لحساب الإرسال في موقع موبايلي بإستخدام CURL
function forgetPassword($userAccount, $sendType, $viewResult=1)
{
	global $arrayForgetPassword;
	$url = "http://www.mobily.ws/api/forgetPassword.php";
	$stringToPost = "mobile=".$userAccount."&type=".$sendType;$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayForgetPassword);
	return $result;
}

//دالة إسترجاع كلمة المرور لحساب الإرسال في موقع موبايلي بإستخدام CURL
function forgetPasswordApiKey($apiKey, $sendType, $viewResult=1)
{
    global $arrayForgetPassword;
    $url = "http://www.mobily.ws/api/forgetPassword.php";
    $stringToPost = "apiKey=".$apiKey."&type=".$sendType;$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    $result = curl_exec($ch);

    if($viewResult)
        $result = printStringResult(trim($result) , $arrayForgetPassword);
    return $result;
}

//دالة عرض الرصيد بإستخدام CURL
function balanceSMS($userAccount, $passAccount, $viewResult=1)
{
	global $arrayBalance;
	$url = "http://www.mobily.ws/api/balance.php";

    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount;
    }
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result), $arrayBalance, 'Balance');
	return $result;
}

//دالة الإرسال بإستخدام CURL
function sendSMS($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsg;
	$url = "http://www.mobily.ws/api/msgSend.php";
	$applicationType = "68";  
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];

    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsg);
	return $result;
}

//دالة الإرسال بإستخدام قالب رسالة موحد من خلال CURL
function sendSMSWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsgWK;
	$url = "http://www.mobily.ws/api/msgSendWK.php";
	$applicationType = "68";
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];

    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    }

    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsgWK);
	return $result;
}

//دالة حذف الرسائل المجدولة بإستخدام CURL
function deleteSMS($userAccount, $passAccount, $deleteKey=0, $viewResult=1)
{
	global $arrayDeleteSMS;
	$url = "http://www.mobily.ws/api/deleteMsg.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&deleteKey=".$deleteKey;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&deleteKey=".$deleteKey;
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayDeleteSMS);
	return $result;
}

//دالة طلب إسم مرسل (جوال) بإستخدام CURL
function addSender($userAccount, $passAccount, $sender, $viewResult=1)
{	
	global $arrayAddSender;
	$url = "http://www.mobily.ws/api/addSender.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&sender=".$sender;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result), $arrayAddSender, 'Normal');
	return $result;
}

//دالة تفعيل إسم مرسل (جوال) بإستخدام CURL
function activeSender($userAccount, $passAccount, $senderId, $activeKey, $viewResult=1)
{
	global $arrayActiveSender;
	$url = "http://www.mobily.ws/api/activeSender.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&senderId=".$senderId."&activeKey=".$activeKey;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId."&activeKey=".$activeKey;
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayActiveSender);
	return $result;
}

//دالة التحقق من حالة طلب إسم مرسل (جوال) بإستخدام CURL
function checkSender($userAccount, $passAccount, $senderId, $viewResult=1)
{	
	global $arrayCheckSender;
	$url = "http://www.mobily.ws/api/checkSender.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&senderId=".$senderId;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId;
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckSender);
	return $result;
}

//دالة طلب إسم مرسل (أحرف) بإستخدام CURL
function addAlphaSender($userAccount, $passAccount, $sender, $viewResult=1)
{
	global $arrayAddAlphaSender;
	$url = "http://www.mobily.ws/api/addAlphaSender.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&sender=".$sender;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayAddAlphaSender);
	return $result;
}

//دالة التحقق من حالة طلب إسم مرسل (أحرف) بإستخدام CURL
function checkAlphasSender($userAccount, $passAccount, $viewResult=1)
{
	global $arrayCheckAlphasSender;
	$url = "http://www.mobily.ws/api/checkAlphasSender.php";
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount;
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount;
    }
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckAlphasSender, 'Senders');
	return $result;
}
?>