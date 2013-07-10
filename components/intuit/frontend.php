<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function intuit(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$inUser = cmsUser::getInstance();
	
	$User_id = $inUser->id;
	
    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }
	
    cmsCore::loadModel('intuit');
    $model = new cms_model_intuit();

	global $_LANG;
	
	$cfg = $model->config;
	
	$do = cmsCore::request('do', 'str', 'view');
	$game_id = cmsCore::request('game_id', 'int');

	$do = preg_replace ('/[^a-z_]/ui', '', $do);

	$inPage->addPathway($inCore->menuTitle(), '/'.$cfg['seo_link']);
	$inPage->setTitle($cfg['seo_title']);
	$inPage->setDescription($cfg['seo_keywords']);
	$inPage->setKeywords($cfg['seo_description']);
	
if ($do == 'view'){
	
	if ($model->User_is_online){cmsCore::redirect('/'.$cfg['seo_link'].'/'.$model->GameInfo['id'].'/play.html');}
	$smarty = $inCore->initSmarty('components', 'com_intuit_view.tpl');
	$smarty->assign('games', $model->GetGames());
	$smarty->display('com_intuit_view.tpl');

	
}

if ($do=='play'){
	if (!$model->User_is_online){cmsCore::redirect('/'.$cfg['seo_link']);}
	if ($game_id and ($model->GameInfo['id'] <> $game_id)){cmsCore::redirect('/'.$cfg['seo_link'].'/'.$model->GameInfo['id'].'/play.html');}
	$smarty = $inCore->initSmarty('components', 'com_intuit_play.tpl');
	$smarty->assign('tabl_body', $model->GenTabl());
	$smarty->assign('TimeOutWaiting', $model->TimeOutWaiting);
	$smarty->display('com_intuit_play.tpl');
	
}

if ($do=='add'){

	// Только аякс
	if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	if ($model->GameInfo) {cmsCore::jsonOutput(array('error' => true, 'text' => 'Невозможно создать новую игру, находясь в игре.'));} 

	if(!cmsCore::validateForm()) { cmsCore::error404(); }

	
	$type_rate = cmsCore::request('type_rate', 'str');
	$rate	   = round(abs(cmsCore::request('rate', 'int')),0);

	if (!$model->CheckBalance($type_rate,$rate)){
		cmsCore::jsonOutput(array('error' => true, 'text' => 'Ваш баланс не позволяет создать игру. '));
	}
	if ($model->PayProcess($User_id,$type_rate,$rate,false)){
		cmsCore::jsonOutput(array('error' => true, 'text' => 'платёж не прошел'));
	}

	//Создаём игру и добавояем пользователя в онайлн
	$model->AddUserOnline($model->AddNewGame($type_rate,$rate));
	// Очищаем буфер
	ob_end_clean();


    cmsUser::clearCsrfToken();
	// Ставим флаг ОК для переадресации
	cmsCore::jsonOutput(array('error' => false, 'is_ok' => 'Игра успешно создана'));

}

$inCore->executePluginRoute($do);
}?>