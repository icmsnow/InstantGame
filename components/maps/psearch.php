<?php
/*********************************************************************************************/
//																							 //
//                            InstantMaps v1.0 (c) 2010 InstanSoft                           //
//	 					  http://www.instantsoft.ru/, r2@instansoft.ru                       //
//                                                                                           //
//                                written by InstantCMS Team                                 //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function search_maps($query, $look){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$searchModel = cms_model_search::initModel();

		//BUILD SQL QUERY
		$sql = "SELECT i.*, c.title as cat, c.seolink as catlink
				FROM cms_map_items i, cms_map_cats c
				WHERE MATCH(i.title, i.shortdesc, i.description) AGAINST ('$query' IN BOOLEAN MODE) AND
                      i.category_id = c.id AND
                      i.published = 1";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);

		if ($found){
			while($item = $inDB->fetch_assoc($result)){

				$result_array = array();

				$result_array['link']        = "/maps/".$item['seolink'].".html";
				$result_array['place']       = $item['cat'];
				$result_array['placelink']   = "/maps/".$item['catlink'];
				$result_array['description'] = $searchModel->getProposalWithSearchWord($item['description']);
				$result_array['title']       = mysql_real_escape_string($item['title']);
				$result_array['pubdate']     = $item['pubdate'];
				$result_array['session_id']  = session_id();

				$searchModel->addResult($result_array);

			}
		}

		return;
}


?>
