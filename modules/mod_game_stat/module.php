<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.10                               //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2012                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

function mod_game_stat($module_id){

	$inCore = cmsCore::getInstance();
	$inDB   = cmsDatabase::getInstance();
	
	if (!$inCore->isComponentInstalled('intuit')){return;}
	$cfg    = $inCore->loadModuleConfig($module_id);
	
	if ($cfg['count_online']){
		//количество игроков в онлайне
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_online WHERE  (date_motion >=NOW()-300)";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_online'] = $result['count'];
	}

	if ($cfg['count_all']){
		//количество игр за месяц
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_all'] = $result['count'];
	}
	if ($cfg['count_month']){
		//количество игр за месяц
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE date_create >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_month'] = $result['count'];
	}
	
	if ($cfg['count_week']){
		//количество игр за неделю
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE date_create >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_week'] = $result['count'];
	}
	
	if ($cfg['count_yesterday']){
	//количество игр вчера 
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE year(date_create) = year(current_timestamp - INTERVAL 1 DAY) AND
																		month(date_create) = month(current_timestamp - INTERVAL 1 DAY) AND
																		day(date_create) = day(current_timestamp - INTERVAL 1 DAY) AND
																		winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_yesterday'] = $result['count'];
	}
	
	if ($cfg['count_today']){
		//количество игр сегодня
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE 
											date_create >= CURDATE()
											AND winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_today'] = $result['count'];
	}
	

	
	$smarty = $inCore->initSmarty('modules', 'mod_game_stat.tpl');
	$smarty->assign('data', $data);
	$smarty->assign('cfg', $cfg);
	$smarty->display('mod_game_stat.tpl');

	return true;

}
?>