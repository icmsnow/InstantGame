<?php

    function info_module_mod_game_stat(){

        //
        // Описание модуля
        //

        //Заголовок (на сайте)
        $_module['title']        = 'Статистика игр';

        //Название (в админке)
        $_module['name']         = 'Статистика игр';

        //описание
        $_module['description']  = 'Отображает стастистку: кол-во игрков которые играют, игр за месяц, неделю, вчера, сегодня';
        
        //ссылка (идентификатор)
        $_module['link']         = 'mod_game_stat';
        
        //позиция
        $_module['position']     = 'maintop';

        //автор
        $_module['author']       = '<a href="http://www.instantcms.ru/users/somebody" target="_blank" >Димитриус</a>';

        //текущая версия
        $_module['version']      = '0.1';

        //
        // Настройки по-умолчанию
        //
        $_module['config'] = array();

        return $_module;

    }

// ========================================================================== //

    function install_module_mod_game_stat(){

        return true;

    }

// ========================================================================== //

    function upgrade_module_mod_game_stat(){

        return true;
        
    }

// ========================================================================== //

?>