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

	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	include(PATH.'/core/ajax/ajax_core.php');

    $do = cmsCore::request('do', 'str');

    if ($do !== 'add') { exit; }
    cmsCore::loadModel('intuit');
    $model = new cms_model_intuit();

	// Подключаем аякс сабмит формы
	$inPage->addHeadJS('includes/jquery/jquery.form.js');

/* ==================================================================================================== */
/* ==================================================================================================== */

/* ==================================================================================================== */
/* ==================================================================================================== */
    $smarty = $inCore->initSmarty();

	$smarty = $inCore->initSmarty('components', 'com_intuit_add.tpl');
	
	$smarty->assign('is_user', $inUser->id);
	$smarty->assign('type_rate', $model->AddTypeRateInForm());
	ob_start();
    $smarty->display('com_intuit_add.tpl');
	echo ob_get_clean();

	exit();

?>