<?php

class p_game_stat extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // Информация о плагине

        $this->info['plugin']           = 'p_game_stat';
        $this->info['title']            = 'Персональная статистика по игре';
        $this->info['description']      = 'Добавляет вкладку "Стастика игр" в профили всех пользователей';
        $this->info['author']           = '<a href="http://www.instantcms.ru/users/somebody" target="_blank" >Димитриус</a>';
        $this->info['version']          = '0.1';

        $this->info['tab']              = 'Стастика игр'; //-- Заголовок закладки в профиле

        // События, которые будут отлавливаться плагином

        $this->events[]                 = 'USER_PROFILE';

    }

// ==================================================================== //

    /**
     * Процедура установки плагина
     * @return bool
     */
    public function install(){

        return parent::install();

    }

// ==================================================================== //

    /**
     * Процедура обновления плагина
     * @return bool
     */
    public function upgrade(){

        return parent::upgrade();

    }

// ==================================================================== //

    /**
     * Обработка событий
     * @param string $event
     * @param array $user
     * @return html
     */
    public function execute($event, $user){

        parent::execute();

        $inCore     = cmsCore::getInstance();

        if (!$inCore->isComponentInstalled('intuit')){
            return 'Для работы плагина требуется компонент Игры.';
        }

		

        ob_start();

        $smarty= $this->inCore->initSmarty('plugins', 'p_game_stat');
        $smarty->assign('data', $this->GetStats($user['id']));
        $smarty->display('p_game_stat.tpl');

        $html = ob_get_clean();

        return $html;

    }

// ==================================================================== //
	public function GetStats($user_id){

		$inDB   = cmsDatabase::getInstance();

		//количество игр за всё время
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE ((user_host = '$user_id') OR (user_client='$user_id')) AND winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_all'] = $result['count'];

		//количество побед 
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE 
					date_create >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND 
					((user_host = '$user_id') OR (user_client='$user_id')) AND
					winner = '$user_id'";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_win'] = $result['count'];
		
		$data['efect'] = number_format($data['count_win']/$data['count_all'], 2);
		
		//количество игр за месяц
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE 
					date_create >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND 
					((user_host = '$user_id') OR (user_client='$user_id')) AND
					winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_month'] = $result['count'];

		//количество игр за неделю
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE 
					date_create >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND
					((user_host = '$user_id') OR (user_client='$user_id')) AND
					winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_week'] = $result['count'];

		//количество игр вчера 
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE 
					year(date_create) = year(current_timestamp - INTERVAL 1 DAY) AND
					month(date_create) = month(current_timestamp - INTERVAL 1 DAY) AND
					day(date_create) = day(current_timestamp - INTERVAL 1 DAY) AND
					((user_host = '$user_id') OR (user_client='$user_id')) AND
					winner IS NOT NULL";
		$result = $inDB->query($sql);
		$result = $inDB->fetch_assoc($result);
		$data['count_yesterday'] = $result['count'];

		//количество игр сегодня
		$sql = "SELECT COUNT(id) as count FROM cms_intuit_games WHERE 
											date_create >= CURDATE() AND
											((user_host = '$user_id') OR (user_client='$user_id')) AND
											winner IS NOT NULL";
		$result = $inDB->query($sql) ;
		$result = $inDB->fetch_assoc($result);
		$data['count_today'] = $result['count'];
		return $data;
	}

}

?>
