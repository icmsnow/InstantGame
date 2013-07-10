<?php

    function routes_billing(){

        $routes[] = array(
                            '_uri'  => '/^billing\/get\-payment\/(.+)$/i',
                            'do'    => 'process_payment',
                            1       => 'psys_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/success.html$/i',
                            'do'    => 'success'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/pay$/i',
                            'do'    => 'payment'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/fail.html$/i',
                            'do'    => 'fail'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/accept.html$/i',
                            'do'    => 'accept'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/accept.html$/i',
                            'do'    => 'accept'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/low.html$/i',
                            'do'    => 'low'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/convert.html$/i',
                            'do'    => 'convert'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/buy_convert$/i',
                            'do'    => 'buy_convert'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/subscribe.html$/i',
                            'do'    => 'subscribe'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/buy_sub$/i',
                            'do'    => 'buy_sub'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/ref_link\/([0-9]+)$/i',
                            'do'    => 'ref_link',
                            1       => 'ref_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/referals([0-9]+).html$/i',
                            'do'    => 'referals',
                            1       => 'user_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/out.html$/i',
                            'do'    => 'out'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/submit_out$/i',
                            'do'    => 'submit_out'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/delete_out\/([0-9]+)$/i',
                            'do'    => 'delete_out',
                            1       => 'out_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/cf\/([a-z0-9]{32})$/i',
                            'do'    => 'confirm_out',
                            1       => 'code'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/done_out\/([0-9]+)$/i',
                            'do'    => 'done_out',
                            1       => 'out_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/transfer.html$/i',
                            'do'    => 'transfer'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/submit_transfer$/i',
                            'do'    => 'submit_transfer'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/delete_tf\/([0-9]+)$/i',
                            'do'    => 'delete_tf',
                            1       => 'tf_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/tf\/([a-z0-9]{32})$/i',
                            'do'    => 'confirm_tf',
                            1       => 'code'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/delete_op\/([0-9]+)$/i',
                            'do'    => 'delete_op',
                            1       => 'op_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^billing\/payment\/qiwi.html$/i',
                            'do'    => 'qiwi'
                         );

        return $routes;

    }

?>
