<?php
    function custom_rewrite_rules(){

	$seo_link ='game';
       $rules[] = array(
                            'source'  => '/^'.$seo_link.'\/get$/ui',
                            'target'  => 'intuit/get',
                            'action'  => 'rewrite'
                         );
        $rules[] = array(
                            'source'  => '/^intuit\/get$/ui',
                            'target'  => $seo_link,
                            'action'  => 'redirect-301'
                         );
						 
       $rules[] = array(
                            'source'  => '/^'.$seo_link.'\/([0-9]+)\/play.html$/i',
                            'target'  => 'intuit/{1}/play.html',
                            'action'  => 'rewrite'
                         );
        
		$rules[] = array(
							'source' =>'/^intuit\/([0-9]+)\/play.html$/i',
							'target' => $seo_link.'/{1}/play.html',
							'action' => 'redirect-301'
						);

					 
        $rules[] = array(
                            'source'  => '/^'.$seo_link.'$/ui',
                            'target'  => 'intuit',
                            'action'  => 'rewrite'
                         );
        $rules[] = array(
                            'source'  => '/^intuit$/ui',
                            'target'  => ''.$seo_link.'',
                            'action'  => 'redirect-301'
                         );
        return $rules;

    }

?>
