<?php

    function routes_maps(){

        $routes[] = array(
                            '_uri'  => '/^maps\/savecitypos$/i',
                            'do'    => 'save_city_pos'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/embed\/([0-9]+)$/i',
                            'do'    => 'embed',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/embed\-code\/([0-9]+)$/i',
                            'do'    => 'embed-code',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/(attend|unattend)\/(item|event)\/([0-9]+)$/i',
                            1       => 'do',
                            2       => 'object_type',
                            3       => 'object_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events$/i',
                            'do'    => 'events',
                            'cat_id'=> 'all'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/([0-9]+)\/add.html$/i',
                            'do'    => 'add_event',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/edit([0-9]+).html$/i',
                            'do'    => 'edit_event',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/delete([0-9]+).html$/i',
                            'do'    => 'delete_event',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/submit$/i',
                            'do'    => 'submit_event'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/([0-9]+).html$/i',
                            'do'    => 'event_read',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/(.+)\/([0-9]+)$/i',
                            'do'    => 'events',
                            1       => 'cat_id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\/(.+)$/i',
                            'do'    => 'events',
                            1       => 'cat_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\-by\/([0-9]+)$/i',
                            'do'    => 'events',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/events\-by\/([0-9]+)\/([0-9]+)$/i',
                            'do'    => 'events',
                            1       => 'item_id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news$/i',
                            'do'    => 'news',
                            'cat_id'=> 'all'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/([0-9]+)\/add.html$/i',
                            'do'    => 'add_news',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/edit([0-9]+).html$/i',
                            'do'    => 'edit_news',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/delete([0-9]+).html$/i',
                            'do'    => 'delete_news',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/submit$/i',
                            'do'    => 'submit_news'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/([0-9]+).html$/i',
                            'do'    => 'news_read',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/(.+)\/([0-9]+)$/i',
                            'do'    => 'news',
                            1       => 'cat_id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\/(.+)$/i',
                            'do'    => 'news',
                            1       => 'cat_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\-by\/([0-9]+)$/i',
                            'do'    => 'news',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/news\-by\/([0-9]+)\/([0-9]+)$/i',
                            'do'    => 'news',
                            1       => 'item_id',
                            2       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/abuse([0-9]+).html$/i',
                            'do'    => 'add_abuse',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/close_abuse\/([0-9]+)$/i',
                            'do'    => 'close_abuse',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/rate$/i',
                            'do'    => 'rate_item'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/accept([0-9]+).html$/i',
                            'do'    => 'accept_item',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/delete([0-9]+).html$/i',
                            'do'    => 'delete_item',
                            1       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/submit_item([0-9]+)$/i',
                            'do'    => 'submit_item',
                            1       => 'cat_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/update_item([0-9]+)\-([0-9]+)$/i',
                            'do'    => 'update_item',
                            1       => 'cat_id',
                            2       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/add.html$/i',
                            'do'    => 'add_item_global'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/add([0-9]+).html$/i',
                            'do'    => 'add_item',
                            1       => 'cat_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/edit([0-9]+)\-([0-9]+).html$/i',
                            'do'    => 'edit_item',
                            1       => 'cat_id',
                            2       => 'item_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/ajax\/get\-markers$/i',
                            'do'    => 'get-markers'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/ajax\/get\-info\/([0-9]+)$/i',
                            'do'    => 'get-info',
                            1       => 'marker_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/ajax\/get\-city\-info\/([0-9]+)$/i',
                            'do'    => 'get-city-info',
                            1       => 'city_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^maps\/ajax\/get\-city\-info\/(.+)\/(.+)$/i',
                            'do'    => 'get-city-info',
                            1       => 'country_name',
                            2       => 'city_name'
                         );

        //RewriteRule ^maps/download/([0-9]*)/([0-9]*)$ /index.php?view=shop&do=download_char&item_id=$1&char_id=$2
        $routes[] = array(
                            '_uri'  => '/^maps\/download\/([0-9]+)\/([0-9]+)$/i',
                            'do'    => 'download_char',
                            1       => 'item_id',
                            2       => 'char_id'
                         );

        //RewriteRule ^maps/compare.html$ /index.php?view=shop&do=compare
        $routes[] = array(
                            '_uri'  => '/^maps\/compare.html$/i',
                            'do'    => 'compare'
                         );

        //RewriteRule ^maps/compare/remove/([0-9]*)$ /index.php?view=shop&do=compare_remove&item_id=$1
        $routes[] = array(
                            '_uri'  => '/^maps\/compare\/remove\/([0-9]+)$/i',
                            'do'    => 'compare_remove',
                            1       => 'item_id'
                         );

        //RewriteRule ^maps/compare/([0-9]*)$ /index.php?view=shop&do=compare&item_id=$1
        $routes[] = array(
                            '_uri'  => '/^maps\/compare\/([0-9]+)$/i',
                            'do'    => 'compare',
                            1       => 'item_id'
                         );

        //RewriteRule ^maps/vendors.html$ /index.php?view=shop&do=vendors
        $routes[] = array(
                            '_uri'  => '/^maps\/vendors.html$/i',
                            'do'    => 'vendors'
                         );

        //RewriteRule ^maps/vendors/([0-9]*)$ /index.php?view=shop&do=view_vendor&vendor_id=$1
        $routes[] = array(
                            '_uri'  => '/^maps\/vendors\/([0-9]+)$/i',
                            'do'    => 'view_vendor',
                            1       => 'vendor_id'
                         );

        //RewriteRule ^maps/vendors/([0-9]*)/page-([0-9]*)$ /index.php?view=shop&do=view_vendor&vendor_id=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^maps\/vendors\/([0-9]+)\/page\-([0-9]+)$/i',
                            'do'    => 'view_vendor',
                            1       => 'vendor_id',
                            2       => 'page'
                         );

        //RewriteRule ^maps/(.*).html$ /index.php?view=shop&do=item&seolink=$1
        $routes[] = array(
                            '_uri'  => '/^maps\/(.+).html$/i',
                            'do'    => 'item',
                            1       => 'seolink'
                         );


        //RewriteRule ^maps/(.*)/page-([0-9]*)$ /index.php?view=shop&do=view&seolink=$1&page=$2
        $routes[] = array(
                            '_uri'  => '/^maps\/(.+)\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'seolink',
                            2       => 'page'
                         );

        //RewriteRule ^maps/([0-9]*)/(.*)/page-([0-9]*)/(.*)$ /index.php?view=shop&do=view&seolink=$1&page=$2&filter_str=$3
        $routes[] = array(
                            '_uri'  => '/^maps\/(.+)\/page\-([0-9]+)\/(.*)$/i',
                            'do'    => 'view',
                            1       => 'seolink',
                            2       => 'page',
                            3       => 'filter_str'
                         );

        //RewriteRule ^maps/(.*)$ /index.php?view=shop&do=view&seolink=$1
        $routes[] = array(
                            '_uri'  => '/^maps\/(.+)$/i',
                            'do'    => 'view',
                            1       => 'seolink'
                         );

        return $routes;

    }

?>
