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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function banners(){

    $inCore = cmsCore::getInstance();

    $inCore->loadModel('banners');
    $model = new cms_model_banners();

	if(!$model->config['component_enabled']) { cmsCore::error404(); }

    $do = $inCore->request('do', 'str', 'click');
	$banner_id = $inCore->request('id', 'int', 0);

//======================================================================================================================//

    if ($do=='click'){

        $banner = $model->getBanner($banner_id);
		if(!$banner) { cmsCore::error404(); }

        $model->clickBanner($banner_id);
        $inCore->redirect($banner['link']);

    }

//======================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>