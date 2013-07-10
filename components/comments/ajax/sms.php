<?php
	function CheckPhone($phone,$user_id){
		$inDB	= cmsDatabase::getInstance();
		$sql = "SELECT phone
				FROM cms_sms_activ 
				WHERE user_id = '$user_id' and phone = '$phone'";
		$result = $inDB->query($sql);
		if ($inDB->num_rows($result)){ 
				$userdata = $inDB->fetch_assoc($result);
				return $userdata['phone'];}
				else return false;
	}

	function CheckCode($phone){
			$inCore 	= cmsCore::getInstance();
			$inDB   = cmsDatabase::getInstance();
			
			$sql = "SELECT phone
					FROM cms_sms_activ 
					WHERE phone = '$phone' AND user_id is null AND user_id <> 0";
			$result = $inDB->query($sql);
			if (!$inDB->num_rows($result)){
				for ($i = 0; $i < 6; $i++) {$code .= mt_rand(0, 9);}
				$sql = "INSERT INTO cms_sms_activ ( user_id, phone, codactiv) VALUES (NULL, '$phone','$code')";
				$inDB->query($sql);
				 return $code;
				}
				 else {return false;}	   
	}
	
	function ActivateCode($phone,$activcode,$user_id){
		$inCore 	= cmsCore::getInstance();
		$inDB   = cmsDatabase::getInstance();
		$inCore->loadClass('user');
		
		$sql = "SELECT phone, codactiv
				FROM cms_sms_activ 
				WHERE phone = '$phone' AND codactiv = '$activcode' AND user_id IS NULL";
		$result = $inDB->query($sql);
		if ($inDB->num_rows($result)){
			if ($user_id){
				$sql = "UPDATE cms_sms_activ
				SET user_id = '$user_id'
				WHERE phone = '$phone' AND codactiv = '$activcode' AND user_id IS NULL"; 
				$inDB->query($sql);
			} else {
				$sql = "UPDATE cms_sms_activ
						SET user_id = 0
						WHERE phone = '$phone' AND codactiv = '$activcode' AND user_id IS NULL"; 
				$inDB->query($sql);
				
				cmsUser::sessionPut('phone', $phone);}
		}else return false;	
		return true;
	}
 
    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }
	
	session_start();
	
	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
	//настроечки
	define('APIKEY', 'Q2M13550B6G9DI1DBGWHO88039Y9DU74L4GFC10DG4WWZ481G690Y02104E1');
	define('SENDNAME', 'instant');
	
	include(PATH.'/core/cms.php');
	include('smspilot.class.php');
	
 	$inCore = cmsCore::getInstance();
	define('HOST', 'http://' . $inCore->getHost());   
	
    $inCore->loadClass('db');           //база данных
	$inCore->loadClass('user');
	
	$opt = $inCore->request('opt', 'str', '');
	if (!$opt) { return; }
	$phone = $inCore->request('phone', 'str', '');
    $activcode = $inCore->request('activcode', 'str', '');	
	
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	$inUser->update();
	$user_id = $inUser->id;
	// Если установленна переменна в сессии с телефоном то ничего недать не надо

	if (cmsUser::sessionGet('phone')){return;}
		
	// Если у пользователя есть номер телефона то тоже ему тут делать нечего
	if ($user_id){if (CheckPhone($phone,$user_id)){return;}}

  	if ($opt=='send' and $phone){		
		if ( substr($phone, 0,2) == '79' && substr($phone, 0,4) != '7940' ){
			$code = CheckCode($phone);
			if ($code){
				$sms = new SMSPilot(APIKEY);
				if ($sms->send($phone,'Ваш код актвации: '.$code, SENDNAME)) {
						echo '200';
						} else {echo $sms->error;}
			}else echo '403';
		}else echo '400';	
	}
	
	if ($opt=='activ' and $phone and $activcode){
		if (ActivateCode($phone,$activcode,$user_id)){			
			echo '200';
		}else echo '403';		
	}
?>