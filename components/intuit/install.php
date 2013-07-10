<?php

    function info_component_intuit(){

        //Описание компонента

        $_component['title']        = 'Игра: открой первым';
        $_component['description']  = 'Игра на два человека, задача как можно быстрей найти ячейку';
        $_component['link']         = 'intuit';
        $_component['author']       = '<a href="http://www.instantcms.ru/users/somebody" target="_blank" >Димитриус</a>';
        $_component['internal']     = '0';
        $_component['version']      = '0.1';
        $_component['config'] = array(
             'rate_rat'=>1,
             'rate_kar'=>1,
             'rate_bil'=>0,			 
			 'Size_tabl'=>15,
             'TimeOutWaiting'=>300,
             'TimeOutTurn'=>150,
			 'seo_link'=>'game',
			 'seo_title'=>'game',
			 'seo_keywords'=>'game',
			 'seo_description'=>'game'
         );

        return $_component;

    }

// ========================================================================== //

    function install_component_intuit(){

        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных
        $inConf     = cmsConfig::getInstance();

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');

        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/intuit/install.sql', $inConf->db_prefix);


        return true;

    }

// ========================================================================== //

?>
