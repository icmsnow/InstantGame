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

	function routes_intuit(){
        $routes[] = array(
                            '_uri'  => '/^intuit\/get$/i',
                            'do'    => 'add'
                         );

        $routes[] = array(
                            '_uri'  => '/^intuit\/([0-9]+)\/play.html$/i',
                            'do'    => 'play',
							1 		=> 'game_id'
                         );
        $routes[] = array(
                            '_uri'  => '/^intuit\/add$/i',
                            'do'    => 'add'
                         );
          return $routes;

    }

?>
