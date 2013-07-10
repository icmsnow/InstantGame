<?php

// ========================================================================== //

    function info_component_billing(){

        //Описание компонента

        $_component['title']        = 'Биллинг пользователей';
        $_component['description']  = 'Управляет финансовым балансом пользователей';
        $_component['link']         = 'billing';
        $_component['author']       = 'InstantSoft';
        $_component['internal']     = '0';
        $_component['version']      = '1.3';

        //Настройки по-умолчанию

        $_component['config'] = array(
                        'currency'      => 'руб.',
                        'packs_only'    => 0,
                        'point_cost'    => '10',
                        'subs_enabled'  => 0,
                        'discount'      => array(
                            '20' => 9,
                            '50' => 8,
                            '100' => 7
                        ),
                        'r2p_enabled'   => 1,
                        'p2r_enabled'   => 0,
                        'r2p_kurs'      => 10,
                        'p2r_kurs'      => 0.5,
                        'ref_enabled'   => 1,
                        'ref_bonus'     => 10,
                        'ref_percent'   => 10,
                        'ref_ttl'       => 100,
                        'ref_url'       => '/',
                        'reg_bonus'     => 50,
                        'in_enabled'    => 1,
                        'out_enabled'   => 0,
                        'out_period'    => 1,
                        'out_min'       => 100,
                        'out_kurs'      => 0.5,
                        'out_ps'        => "Webmoney\nЯндекс.Деньги",
                        'out_email'     => '',
                        'tf_enabled'    => 0,
                        'tf_confirm'    => 1,
                        'license_key'   => ''
        );

        return $_component;

    }

// ========================================================================== //

    function install_component_billing(){

        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных
        $inConf     = cmsConfig::getInstance();

        if (!$inDB->isFieldExists('cms_users', 'balance')){
            $sql = "ALTER TABLE `cms_users` ADD `balance` FLOAT NOT NULL DEFAULT '0'";
            $inDB->query($sql);
        }

        if (!$inDB->isFieldExists('cms_users', 'ref_id')){
            $sql = "ALTER TABLE `cms_users` ADD `ref_id` INT( 11 ) NOT NULL DEFAULT '0'";
            $inDB->query($sql);
        }

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');

        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/billing/install.sql', $inConf->db_prefix);

        $inCore->loadClass('cron');
        $inCore->loadClass('billing');

        cmsCron::registerJob('billing_check', array(
            'interval' => 24,
            'class_name' => 'billing|cmsBilling',
            'class_method' => 'checkSubscribers',
            'comment' => 'Отменяет просроченные платные переходы в группы (подписки)'
        ));

        cmsBilling::registerAction('forum', array('name' => 'add_thread', 'title' => 'Создание темы на форуме'));
        cmsBilling::registerAction('faq', array('name' => 'add_quest', 'title' => 'Добавление вопроса'));
        cmsBilling::registerAction('blogs', array('name' => 'add_post', 'title' => 'Добавление поста'));
        cmsBilling::registerAction('blogs', array('name' => 'add_blog', 'title' => 'Создание блога'));
        cmsBilling::registerAction('board', array('name' => 'add_item', 'title' => 'Добавление объявления'));
        cmsBilling::registerAction('catalog', array('name' => 'add_catalog_item', 'title' => 'Добавление записи в каталог'));
        cmsBilling::registerAction('content', array('name' => 'add_content', 'title' => 'Добавление статьи'));
        cmsBilling::registerAction('maps', array('name' => 'add_item', 'title' => 'Добавление объекта на карту'));
        cmsBilling::registerAction('maps', array('name' => 'add_news', 'title' => 'Добавление новости объекта'));
        cmsBilling::registerAction('maps', array('name' => 'add_event', 'title' => 'Добавление события объекта'));

        return true;

    }

// ========================================================================== //

    function upgrade_component_billing(){
        
        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных
        $inConf     = cmsConfig::getInstance();

        if (!$inDB->isFieldExists('cms_billing_tf', 'comment')){
            $sql = "ALTER TABLE  `cms_billing_tf` ADD  `comment` VARCHAR( 200 ) NULL DEFAULT NULL";
            $inDB->query($sql);
        }

        return true;
        
    }

// ========================================================================== //
