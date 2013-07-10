<?php
/*********************************************************************************************/
//                                                                                           //
//                            InstantMaps v1.6 (c) 2011 InstanSoft                           //
//                       http://www.instantsoft.ru/, support@instansoft.ru                   //
//                                                                                           //
//                                written by InstantCMS Team                                 //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function maps(){

    global $_LANG;
    global $_CFG;

    setlocale(LC_NUMERIC, 'POSIX');

    //подключим нужные классы
    $inCore     = cmsCore::getInstance();       //ядро
    $inConf     = cmsConfig::getInstance();       //ядро
    $inPage     = cmsPage::getInstance();       //страница
    $inDB       = cmsDatabase::getInstance();   //база данных
    $inUser     = cmsUser::getInstance();       //пользователь

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

    //получим ID текущего пункта меню
    $menuid     = $inCore->menuId();
    $menutitle  = $inCore->menuTitle();

    $is_homepage = (bool)($menuid==1);

    if ($is_homepage){ $menutitle = ''; }

    //загружаем модель
    $inCore->loadModel('maps');
    $model = new cms_model_maps();

    //загрузим конфиг компонента
    $cfg = $model->getConfig();

	// Проверяем включен ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }

    //получаем входные параметры
    $id         = $inCore->request('id', 'int', 0);
    $seolink    = $inCore->request('seolink', 'str', '');
    $do         = $inCore->request('do', 'str', 'view');

    $page       = $inCore->request('page', 'int', 1);
    $perpage    = $cfg['perpage'];

    //Подключаем CSS к странице
    $inPage->addHeadCSS('templates/'.$inConf->template.'/css/inmaps.css');

    //узнаем город пользователя
    $location = $model->detectUserLocation();

//============================================================================//
//============================================================================//

    if ($do=='get-markers'){

        $load_limit = $cfg['load_limit'];

        $tree_cat   = $inCore->request('tree_cat', 'array');
        $from       = $inCore->request('from', 'int', 0);

        $city_name = $inCore->request('user_city', 'str', '');
        $city_name = urldecode($city_name);

        $city = $city_name ? $city_name : $location['city'];

        $json = '';

        if (!$tree_cat) { $is_items = false; }

        //устанавливаем нужную категорию
        if ($tree_cat){

            $total      = $model->getMarkersCount($city, $tree_cat);
            $use_limit  = ($load_limit && $total > $load_limit);

            $items      = $model->getMarkers($city, $tree_cat, $use_limit, $from, $load_limit);
            $is_items   = is_array($items);

            if ($use_limit) {
                $pages = ceil($total / $load_limit);
            } else {
                $pages = 1;
            }

        }

        $cities     = $model->getCities();
        $is_cities  = is_array($cities);

        if ($is_items || $is_cities){
            $json = 'var markers_list = [';
        }

        if ($is_items){
            foreach($items as $item){
                $item['is_addr'] = $item['addr_city'] ? 1 : 0;
                $json .= "\n{
                    id: {$item['id']},
                    lng: '{$item['lng']}',\n
                    lat: '{$item['lat']}',\n
                    icon: '{$item['marker']}',
                    is_addr: {$item['is_addr']},
                    zoom: {$item['zoom']}
                },";
            }
        }

        if ($is_cities){
            foreach($cities as $id=>$city){
                $json .= "\n{
                    is_city: true,\n
                    id: {$city['id']},\n
                    city: '{$city['title']}',\n
                    country: '{$city['country']}',\n
                    lat: '{$city['lat']}',\n
                    lng: '{$city['lng']}',\n
                    icon: ''\n
                },";
            }
        }

        if ($json){

            $json = rtrim($json, ',');
            $json .= "];\n";

            if (!$total) { $total = 0; }
            if (!$pages) { $pages = 0; }

            $json .= "var total     = {$total};\n";
            $json .= "var pages     = {$pages};\n";
            $json .= "var perpage   = {$load_limit};\n";

            echo $json;

        }

        $inCore->halt();

    }

//============================================================================//
//============================================================================//

    if ($do=='get-info'){

        $marker_id = $inCore->request('marker_id', 'int', '0');

        if (!$marker_id) { exit; }

        $items = $model->getItemsByMarker($marker_id);

        if (sizeof($items)>1 && $cfg['join_same_addr']){
            $is_many = true;
        } else {
            $item           = $items[0];
            $item['category'] = $model->getCategory($item['category_id']);
            $news_count     = $cfg['news_enabled'] ? $model->getNewsCountForObject($item['id']) : false;
            $events_count   = $cfg['events_enabled'] ? $model->getEventsCountForObject($item['id']) : false;
            $is_many        = false;
        }

        $tpl = $is_many ? 'com_inmaps_many_short.tpl' : 'com_inmaps_item_short.tpl';

        $smarty = $inCore->initSmarty('components', $tpl);
        $smarty->assign('cfg', $cfg);

        if ($is_many){
            $smarty->assign('items', $items);
        } else {
            $smarty->assign('item', $item);
            $smarty->assign('marker_id', $marker_id);
            $smarty->assign('news_count', $news_count);
            $smarty->assign('events_count', $events_count);
        }

        $smarty->display($tpl);

        $inCore->halt();

    }

//============================================================================//
//============================================================================//

    if ($do=='get-city-info'){

        $city_id = $inCore->request('city_id', 'int', false);

        if ($city_id){

            $city = $model->getCityById($city_id);

        }

        if (!$city_id){

            $country_name = $inCore->request('country_name', 'str', '');
            $country_name = urldecode($country_name);
            if (!$country_name) { $country_name = $cfg['country']; }

            $city_name = $inCore->request('city_name', 'str', '');
            $city_name = urldecode($city_name);
            if (!$city_name) { exit; }

            $city = $model->getCity($city_name);

        }

        if (!$city) { exit; }

        $smarty = $inCore->initSmarty('components', 'com_inmaps_city_short.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('city', $city);
        $smarty->display('com_inmaps_city_short.tpl');

        $inCore->halt();

    }

//============================================================================//
//============================================================================//

    //
    // ПРОСМОТР КАТЕГОРИИ
    //

    if ($do=='view'){

        // -------- получаем категорию --------------

        if (!$seolink){
            //Корневая категория
            $root_cat           = $model->getRootCategory();
            $root_cat['title']  = ($menuid > 1 ? $menutitle : '');
        }

        if ($seolink){
            //Внутренняя (не корневая) категория
            $root_cat   = $model->getCategoryByLink($seolink);
            $path_list  = $model->getCategoryPath($root_cat['NSLeft'], $root_cat['NSRight']);
        }

        //Если не найдена - 404
        if (!$root_cat){ cmsCore::error404(); }

        $_SESSION['inmaps_last_url'] = $_SERVER['REQUEST_URI'];

        //Ставим заголовки страницы
        if (!$root_cat['seotitle']){ $root_cat['seotitle'] = $root_cat['title']; }
        $page_title = $root_cat['seotitle'];
        if($page>1) { $page_title .= ' - '.$_LANG['PAGE'].' '.$page; }
        $inPage->setTitle($page_title);

        //Устанавливаем заголовок страницы
        if ($root_cat['metakeys']){ $inPage->setKeywords($root_cat['metakeys']); }
        if ($root_cat['metadesc']){ $inPage->setDescription($root_cat['metadesc']); }

        //Если у категории есть родители, выводим их в глубиномере
        if ($path_list){
            foreach($path_list as $pcat){
                $inPage->addPathway($pcat['title'], '/maps/'.$pcat['seolink']);
            }
        }

        //выводим название категории (или пункта меню, если это корневой раздел)
        $inPage->addPathway($root_cat['title']);

        //получаем подкатегории
        if (!$is_homepage || $cfg['show_homepage']=='all'){
            $subcats = $model->getSubCats($root_cat['id']);
        }

        // ------- получаем значения фильтров -----------------

        $filter     = array();
        $filter_str = $inCore->request('filter_str', 'str', '');


        if ($filter_str){
            $filter_str = urldecode($filter_str);
            $filter = $model->parseFilterString($filter_str);
        }
        if ($inCore->inRequest('filter')) { $filter = $inCore->request('filter', 'array'); }

        if (is_array($filter)){
            foreach($filter as $key=>$val){

                if ($val && $key){

                    //характеристика с одним значением (select)
                    if (!is_array($val)){
                        switch($key){
                            case 'pfrom':  $model->wherePriceFrom($val); break;
                            case 'pto':    $model->wherePriceTo($val); break;
                            default:       $model->whereCharIs($key, $val); break;
                        }
                    }

                    //характеристика с множеством значений (checkbox)
                    if (is_array($val)){
                        $model->whereCharIn($key, $val);
                    }

                }

            }
            if (!$filter_str) { $filter_str = $model->makeFilterString($filter); }
        }

        //получаем хар-ки категории
        $chars = $model->getCatChars($root_cat['id']);

        //хар-ки фильтра

        foreach($chars as $char){
            if ($char['is_filter']){
                $filter_chars[] = $char;
            }
        }

        // ------- готовим карту -----------------

        $checked_cats = array($root_cat['id']);
        $hidden_pairs = array();

        if ($is_homepage || ($root_cat['id']==1 && $cfg['show_map']) || ($root_cat['id']>1 && $cfg['show_map_in_cats'])){

            if (in_array($cfg['maps_engine'], array('yandex', 'narod', 'custom'))){
                $key = $cfg['yandex_key'];
            } else {
                $key = $cfg[$cfg['maps_engine'].'_key'];
            }

            $inCore->includeFile('components/maps/systems/'.$cfg['maps_engine'].'/info.php');
            $inPage->addHeadJS('components/maps/systems/'.$cfg['maps_engine'].'/geo.js');
            $inPage->addHeadJS('components/maps/js/map.js');
            $inPage->addHeadJS('includes/jquery/jquery.form.js');
            $api_key = str_replace('#key#', $key, $GLOBALS['MAP_API_URL']);
            $inPage->page_head[] = $api_key;

            $cats_tree = $model->getCategories(true);

            if (($root_cat['id']==1 && $cfg['show_markers']) || ($root_cat['id']>1 && $cfg['show_nested'])){
                foreach($cats_tree as $cat){
                    if ($cat['NSLeft']>$root_cat['NSLeft'] && $cat['NSRight']<$root_cat['NSRight'] && !$cat['config']['hide']){

                        $is_hidden_parent = false;

                        if ($hidden_pairs){
                            foreach($hidden_pairs as $pair){
                                if ($cat['NSLeft']>$pair[0] && $cat['NSRight']<$pair[1]){
                                    $is_hidden_parent = true;
                                    break;
                                }
                            }
                        }

                        if (!$is_hidden_parent){
                            $checked_cats[] = $cat['id'];
                        }

                    }
                    if ($cat['config']['hide']){
                        $hidden_pairs[] = array($cat['NSLeft'], $cat['NSRight']);
                    }
                }
            }

        }

        // ------- получаем объекты -----------------

        //устанавливаем нужную категорию
        if ($root_cat['id']==1 || ($root_cat['id']>1 && !$cfg['show_nested'])){
            $model->whereCatIs($root_cat['id']);
        } else {
            $model->whereRecursiveCatIs($root_cat['id'], array('NSLeft'=>$root_cat['NSLeft'], 'NSRight'=>$root_cat['NSRight']));
        }

        if ($location['city']){
            //город
            $city_has_objects = $model->whereCityMaybeIs($location['city']);
        } else {
            $city_has_objects = true;
        }

        $model->groupBy('i.id');

        $model->where('i.published=1');

        //узнаем сколько всего подходящих объектов в базе
        $total = $model->getItemsCount();

        //устанавливаем сортировку "по порядку"
        $model->orderBy($cfg['cat_order_by'], $cfg['cat_order_to']);

        //устанавливаем номер текущей страницы и кол-во объектов на странице
        $model->limitPage($page, $perpage);

        //получим все подходящие объекты на текущей странице
        $items = $model->getItems();

        //проверим возможность добавления записей
        $is_cat_access = $model->checkCategoryAccess($root_cat['id'], $root_cat['is_public'], $inUser->group_id);
        $is_can_add = $is_cat_access || $inUser->is_admin;

        //считаем конечное число страниц
        $pages = ceil($total / $perpage);

        $pages_url = '/maps/'.$root_cat['seolink'].'/page-%page%';

        if ($filter_str){ $pages_url .= '/' . urlencode($filter_str); }

        $pagebar = cmsPage::getPagebar($total, $page, $perpage, $pages_url);

        //проверяем что задан шаблон
        if (!$root_cat['tpl']) { $root_cat['tpl'] = 'com_inmaps_view.tpl'; }

        //передаем все в шаблон
        $smarty = $inCore->initSmarty('components', $root_cat['tpl']);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('subcats', $subcats);
        $smarty->assign('items', $items);
        $smarty->assign('chars', $chars);
        $smarty->assign('filter_chars', $filter_chars);
        $smarty->assign('filter', $filter);
        $smarty->assign('filter_str', $filter_str);
        $smarty->assign('total', $total);
        $smarty->assign('pages', $pages);
        $smarty->assign('page', $page);
        $smarty->assign('pagebar', $pagebar);
        $smarty->assign('root_cat', $root_cat);
        $smarty->assign('is_can_add', $is_can_add);
        $smarty->assign('is_user', $inUser->id);
        $smarty->assign('is_homepage', $is_homepage);
        $smarty->assign('location', $location);
        $smarty->assign('city_has_objects', $city_has_objects);
        if ($cats_tree) {
            $smarty->assign('cats_tree', $cats_tree);
            $smarty->assign('last_level', -1);
            $smarty->assign('hide_parent', 0);
            $smarty->assign('checked_cats', $checked_cats);
        }
        $smarty->display($root_cat['tpl']);

    }

//============================================================================//
//============================================================================//

    //
    // ПРОСМОТР ОБЪЕКТА
    //

    if ($do=='item'){

        //если нет ссылки - ошибка
        if (!$seolink) { cmsCore::error404(); }

        //получаем объект по ссылке
        $item = $model->getItemBySeolink($seolink);

        //если объект не найден - ошибка
        if (!$item) { cmsCore::error404(); }

        if (!$item['published'] && $item['user_id'] != $inUser->id && !$inUser->is_admin) { cmsCore::error404(); }

        if ($item['images']){
            $inPage->addHeadJS('includes/jquery/lightbox/js/jquery.lightbox.js');
            $inPage->addHeadCSS('includes/jquery/lightbox/css/jquery.lightbox.css');
        }

        //Устанавливаем заголовок страницы
        if (!$item['seotitle']){ $item['seotitle'] = $item['title']; }
        $inPage->setTitle($item['seotitle']);

        if ($item['metakeys']){ $inPage->setKeywords($item['metakeys']); }
        if ($item['metadesc']){ $inPage->setDescription($item['metadesc']); }

        $_SESSION['inmaps_last_url'] = $_SERVER['REQUEST_URI'];

        //получаем путь к объекту (список категорий)
        $path_list  = $model->getCategoryPath($item['category']['NSLeft'], $item['category']['NSRight']);

        //Если у категории есть родители, выводим их в глубиномере
        if ($path_list){
            foreach($path_list as $pcat){
                $inPage->addPathway($pcat['title'], '/maps/'.$pcat['seolink']);
            }
        }

        $inPage->addPathway($item['title']);

        // ------- готовим карту -----------------

        if (in_array($cfg['maps_engine'], array('yandex', 'narod', 'custom'))){
            $key = $cfg['yandex_key'];
        } else {
            $key = $cfg[$cfg['maps_engine'].'_key'];
        }

        $inCore->includeFile('components/maps/systems/'.$cfg['maps_engine'].'/info.php');
        $inPage->addHeadJS('components/maps/systems/'.$cfg['maps_engine'].'/geo.js');
        $inPage->addHeadJS('components/maps/js/map.js');
        $api_key = str_replace('#key#', $key, $GLOBALS['MAP_API_URL']);
        $inPage->page_head[] = $api_key;

        if ($cfg['desc_filters']){
            $filters = $inCore->getFilters();
            if ($filters){
                foreach($filters as $id=>$_data){
                    require_once PATH.'/filters/'.$_data['link'].'/filter.php';
                    $_data['link']($item['description']);
                }
            }
        }

        //проверяем что задан шаблон
        if (!$item['tpl']) { $item['tpl'] = 'com_inmaps_item.tpl'; }

        //передаем все в шаблон
		$smarty = $inCore->initSmarty('components', $item['tpl']);
        $smarty->assign('cfg', $cfg);
		$smarty->assign('item', $item);
		$smarty->assign('is_admin', $inUser->is_admin);
		$smarty->assign('is_user', $inUser->id);
		$smarty->display($item['tpl']);

        if($inCore->isComponentInstalled('comments') && $cfg['comments']){
            $inCore->includeComments();
            comments('mapitem', $item['id'], array(
                'comments' => $_LANG['MAPS_REVIEWS'],
                'add' => $_LANG['MAPS_REVIEWS_ADD'],
                'rss' => $_LANG['MAPS_REVIEWS_RSS'],
                'not_comments' => $_LANG['MAPS_REVIEWS_NO']
            ));
        }

    }

//============================================================================//
//============================================================================//

    if ($do=='compare'){

        $inPage->setTitle($_LANG['MAPS_COMPARE']);
        $inPage->addPathway($_LANG['MAPS_COMPARE'], '/maps/compare.html');

        $add_item_id = $inCore->request('item_id', 'int', 0);

        if ($add_item_id) { $model->addCompareItem($add_item_id); }

        $items = $model->getCompareItems();
        $chars = $model->getChars(true, '', true); //only_published, all groups, only_compare

        $cmp_chars = array();

        foreach($chars as $char_id=>$char){
            if ($char['is_compare']){
                foreach($items as $num=>$item){
                    if ($item['chars'][$char_id]){
                        $cmp_chars[$char['title']][$item['id']] = $item['chars'][$char_id]['value'];
                    }
                }
            }
        }

        //передаем все в шаблон
		$smarty = $inCore->initSmarty('components', 'com_inmaps_compare.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('items', $items);
		$smarty->assign('cmp_chars', $cmp_chars);
        $smarty->assign('last_url', $_SESSION['inmaps_last_url']);
        $smarty->display('com_inmaps_compare.tpl');

    }

    if ($do=='compare_remove'){

        $item_id = $inCore->request('item_id', 'int', 0);

        $model->deleteCompare($item_id);

        $inCore->redirectBack();

    }

//============================================================================//
//============================================================================//

    //
    // ЗАГРУЗКА ФАЙЛА ХАРАКТЕРИСТИКИ
    //

    if ($do=='download_char'){

        $item_id = $inCore->request('item_id', 'int', 0);
        $char_id = $inCore->request('char_id', 'int', 0);

        if (!$item_id || !$char_id) { $inCore->halt('no item'); }

        $file = '/upload/userfiles/maps-char-'.$item_id.'-'.$char_id.'.file';

        if (!file_exists(PATH. $file)) { $inCore->halt('no file'); }

        $data = $inDB->get_field('cms_map_chars_val', "item_id={$item_id} AND char_id={$char_id}", 'val');

        $data = $inCore->yamlToArray($data);

        if (!is_array($data)) { $inCore->halt('no data'); }

        header('Content-Disposition: attachment; filename='.$data['name'] . "\n");
        header('Content-Type: application/x-force-download; name="'.$file.'"' . "\n\n");

        echo file_get_contents(PATH . $file);

        $inCore->halt();

    }

//============================================================================//
//============================================================================//

    if ($do=='add_item_global'){

        $inUser = cmsUser::getInstance();

        if (!$inUser->id) { cmsUser::goToLogin(); }

        //устанавливаем заголовки
        $inPage->setTitle($_LANG['MAPS_ADD_OBJECT']);
        $inPage->addPathway($_LANG['MAPS_ADD_OBJECT']);

        $cats = $model->getAllowedCats();

		$smarty = $inCore->initSmarty('components', 'com_inmaps_add_select.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('cats', $cats);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->display('com_inmaps_add_select.tpl');

    }

//============================================================================//
//============================================================================//

    if ($do=='add_item' || $do=='edit_item'){

        $GLOBALS['do']  = $do;

        //получим целевую категорию
        $cat_id = $inCore->request('cat_id', 'int', 0);     if (!$cat_id) { return; }
        $cat    = $model->getCategory($cat_id);             if (!$cat) { return; }
        $GLOBALS['cat_id']  = $cat_id;
        $GLOBALS['cat']     = $cat;

        //проверим возможность добавления записей
        $is_cat_access = $model->checkCategoryAccess($cat['id'], $cat['is_public'], $inUser->group_id);
        $is_can_add = $is_cat_access || $inUser->is_admin;
        if (!$is_can_add && $do=='add_item') { cmsCore::error404(); }

        $inPage->addHeadJS('components/maps/js/edit.js');

        if (in_array($cfg['maps_engine'], array('yandex', 'narod', 'custom'))){
            $key = $cfg['yandex_key'];
        } else {
            $key = $cfg[$cfg['maps_engine'].'_key'];
        }

        //готовим карту, понадобится для ручной расстановки маркеров
        $inCore->includeFile('components/maps/systems/'.$cfg['maps_engine'].'/info.php');
        $inPage->addHeadJS('components/maps/systems/'.$cfg['maps_engine'].'/geo.js');
        $api_key = str_replace('#key#', $key, $GLOBALS['MAP_API_URL']);
        $inPage->page_head[] = $api_key;

        if ($do=='edit_item'){

            $item_id    = $inCore->request('item_id', 'int', 0); if (!$item_id) { return; }
            $item       = $model->getItem($item_id);            if (!$item) { return; }

            $is_can_edit = ($item['user_id']==$inUser->id || $inUser->is_admin);

            if (!$is_can_edit) { cmsCore::error404(); }

            //устанавливаем заголовки
            $inPage->setTitle($_LANG['MAPS_EDIT_OBJECT']);
            $inPage->addPathway($_LANG['MAPS_EDIT_OBJECT']);

            $GLOBALS['item_id']  = $item_id;
            $GLOBALS['item']     = $item;

            $GLOBALS['city']    = $item['addr_city'] ? $item['addr_city'] : $cfg['city'];
            $GLOBALS['country'] = $item['addr_country'] ? $item['addr_country'] : $cfg['country'];

        }

        if ($do=='add_item'){

            //устанавливаем заголовки
            $inPage->setTitle($_LANG['MAPS_ADD_OBJECT']);
            $inPage->addPathway($_LANG['MAPS_ADD_OBJECT']);

            $GLOBALS['city']    = $location['city'];
            $GLOBALS['country'] = $cfg['country'];

            if (IS_BILLING){ cmsBilling::checkBalance('maps', 'add_item'); }

        }

        $inPage->addHeadJS('includes/jquery/multifile/jquery.multifile.js');

        //подключаем шаблон
        $inPage->includeTemplateFile('components/com_inmaps_edit.php');

    }

//============================================================================//
//============================================================================//

    if ($do=='submit_item'){

        if (IS_BILLING){ cmsBilling::process('maps', 'add_item'); }

        $inCore->includeGraphics();

        $inCore->loadLib('tags');

        $inUser = cmsUser::getInstance();

        $item                   = array();

        //get variables
        $item['user_id']        = $inUser->id;
        $item['category_id']    = $inCore->request('cat_id', 'int', 0);
        $item['vendor_id']      = 0;
        $item['title']          = $inCore->request('title', 'str');

        $item['addr_id']        = $inCore->request('addr_id', 'array');
        $item['addr_country']   = $inCore->request('addr_country', 'array');
        $item['addr_city']      = $inCore->request('addr_city', 'array');
        $item['addr_prefix']    = $inCore->request('addr_prefix', 'array');
        $item['addr_street']    = $inCore->request('addr_street', 'array');
        $item['addr_house']     = $inCore->request('addr_house', 'array');
        $item['addr_room']      = $inCore->request('addr_room', 'array');
        $item['addr_phone']     = $inCore->request('addr_phone', 'array');
        $item['addr_lat']       = $inCore->request('addr_lat', 'array');
        $item['addr_lng']       = $inCore->request('addr_lng', 'array');

        $item['tpl']            = 'com_inmaps_item.tpl';

        $item['is_public_events'] = $inCore->request('is_public_events', 'int', 0);

        $item['shortdesc']      = $inDB->escape_string($inCore->request('shortdesc', 'html'));
        $item['description']    = $inDB->escape_string($inCore->request('description', 'html'));
        $item['metakeys']       = $inCore->request('tags', 'str');
        $item['metadesc']       = $inCore->request('shortdesc', 'str');

        $item['is_comments']    = 0;
        $item['tags']           = $inCore->request('tags', 'str');

        $item['published']      = ($inUser->is_admin ? 1 : $cfg['published_add']);
        $item['on_moderate']    = ($item['published'] ? 0 : 1);
        $item['pubdate']        = date('Y-m-d H:i');

        $item['is_front']       = 0;

        if ($cfg['can_edit_cats']){
            $item['cats']           = $inCore->request('cats', 'array_int', array());
        } else {
            $item['cats']           = array();
        }

        $item['chars']          = $inCore->request('chars', 'array');

        $item['auto_thumb']     = $inCore->request('auto_thumb', 'int', 0);

        $item['contacts']       = $inCore->request('contacts', 'array');

        $item['id']             = $model->addItem($item);

        $seolink                = $inDB->get_field('cms_map_items', "id={$item['id']}", 'seolink');

        //сообщение администратору
        if ($inUser->id != 1 && !$item['published'] && !$inUser->is_admin){

            $link = "<a href=\"".HOST."/maps/{$seolink}.html\">{$item['title']}</a>";
            $user = '<a href="'.HOST.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';

            $message = $_LANG['MSG_ITEM_SUBMIT'];
            $message = str_replace('%user%', $user, $message);
            $message = str_replace('%link%', $link, $message);

            if (in_array($cfg['moder_notify'], array('both', 'pm'))){
                cmsUser::sendMessage(USER_UPDATER, 1, $message);
            }

            if (in_array($cfg['moder_notify'], array('both', 'mail'))){
                if ($cfg['moder_mail']){
                    $mails = explode(',', $cfg['moder_mail']);
                    foreach($mails as $addr){
                        $addr = trim($addr);
                        $inCore->mailText($addr, $_LANG['MSG_ITEM_SUBMITTED'].' - '.$inConf->sitename, $message);
                    }
                }
            }

        }

        $seolink = $inDB->get_field('cms_map_items', "id={$item['id']}", 'seolink');
        $category = $inDB->get_fields('cms_map_cats', "id={$item['category_id']}", 'title, seolink');

        cmsActions::log('add_maps_obj', array(
            'object' => $item['title'],
            'object_url' =>  "/maps/{$seolink}.html",
            'object_id' =>  $item['id'],
            'target' => $category['title'],
            'target_url' => "/maps/{$category['seolink']}",
            'target_id' =>  $item['category_id'],
            'description' => ''
        ));

        $inCore->redirect('/maps/'.$seolink.'.html');

    }

//============================================================================//
//============================================================================//

    if ($do=='update_item'){

        if($inCore->inRequest('item_id')) {

			$id = $inCore->request('item_id', 'int');

            $inCore->includeGraphics();

            $inCore->loadLib('tags');

            $old                    = $model->getItem($id);

            $item                   = array();

            //get variables
            $item['category_id']    = $inCore->request('cat_id', 'int', 0);
            $item['vendor_id']      = 0;
            $item['title']          = $inCore->request('title', 'str');

            $item['addr_id']        = $inCore->request('addr_id', 'array');
            $item['addr_country']   = $inCore->request('addr_country', 'array');
            $item['addr_city']      = $inCore->request('addr_city', 'array');
            $item['addr_prefix']    = $inCore->request('addr_prefix', 'array');
            $item['addr_street']    = $inCore->request('addr_street', 'array');
            $item['addr_house']     = $inCore->request('addr_house', 'array');
            $item['addr_room']      = $inCore->request('addr_room', 'array');
            $item['addr_phone']     = $inCore->request('addr_phone', 'array');
            $item['addr_lat']       = $inCore->request('addr_lat', 'array');
            $item['addr_lng']       = $inCore->request('addr_lng', 'array');
            $item['addr_del']       = $inCore->request('addr_del', 'array');

            $item['tpl']            = 'com_inmaps_item.tpl';

            $item['is_public_events'] = $inCore->request('is_public_events', 'int', 0);

            $item['shortdesc']      = $inDB->escape_string($inCore->request('shortdesc', 'html'));
            $item['description']    = $inDB->escape_string($inCore->request('description', 'html'));
            $item['metakeys']       = $inCore->request('tags', 'str');
            $item['metadesc']       = $inCore->request('shortdesc', 'str');

            $item['is_comments']    = 0;
            $item['tags']           = $inCore->request('tags', 'str');

            $item['published']      = ($inUser->is_admin ? 1 : $cfg['published_edit']);
            $item['pubdate']        = $old['pubdate'];

            $item['on_moderate']    = ($item['published'] ? 0 : 1);

            if ($inUser->is_admin){
                $item['is_front'] = $old['is_front'];
            } else {
                $item['is_front'] = $cfg['unfront_edit'] ? 0 : $old['is_front'];
            }

            if ($cfg['can_edit_cats']){
                $item['cats']           = $inCore->request('cats', 'array_int', array());
            } else {
                $item['cats']           = $old['cats'];
            }

            $item['chars']          = $inCore->request('chars', 'array');

            $item['auto_thumb']     = $inCore->request('auto_thumb', 'int', 0);

            $item['img_delete']     = $inCore->request('img_delete', 'array');

            $item['contacts']       = $inCore->request('contacts', 'array');

			$model->updateItem($id, $item);

            $seolink                = $inDB->get_field('cms_map_items', "id={$id}", 'seolink');

            //сообщение администратору
            if ($inUser->id != 1 && !$item['published_edit'] && !$inUser->is_admin && !$old['on_moderate']){

                $link = "<a href=\"".HOST."/maps/{$seolink}.html\">{$item['title']}</a>";
                $user = '<a href="'.HOST.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';

                $message = $_LANG['MSG_ITEM_EDITED'];
                $message = str_replace('%user%', $user, $message);
                $message = str_replace('%link%', $link, $message);

                if (in_array($cfg['moder_notify'], array('both', 'pm'))){
                    cmsUser::sendMessage(USER_UPDATER, 1, $message);
                }

                if (in_array($cfg['moder_notify'], array('both', 'mail'))){
                    if ($cfg['moder_mail']){
                        $mails = explode(',', $cfg['moder_mail']);
                        foreach($mails as $addr){
                            $addr = trim($addr);
                            $inCore->mailText($addr, $_LANG['MSG_ITEM_CHANGED'].' - '.$inConf->sitename, $message);
                        }
                    }
                }

            }

            $inCore->redirect('/maps/'.$seolink.'.html');

		}

    }

//============================================================================//
//============================================================================//

    if ($do == 'accept_item'){

        $item_id = $inCore->request('item_id', 'int');

        if (!$item_id || !$inUser->is_admin){ $inCore->halt(); }

        $inDB->query("UPDATE cms_map_items SET published=1, on_moderate=0 WHERE id={$item_id}");
        $inDB->query("UPDATE cms_map_markers SET published=1 WHERE item_id={$item_id}");

        $item = $model->getItem($item_id);

        $item_link = "<a href=\"/maps/{$item['seolink']}.html\">{$item['title']}</a>";

        $message = str_replace('%link%', $item_link, $_LANG['MSG_ITEM_ACCEPTED']);

        cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $message);

        $category = $inDB->get_fields('cms_map_cats', "id={$item['category_id']}", 'title, seolink');

        cmsActions::log('add_maps_obj', array(
            'object' => $item['title'],
            'object_url' =>  "/maps/{$item['seolink']}.html",
            'object_id' =>  $item['id'],
            'target' => $category['title'],
            'target_url' => "/maps/{$category['seolink']}",
            'target_id' =>  $item['category_id'],
            'description' => '',
            'user_id' => $item['user_id']
        ));

        $inCore->redirectBack();

    }

//============================================================================//
//============================================================================//

    if ($do == 'delete_item'){

        $item_id = $inCore->request('item_id', 'int');

        if (!$item_id){ $inCore->halt(); }

        $item = $model->getItem($item_id);

        if (!($item['user_id']==$inUser->id || $inUser->is_admin)){ $inCore->halt(); }

        $model->deleteItem($item_id);

        $message = str_replace('%item%', $item['title'], $_LANG['MSG_ITEM_REJECTED']);
        cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $message);

        $inCore->redirect('/maps/'.$item['category']['seolink']);

    }

//============================================================================//
//============================================================================//

    if ($do == 'rate_item'){

        $item_id    = $inCore->request('item_id', 'int');
        $points     = $inCore->request('rate', 'int');

        $user_id    = $inUser->id;

        $model->rateItem($item_id, $user_id, $points);

        $inCore->redirectBack();

    }

//============================================================================//
//============================================================================//

    if ($do=='add_news' || $do=='edit_news'){

        $limit_used  = $model->getNewsCountForUser($inUser->id, $cfg['news_period']);
        $limit_reach = ($do=='add_news' && ($limit_used >= $cfg['news_limit']) && !$inUser->is_admin);

        if ($do=='edit_news'){

            $id     = $inCore->request('id', 'int', 0); if (!$id) { return; }
            $news   = $model->getNewsItem($id); if (!$news) { return; }

            $is_can_edit = ($news['user_id']==$inUser->id || $inUser->is_admin);

            if (!$is_can_edit) { cmsCore::error404(); }

            $item_id = $news['item_id'];

            //устанавливаем заголовки
            $inPage->setTitle($_LANG['MAPS_EDIT_NEWS']);
            $inPage->addPathway($_LANG['MAPS_EDIT_NEWS']);

        }

        if ($do=='add_news'){

            $id     = 0;
            $news   = array();

            $item_id = $inCore->request('item_id', 'int', 0); if (!$item_id) { cmsCore::error404(); }

            if (IS_BILLING){ cmsBilling::checkBalance('maps', 'add_news'); }

            //устанавливаем заголовки
            $inPage->setTitle($_LANG['MAPS_ADD_NEWS']);
            $inPage->addPathway($_LANG['MAPS_ADD_NEWS']);

        }

        $item = $model->getItem($item_id);

        $_LANG['MAPS_PERIOD']           = $_LANG['MAPS_PERIOD_IN_'.mb_strtoupper($cfg['news_period'])];
        $_LANG['MAPS_NEWS_LIMIT']       = sprintf($_LANG['MAPS_NEWS_LIMIT'], $cfg['news_limit'], $_LANG['MAPS_PERIOD']);
        $_LANG['MAPS_NEWS_LIMIT_USED']  = sprintf($_LANG['MAPS_NEWS_LIMIT_USED'], $limit_used, $cfg['news_limit']);

		$smarty = $inCore->initSmarty('components', 'com_inmaps_edit_news.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('do', $do);
		$smarty->assign('id', $id);
		$smarty->assign('news', $news);
		$smarty->assign('item', $item);
		$smarty->assign('item_id', $item_id);
        $smarty->assign('limit_used', $limit_used);
        $smarty->assign('limit_reach', $limit_reach);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->display('com_inmaps_edit_news.tpl');

    }

//============================================================================//
//============================================================================//

    if ($do=='delete_news'){

        $id     = $inCore->request('id', 'int', 0); if (!$id) { cmsCore::error404(); }
        $news   = $model->getNewsItem($id); if (!$news) { cmsCore::error404(); }

        $is_can_edit = ($news['user_id']==$inUser->id || $inUser->is_admin);

        if (!$is_can_edit) { cmsCore::error404(); }

        $item = $model->getItem($news['item_id']);

        $model->deleteNews($id);

        $inCore->redirect('/maps/'.$item['seolink'].'.html#tab_news');

    }

//============================================================================//
//============================================================================//

    if ($do == 'submit_news'){

        $news['item_id'] = $inCore->request('item_id', 'int', 0);

        //проверка лимита
        $limit_used = $model->getNewsCountForUser($inUser->id, $cfg['news_period']);
        if ($limit_user >= $cfg['news_limit'] && !$inUser->is_admin){ cmsCore::error404(); }

        //проверка хозяина объекта
        $item = $model->getItem($news['item_id']); if (!$item) { cmsCore::error404(); }
        if ($item['user_id'] != $inUser->id && !$inUser->is_admin) { cmsCore::error404(); }

        //проверка типа действия
        $action = $inCore->request('action', 'str', 'add_news');
        if ($action != 'add_news' && $action != 'edit_news') { cmsCore::error404(); }

        //получаем поля новости
        $news['title']      = $inCore->request('title', 'str', '');
        $news['content']    = $inDB->escape_string($inCore->request('content', 'html', ''));

        if (!$cfg['news_html']){ $news['content'] = strip_tags($news['content']); }

        //добавить новость
        if ($action == 'add_news'){
            $news['id'] = $model->addNews($news);
            if (IS_BILLING){ cmsBilling::process('maps', 'add_news'); }

            cmsActions::log('add_maps_news', array(
                'object' => $news['title'],
                'object_url' =>  "/maps/news/{$news['id']}.html",
                'object_id' =>  $news['id'],
                'target' => $item['title'],
                'target_url' => "/maps/{$item['seolink']}.html",
                'target_id' =>  $item['id'],
                'description' => ''
            ));
        }

        //редактировать новость
        if ($action == 'edit_news'){
            $news['id'] = $inCore->request('id', 'int', 0);
            $model->updateNews($news['id'], $news);
        }

        $inCore->redirect('/maps/news/'.$news['id'].'.html');

    }

//============================================================================//
//============================================================================//

    if ($do == 'news_read'){

        $id     = $inCore->request('id', 'int', 0); if (!$id) { cmsCore::error404(); }
        $news   = $model->getNewsItem($id); if (!$news) { cmsCore::error404(); }

        $is_can_edit = ($news['user_id']==$inUser->id || $inUser->is_admin);

        $item = $model->getItem($news['item_id']);

        if (!$cfg['news_html']){
            $news['content'] = nl2br($news['content']);
        }

        //устанавливаем заголовки
        $inPage->setTitle($news['title']);
        $inPage->addPathway($item['title'], '/maps/'.$item['seolink'].'.html#tab_news');
        $inPage->addPathway($_LANG['MAPS_NEWS'], '/maps/news-by/'.$item['id']);
        $inPage->addPathway($news['title']);

        //передаем все в шаблон
		$smarty = $inCore->initSmarty('components', 'com_inmaps_news_read.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('id', $id);
		$smarty->assign('news', $news);
		$smarty->assign('item', $item);
		$smarty->assign('is_can_edit', $is_can_edit);
        $smarty->display('com_inmaps_news_read.tpl');

        //подключаем комментарии
        if($inCore->isComponentInstalled('comments') && $cfg['news_cm']){
            $inCore->includeComments();
            comments('mapnews', $id);
        }

        return;

    }

//============================================================================//
//============================================================================//

    if ($do == 'news'){

        $cat_id     = $inCore->request('cat_id', 'str', false);
        $item_id    = $inCore->request('item_id', 'int', false);

        $item = false;
        $cat = false;

        if ($cat_id){

            if ($cat_id == 'all'){
                $inPage->setTitle($_LANG['MAPS_NEWS_ALL']);
                $inPage->addPathway($_LANG['MAPS_NEWS_ALL']);
            }

            if ($cat_id != 'all') {

                $cat_id = intval($cat_id);
                $cat = $model->getCategory($cat_id);
                if (!$cat){ cmsCore::error404(); }
                $model->whereNewsCatIs($cat_id);

                $inPage->setTitle($cat['title'].': '.$_LANG['MAPS_NEWS']);
                $inPage->addPathway($cat['title'], '/maps/'.$cat['seolink']);
                $inPage->addPathway($_LANG['MAPS_NEWS']);

            }

            $pages_url  = '/maps/news/'.$cat_id.'/%page%';

        }

        if ($item_id){

            $item = $model->getItem($item_id);

            $inPage->setTitle($item['title'].': '.$_LANG['MAPS_NEWS']);
            $inPage->addPathway($item['title'], '/maps/'.$item['seolink'].'.html');
            $inPage->addPathway($_LANG['MAPS_NEWS']);

            $model->whereNewsObjectIs($item_id);

            $pages_url  = '/maps/news-by/'.$item_id.'/%page%';

        }

        $model->groupBy('i.id');

        $total      = $model->getNewsCount();
        $pagebar    = cmsPage::getPagebar($total, $page, $perpage, $pages_url);

        $model->orderBy('i.pubdate', 'DESC');
        $model->limitPage($page, $perpage);

        $news = $model->getNewsAll();

        $cats = $model->getCategories();

		$smarty = $inCore->initSmarty('components', 'com_inmaps_news.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('total', $total);
		$smarty->assign('items', $news);
		$smarty->assign('pagebar', $pagebar);
		$smarty->assign('cats', $cats);
		$smarty->assign('cat_id', $cat_id);
		$smarty->assign('cat', $cat);
		$smarty->assign('item_id', $item_id);
		$smarty->assign('item', $item);
        $smarty->display('com_inmaps_news.tpl');

    }

//============================================================================//
//============================================================================//
//============================================================================//
//============================================================================//

    if ($do=='add_event' || $do=='edit_event'){

        $limit_used  = $model->getNewsCountForUser($inUser->id, $cfg['news_period']);
        $limit_reach = ($do=='add_event' && ($limit_used >= $cfg['events_limit']) && !$inUser->is_admin);

        if ($do=='edit_event'){

            $id     = $inCore->request('id', 'int', 0); if (!$id) { return; }
            $event  = $model->getEvent($id); if (!$event) { return; }

            $is_can_edit = ($event['user_id']==$inUser->id || $inUser->is_admin);

            if (!$is_can_edit) { cmsCore::error404(); }

            $item_id = $event['item_id'];

            //устанавливаем заголовки
            $inPage->setTitle($_LANG['MAPS_EDIT_EVENT']);
            $inPage->addPathway($_LANG['MAPS_EDIT_EVENT']);

        }

        if ($do=='add_event'){

            $id     = 0;
            $event   = array();

            $item_id = $inCore->request('item_id', 'int', 0); if (!$item_id) { cmsCore::error404(); }

            if (IS_BILLING){ cmsBilling::checkBalance('maps', 'add_event'); }

            //устанавливаем заголовки
            $inPage->setTitle($_LANG['MAPS_ADD_EVENT']);
            $inPage->addPathway($_LANG['MAPS_ADD_EVENT']);

        }

        $item = $model->getItem($item_id);

        $_LANG['MAPS_PERIOD']             = $_LANG['MAPS_PERIOD_IN_'.mb_strtoupper($cfg['events_period'])];
        $_LANG['MAPS_EVENTS_LIMIT']       = sprintf($_LANG['MAPS_EVENTS_LIMIT'], $cfg['events_limit'], $_LANG['MAPS_PERIOD']);
        $_LANG['MAPS_EVENTS_LIMIT_USED']  = sprintf($_LANG['MAPS_EVENTS_LIMIT_USED'], $limit_used, $cfg['events_limit']);

		$smarty = $inCore->initSmarty('components', 'com_inmaps_edit_event.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('do', $do);
		$smarty->assign('id', $id);
		$smarty->assign('event', $event);
		$smarty->assign('item', $item);
		$smarty->assign('item_id', $item_id);
        $smarty->assign('limit_used', $limit_used);
        $smarty->assign('limit_reach', $limit_reach);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->display('com_inmaps_edit_event.tpl');

    }

//============================================================================//
//============================================================================//

    if ($do=='delete_event'){

        $id      = $inCore->request('id', 'int', 0); if (!$id) { cmsCore::error404(); }
        $event   = $model->getEvent($id); if (!$event) { cmsCore::error404(); }

        $item = $model->getItem($event['item_id']);

        $is_can_edit = ($event['user_id']==$inUser->id || $inUser->is_admin || $item['user_id'] ==$inUser->id);
        if (!$is_can_edit) { cmsCore::error404(); }

        $model->deleteEvent($id);

        $inCore->redirect('/maps/'.$item['seolink'].'.html#tab_events');

    }

//============================================================================//
//============================================================================//

    if ($do == 'submit_event'){

        $event['item_id'] = $inCore->request('item_id', 'int', 0);

        //проверка лимита
        $limit_used = $model->getNewsCountForUser($inUser->id, $cfg['events_period']);
        if ($limit_user >= $cfg['events_limit'] && !$inUser->is_admin){ cmsCore::error404(); }

        //проверка хозяина объекта
        $item = $model->getItem($event['item_id']); if (!$item) { cmsCore::error404(); }
        $is_can_add = ($inUser->id == $item['user_id']) || ($cfg['events_add_any']==1) || ($cfg['events_add_any']==2 && $item['is_public_events']) || $inUser->is_admin;
        if (!$is_can_add) { cmsCore::error404(); }

        //проверка типа действия
        $action = $inCore->request('action', 'str', 'add_event');
        if ($action != 'add_event' && $action != 'edit_event') { cmsCore::error404(); }

        //получаем поля
        $event['user_id']    = $inUser->id;
        $event['date_start'] = $inCore->request('date_start', 'str', '');
        $event['date_end']   = $inCore->request('date_end', 'str', $event['date_start']);
        $event['title']      = $inCore->request('title', 'str', '');
        $event['content']    = $inDB->escape_string($inCore->request('content', 'html', ''));

        if (!$cfg['events_html']){ $event['content'] = strip_tags($event['content']); }

        //добавить событие
        if ($action == 'add_event'){
            $event['id'] = $model->addEvent($event);
            if (IS_BILLING){ cmsBilling::process('maps', 'add_event'); }
            cmsActions::log('add_maps_event', array(
                'object' => $event['title'],
                'object_url' =>  "/maps/events/{$event['id']}.html",
                'object_id' =>  $event['id'],
                'target' => $item['title'],
                'target_url' => "/maps/{$item['seolink']}.html",
                'target_id' =>  $event['id'],
                'description' => ''
            ));
        }

        //редактировать событие
        if ($action == 'edit_event'){
            $event['id'] = $inCore->request('id', 'int', 0);
            $model->updateEvent($event['id'], $event);
        }

        $inCore->redirect('/maps/events/'.$event['id'].'.html');

    }

//============================================================================//
//============================================================================//

    if ($do == 'event_read'){

        $id         = $inCore->request('id', 'int', 0); if (!$id) { cmsCore::error404(); }
        $event      = $model->getEvent($id); if (!$event) { cmsCore::error404(); }

        $item = $model->getItem($event['item_id']);

        $is_can_edit = ($event['user_id']==$inUser->id || $inUser->is_admin || $item['user_id'] ==$inUser->id);

        if (!$cfg['events_html']){
            $event['content'] = nl2br($event['content']);
        }

        //устанавливаем заголовки
        $inPage->setTitle($event['title']);
        $inPage->addPathway($item['title'], '/maps/'.$item['seolink'].'.html#tab_events');
        $inPage->addPathway($_LANG['MAPS_EVENTS'], '/maps/events-by/'.$item['id']);
        $inPage->addPathway($event['title']);

		$smarty = $inCore->initSmarty('components', 'com_inmaps_event_read.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('id', $id);
		$smarty->assign('event', $event);
		$smarty->assign('item', $item);
		$smarty->assign('is_can_edit', $is_can_edit);
		$smarty->assign('is_user', $inUser->id);
        $smarty->display('com_inmaps_event_read.tpl');

        if($inCore->isComponentInstalled('comments') && $cfg['events_cm']){
            $inCore->includeComments();
            comments('mapevent', $id);
        }

        return;

    }

//============================================================================//
//============================================================================//

    if ($do == 'events'){

        $cat_id     = $inCore->request('cat_id', 'str', false);
        $item_id    = $inCore->request('item_id', 'int', false);

        $item = false;
        $cat = false;

        $date_start = $inCore->request('date_start', 'str', date('d.m.Y'));
        $date_end   = $inCore->request('date_end', 'str', date('d.m.Y', time() + 3600*24*31));

        if (!preg_match('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/i', $date_start)) { $date_start = date('d.m.Y'); }
        if (!preg_match('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/i', $date_end)) { $date_end = date('d.m.Y', time() + 3600*24*31); }

        if ($cat_id){

            if ($cat_id == 'all'){
                $inPage->setTitle($_LANG['MAPS_EVENTS_ALL']);
                $inPage->addPathway($_LANG['MAPS_EVENTS_ALL']);
            }

            if ($cat_id != 'all') {

                $cat_id = intval($cat_id);
                $cat = $model->getCategory($cat_id);
                if (!$cat){ cmsCore::error404(); }
                $model->whereEventsCatIs($cat_id);

                $inPage->setTitle($cat['title'].': '.$_LANG['MAPS_EVENTS']);
                $inPage->addPathway($cat['title'], '/maps/'.$cat['seolink']);
                $inPage->addPathway($_LANG['MAPS_EVENTS']);

            }

            $pages_url  = '/maps/events/'.$cat_id.'/%page%?date_start='.$date_start.'&date_end='.$date_end;

        }

        if ($item_id){

            $item = $model->getItem($item_id);

            $inPage->setTitle($item['title'].': '.$_LANG['MAPS_EVENTS']);
            $inPage->addPathway($item['title'], '/maps/'.$item['seolink'].'.html');
            $inPage->addPathway($_LANG['MAPS_EVENTS']);

            $model->whereEventsObjectIs($item_id);

            $pages_url  = '/maps/events-by/'.$item_id.'/%page%?date_start='.$date_start.'&date_end='.$date_end;

        }

        $model->groupBy('i.id');

        $total      = $model->getEventsCount($date_start, $date_end);
        $pagebar    = cmsPage::getPagebar($total, $page, $perpage, $pages_url);

        $model->orderBy('i.date_start', 'asc');
        $model->limitPage($page, $perpage);

        $events = $model->getEventsAll($date_start, $date_end);

        $cats = $model->getCategories();

		$smarty = $inCore->initSmarty('components', 'com_inmaps_events.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('total', $total);
		$smarty->assign('items', $events);
		$smarty->assign('pagebar', $pagebar);
		$smarty->assign('cats', $cats);
		$smarty->assign('cat_id', $cat_id);
		$smarty->assign('cat', $cat);
		$smarty->assign('item_id', $item_id);
		$smarty->assign('item', $item);
		$smarty->assign('date_start', $date_start);
		$smarty->assign('date_end', $date_end);
        $smarty->display('com_inmaps_events.tpl');

    }

//============================================================================//
//============================================================================//

    if ($do=='attend' || $do=='unattend'){

        $object_type = $inCore->request('object_type', 'str', 'item');
        $object_id   = $inCore->request('object_id', 'int');

        if (!$inUser->id) { cmsCore::error404(); }
        if (!$object_id) { cmsCore::error404(); }

        if ($do=='attend' && $model->isUserAttend($object_type, $object_id, $inUser->id)){ $inCore->redirectBack(); }

        if ($do=='attend'){

            $model->attendUser($object_type, $object_id, $inUser->id);

            if ($object_type=='event'){
                $event = $inDB->get_fields('cms_map_events', "id={$object_id}", '*');
                cmsActions::log('add_maps_attend', array(
                    'object' => $event['title'],
                    'object_url' =>  "/maps/events/{$event['id']}.html",
                    'object_id' =>  $event['id'],
                    'description' => ''
                ));
            }

        } else {

            $model->unAttendUser($object_type, $object_id, $inUser->id);

            if ($object_type=='event'){
                cmsActions::removeObjectLog('add_maps_attend', $object_id, $inUser->id);
            }

        }

        $inCore->redirectBack();

    }

//============================================================================//
//============================================================================//

    if ($do == 'add_abuse'){

        $item_id     = $inCore->request('item_id', 'int', 0);

        if (!$item_id) { cmsCore::error404(); }
        if ($model->isUserAbused($item_id, $inUser->id)){ cmsCore::error404(); }

        $item = $model->getItem($item_id);

        if (!$item) { cmsCore::error404(); }

        $message = '';

        if ($inCore->inRequest('submit')){

            $message = $inCore->request('message', 'str', '');

            if ($message){

                $model->addAbuse($item_id, $inUser->id, $message);
                cmsCore::addSessionMessage($_LANG['MAPS_ITEM_ABUSE_SENT']);

                $inCore->redirect('/maps/'.$item['seolink'].'.html');

            }

            cmsCore::addSessionMessage($_LANG['MAPS_ITEM_ABUSE_MSG_REQ'], 'error');

        }

        $inPage->setTitle($_LANG['MAPS_ITEM_ABUSE']);
        $inPage->addPathway($_LANG['MAPS_ITEM_ABUSE']);

		$smarty = $inCore->initSmarty('components', 'com_inmaps_abuse.tpl');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('item', $item);
		$smarty->assign('item_id', $item_id);
        $smarty->display('com_inmaps_abuse.tpl');

    }

//============================================================================//
//============================================================================//

    if ($do == 'close_abuse'){

        if (!$inUser->is_admin){ cmsCore::error404(); }

        $abuse_id = $inCore->request('id', 'int', 0);

        $model->closeAbuse($abuse_id);

        $inCore->redirectBack();

    }

//============================================================================//
//============================================================================//

    if ($do == 'save_city_pos'){

        $id  = $inCore->request('id', 'int', 0);
        $lat = $inCore->request('lat', 'str', 0);
        $lng = $inCore->request('lng', 'str', 0);

        $model->saveCityPosition($id, $lat, $lng);

        $inCore->halt();

    }

//============================================================================//
//============================================================================//

    if ($do == 'embed'){

        $id  = $inCore->request('item_id', 'int', 0);

        $item = $model->getItem($id);

        if (!$item){ $inCore->halt(); }

        if (in_array($cfg['maps_engine'], array('yandex', 'narod', 'custom'))){
            $key = $cfg['yandex_key'];
        } else {
            $key = $cfg[$cfg['maps_engine'].'_key'];
        }

        $inCore->includeFile('components/maps/systems/'.$cfg['maps_engine'].'/info.php');
        $api_key = str_replace('#key#', $key, $GLOBALS['MAP_API_URL']);

        $smarty = $inCore->initSmarty('components', 'com_inmaps_embed.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('api_key', $api_key);
        $smarty->assign('item', $item);
        $smarty->assign('template', $inConf->template);
        $smarty->display('com_inmaps_embed.tpl');

        $inCore->halt();

    }

//============================================================================//
//============================================================================//

    if ($do == 'embed-code'){

        if (!$cfg['items_embed']){ $inCore->error404(); }

        $id  = $inCore->request('item_id', 'int', 0);
        $item = $model->getItem($id);

        if (!$item){ $inCore->error404(); }

        $inPage->setTitle($_LANG['MAPS_ITEM_EMBED']);
        $inPage->addPathway($item['title'], '/maps/'.$item['seolink'].'.html');
        $inPage->addPathway($_LANG['MAPS_ITEM_EMBED']);

        if (in_array($cfg['maps_engine'], array('yandex', 'narod', 'custom'))){
            $key = $cfg['yandex_key'];
        } else {
            $key = $cfg[$cfg['maps_engine'].'_key'];
        }

        $inCore->includeFile('components/maps/systems/'.$cfg['maps_engine'].'/info.php');
        $inPage->addHeadJS('components/maps/systems/'.$cfg['maps_engine'].'/geo.js');
        $inPage->addHeadJS('components/maps/js/map.js');
        $inPage->addHeadJS('includes/jquery/jquery.form.js');
        $api_key = str_replace('#key#', $key, $GLOBALS['MAP_API_URL']);
        $inPage->page_head[] = $api_key;

        $code = '<iframe width="W" height="H" border="0" framespacing="0" frameborder="0" src="'.HOST.'/maps/embed/'.$item['id'].'"></iframe>';

        $smarty = $inCore->initSmarty('components', 'com_inmaps_embed_code.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('item', $item);
        $smarty->assign('code', $code);
        $smarty->display('com_inmaps_embed_code.tpl');

    }

//============================================================================//
//============================================================================//

}
?>
