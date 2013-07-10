<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function magictree(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$inUser = cmsUser::getInstance();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }
	
    cmsCore::loadModel('magictree');
    $model = new cms_model_magictree();

	global $_LANG;
	
	$cfg = $inCore->loadComponentConfig('magictree');
	
	$do = cmsCore::request('do', 'str', 'view');
	$do = preg_replace ('/[^a-z_]/ui', '', $do);
	$page = cmsCore::request('page', 'int', 1);
	
	$pagetitle = $inCore->menuTitle();
	$pagetitle = ($pagetitle && $inCore->isMenuIdStrict()) ? $pagetitle : 'Моё дерево';

	$inPage->addPathway($pagetitle, '/magictree');
	$inPage->setTitle($pagetitle);
	$inPage->setDescription($pagetitle);

if ($do == 'view'){

	$tree = $model->getTopListTree();
	$total = $model->getTreeCount();
	
	$smarty = $inCore->initSmarty('components', 'com_magictree_view.tpl');
	$smarty->assign('tree', $tree);
	$smarty->assign('total', $total);
	$smarty->display('com_magictree_view.tpl');

}

if ($do == 'add'){

	$inPage->addPathway('Создать своё дерево', '/magictree/add');
	$inPage->setTitle($pagetitle);
	$inPage->setDescription($pagetitle);
	$tree = $model->getTopListTree();
	$total = $model->getTreeCount();
	
	$smarty = $inCore->initSmarty('components', 'com_magictree_add.tpl');
	$smarty->assign('tree', $tree);
	$smarty->assign('total', $total);
	$smarty->display('com_magictree_add.tpl');

}

$inCore->executePluginRoute($do);
}?>