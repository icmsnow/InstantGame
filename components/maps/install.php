<?php

// ========================================================================== //

    function info_component_maps(){

        //Описание компонента

        $_component['title']        = 'InstantMaps';                        //название
        $_component['description']  = 'Каталог объектов на карте';          //описание
        $_component['link']         = 'maps';                               //ссылка (идентификатор)
        $_component['author']       = 'InstantSoft';                        //автор
        $_component['internal']     = '0';                                  //внутренний (только для админки)? 1-Да, 0-Нет
        $_component['version']      = '2.3';                                //текущая версия

        //Настройки по-умолчанию

        $_component['config'] = array(
            'mode'=>'city',
            'country'=>'Россия',
            'city'=>'Екатеринбург',
            'city_sel'=>'base',
            'autocity'=>1,
            'maps_engine'=>'yandex',
            'map_filter'=>1,
            'map_type'=>'map',
            'minimap_type'=>'map',
            'yandex_key'=>'',
            'show_cats'=>1,
            'show_filter'=>1,
            'show_subcats'=>1,
            'show_subcats2'=>1,
            'show_desc'=>1,
            'show_full_desc'=>1,
            'desc_filters'=>0,
            'show_thumb'=>1,
            'show_compare'=>1,
            'show_char_grp'=>1,
            'show_user'=>1,
            'img_w'=>350,
            'img_h'=>350,
            'thumb_w'=>150,
            'thumb_h'=>150,
            'img_sqr'=>0,
            'thumb_sqr'=>1,
            'waterwark'=>0,
            'perpage'=>15,
            'published_add'=>0,
            'published_edit'=>0,
            'allow_edit'=>1,
            'cat_order_by'=>'ordering',
            'cat_order_to'=>'asc',
            'comments'=>1,
            'ratings'=>1,
            'subcats_order'=>'title',
            'city_zoom_level'=>8,
            'zoom_city'=>11,
            'zoom_country'=>3,
            'zoom_minimap'=>15,
            'zoom_min'=>3,
            'zoom_max'=>15,
            'prefixes'=>array(
                 'улица' => 'ул.',
                 'переулок' => 'пер.',
                 'проспект' => 'пр-кт',
                 'проезд' => 'пр-д',
                 'шоссе' => 'шоссе',
                 'аллея' => 'аллея',
                 'площадь' => 'пл.',
                 'бульвар' => 'бульвар',
                 'набережная' => 'наб.'
            ),
            'show_default' => 'city',
            'news_enabled' => 1,
            'news_html' => 1,
            'news_limit' => 3,
            'news_period' => 'DAY',
            'news_show' => 15,
            'events_enabled' => 1,
            'events_add_any' => 1,
            'events_html' => 1,
            'events_limit' => 3,
            'events_period' => 'DAY',
            'events_show' => 15,
            'show_markers' => 1,
            'load_limit' => 25,
            'items_attend' => 1,
            'items_abuses' => 1,
            'items_embed' => 1,
            'events_attend' => 1,
            'multiple_addr' => 1,
            'join_same_addr' => 1,
            'news_cm' => 1,
            'events_cm' => 1,
            'show_homepage' => 'all',
            'show_map' => 1,
            'show_map_in_cats' => 1,
            'unfront_edit' => 1,
            'show_cats_pos' => 'top',
            'selmap_lng' => '',
            'selmap_lat' => '',
            'center_lng' => '',
            'center_lat' => '',
            'cl_grid' => '64',
            'cl_zoom' => '15',
            'cl_size' => '2',
            'show_rss' => 1,
            'show_nested' => 1,
            'can_edit_cats' => 1,
            'moder_notify' => 'both',
            'moder_mail' => '',
            'license_key' => ''
        );

        return $_component;

    }

// ========================================================================== //

    function install_component_maps(){

        $inConf = cmsConfig::getInstance();

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');

        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/maps/install.sql', $inConf->db_prefix);
        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/maps/geo.sql', $inConf->db_prefix);

        cmsCore::registerCommentsTarget('mapitem', 'maps', 'Объекты на карте', 'cms_map_items', 'вашего объекта');
        cmsCore::registerCommentsTarget('mapnews', 'maps', 'Новости объектов на карте', 'cms_map_news', 'вашей новости');
        cmsCore::registerCommentsTarget('mapevent', 'maps', 'События объектов на карте', 'cms_map_events', 'вашего события');

        return true;

    }

// ========================================================================== //

    function upgrade_component_maps(){

        $inDB = cmsDatabase::getInstance();

        $inDB->query("UPDATE `cms_comment_targets` SET target_table='cms_map_items', subj='вашего объекта' WHERE component='maps' AND target='mapitem'");
        $inDB->query("UPDATE `cms_comment_targets` SET target_table='cms_map_news', subj='вашей новости' WHERE component='maps' AND target='mapnews'");
        $inDB->query("UPDATE `cms_comment_targets` SET target_table='cms_map_events', subj='вашего события' WHERE component='maps' AND target='mapevent'");

        return true;

    }

// ========================================================================== //

?>