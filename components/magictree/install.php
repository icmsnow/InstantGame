<?php

    function info_component_magictree(){

        //Описание компонента

        $_component['title']        = 'Магическое дерево';
        $_component['description']  = 'Магическое дерево - вырасти своё дерево';
        $_component['link']         = 'magictree';
        $_component['author']       = '<a href="http://www.instantcms.ru/users/somebody" target="_blank" >Димитриус</a>';
        $_component['internal']     = '0';
        $_component['version']      = '0.1';

        return $_component;

    }

// ========================================================================== //

    function install_component_magictree(){

        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных
        $inConf     = cmsConfig::getInstance();

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');

        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/magictree/install.sql', $inConf->db_prefix);

        return true;

    }

// ========================================================================== //

?>
