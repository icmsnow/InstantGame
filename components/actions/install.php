<?php

// ========================================================================== //

    function info_component_actions(){

        $_component['title']        = '����� ����������';
        $_component['description']  = '���������� ������ ������� �� �����';
        $_component['link']         = 'actions';
        $_component['author']       = 'InstantCMS Team';
        $_component['internal']     = '0';
        $_component['version']      = '1.9';

		$inCore = cmsCore::getInstance();
		$inCore->loadModel('actions');

		$_component['config'] = cms_model_actions::getDefaultConfig();

        return $_component;

    }

// ========================================================================== //

    function install_component_actions(){

        return true;

    }

// ========================================================================== //

    function upgrade_component_actions(){

        return true;
        
    }

// ========================================================================== //

?>