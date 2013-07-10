<?php

    function routes_magictree(){

        $routes[] = array(
                            '_uri'  => '/^magictree\/add.html$/i',
                            'do'    => 'add'
                         );
        $routes[] = array(
                            '_uri'  => '/^magictree\/tree([0-9]+).html$/i',
                            'do'    => 'show',
                            1       => 'id'
                         );
						 
        $routes[] = array(
                            '_uri'  => '/^elki\/delelka([0-9]+).html$/i',
                            'do'    => 'delelka',
                            1       => 'id'
                         );
        $routes[] = array(
                            '_uri'  => '/^elki\/delpod([0-9]+).html$/i',
                            'do'    => 'delpod',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^elki\/add.html$/i',
                            'do'    => 'add'
                         );
        $routes[] = array(
                            '_uri'  => '/^elki\/add_pod([0-9]+).html$/i',
                            'do'    => 'add_pod',
                            1       => 'id'
                         );
        $routes[] = array(
                            '_uri'  => '/^elki\/podarki([0-9]+).html$/i',
                            'do'    => 'podarki',
                            1       => 'id'
                         );
        $routes[] = array(
                            '_uri'  => '/^elki\/podarki([0-9]+)-([0-9]+).html$/i',
                            'do'    => 'podarki',
                            1       => 'id',
            				2       => 'page'
                         );
        return $routes;

    }

?>
