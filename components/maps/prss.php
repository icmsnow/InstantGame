<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function rss_maps($item_id, $cfg, &$rssdata){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

		global $_CFG;
		global $_LANG;

        $inCore->loadModel('maps');
        $model = new cms_model_maps();

		$maxitems   = $cfg['maxitems'];
		$rooturl    = 'http://'.$_SERVER['HTTP_HOST'];

		if ($item_id == 'all') { $item_id = 0; }

		$channel = array();

		//CHANNEL
		if ($item_id){
			$cat = dbGetFields('cms_map_cats', "id='$item_id'", 'id, title, description, seolink, NSLeft, NSRight');
			$catsql = "AND c.category_id = cat.id AND cat.NSLeft >= {$cat['NSLeft']} AND cat.NSRight <= {$cat['NSRight']}";

			$channel['title'] = $cat['title'] ;
			$channel['description'] = $cat['description'];
			$channel['link'] = $rooturl . '/maps/' . $cat['seolink'];
		} else {
			$catsql = '';

			$channel['title'] = 'Новое на карте';
			$channel['description'] = 'Лента объектов на карте';
			$channel['link'] = $rooturl;
		}

		//ITEMS
		$sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%a, %d %b %Y %H:%i:%s GMT') as pubdate, cat.title as category
				FROM cms_map_items c, cms_map_cats cat
				WHERE c.published=1 AND c.category_id = cat.id $catsql
				ORDER by c.pubdate DESC
				LIMIT $maxitems";

		$rs = $inDB->query($sql) or die('RSS building error!');

		$items = array();

		if ($inDB->num_rows($rs)){

			while ($item = $inDB->fetch_assoc($rs)){
				$id = $item['id'];
				$items[$id] = $item;
                $items[$id]['link'] = $rooturl . '/maps/' . $item['seolink'] . '.html';
				$items[$id]['comments'] = $items[$id]['link'].'#c';
				$items[$id]['category'] = $item['category'];

                $image_file = PATH.'/images/photos/small/map'.$id.'.jpg';
                $image_url  = $rooturl . '/images/photos/small/map'.$id.'.jpg';

                $items[$id]['image'] = file_exists($image_file) ? $image_url : '';
				$items[$id]['size']  = round(filesize($image_file));
			}

		}

		//RETURN
		$rssdata = array();
		$rssdata['channel'] = $channel;
		$rssdata['items'] = $items;

		return;
}


?>