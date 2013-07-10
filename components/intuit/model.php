<?php


if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_intuit{

	public $GameOpen = true;
	public $GameMsg;
	public $GameInfo = array();
	public $User_id;
	public $Size_tabl = 15;
	public $TimeOutWaiting;
	public $TimeOutTurn;
	public $TimeOut;
	//public $is_billing;
	
	public function __construct(){
		
        $this->config = cmsCore::getInstance()->loadComponentConfig('intuit');
		$this->TimeOutWaiting = $this->config['TimeOutWaiting'];
		$this->TimeOutTurn = $this->config['TimeOutWaiting'];
		$this->Size_tabl = $this->config['Size_tabl'];

        $this->inDB = cmsDatabase::getInstance();
		$this->inUser = cmsUser::getInstance();
		

		// Получаем ид пользователя
		$this->User_id = $this->inUser->id;
		// Проверяем пользователь в онлайне и загружаем данные по игре в которой он в онлайне
		$this->GetUserInOnline($this->User_id);
		//Проверяем тайм ауты
		$this->CheckTimeOut();
    }
	
	
/* ==================================================================================================== */
/* ==================================================================================================== */
	public function UpdateOnline() {
		$this->inDB->query("UPDATE cms_intuit_online SET date_motion = NOW() WHERE user_id = '$this->User_id'");
	}

/* ==================================================================================================== */
/* ==================================================================================================== */
    public function LoadGame($game_id) {
		if (!$game_id) {return false;} 
		$data = array();
		$sql = "SELECT * FROM cms_intuit_games WHERE id = '$game_id' LIMIT 1";
		$result = $this->inDB->query($sql);
		if ($this->inDB->num_rows($result)){
			$result = $this->inDB->fetch_assoc($result);
			$data  = $result;
			if (($result['user_host'] == $this->User_id) or ($result['user_client'] == $this->User_id)){			
				if ($result['user_host'] == $this->User_id){ $data['you_role'] = 'host';} else {$data['you_role'] = 'client';}	
			} else $data['you_role'] = 'none';
 			return $data;
		}else{
			$data = 'game_not_found';
			return $data;
			}			
    }
/* ==================================================================================================== */
/* ==================================================================================================== */
	public function GetUserInOnline($user_id){
		$sql = "SELECT * FROM cms_intuit_online WHERE user_id = '$user_id' LIMIT 1";
		$result = $this->inDB->query($sql);
		if ($this->inDB->num_rows($result)){
			$result = $this->inDB->fetch_assoc($result);
			// ставим флаг пользователя онлайна
			$this->User_is_online = true;
			//обновляем время пользовтеля находящегося в онлайне
			$this->UpdateOnline();
			//загружаем информацию об игре в каторой сейчас пользователь
			$this->GameInfo = $this->LoadGame($result['game_id']);
			//Проверяем стутус игры если игра закрыта проверяем причину и выводим текст причины закрытия игры
			if ($this->GameInfo['status']=='3'){
				$this->GameOpen = false;
				$this->GameMsg = $this->GetMotiveClose();
			}
			return $result;
		} else return false;
		
	}
/* ==================================================================================================== */
/* ==================================================================================================== */

	public function CheckBalance($type_rate,$rate){
	
		$user = $this->inUser->loadUser($this->inUser->id);
		
		if ($type_rate == 'rat'){			
			if ($user['rating'] >= $rate){	
				return true;				
			 }else return false;	
		}
		
		if ($type_rate == 'kar'){
			if ($user['karma'] >= $rate){
				return true;	
			} else return false;
		}
		
		if ($type_rate == 'bil'){
			if ($this->inUser->balance >= $rate){
				return true;} else return false;
		}
	return;}

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function PayProcess($user_id,$type_rate,$rate, $plus = true){
		
		$error = true;
		if ($plus){$znak = '+';} else {$znak = '-';}
		if ($type_rate == 'rat'){
			$this->inDB->query("UPDATE cms_users SET rating = rating $znak '$rate' WHERE id = $user_id");
			$error = false;
		}
		if ($type_rate == 'kar'){
			$this->inDB->query("UPDATE cms_user_profiles SET karma = karma $znak '$rate' WHERE user_id = $user_id");
			$error = false;
		}
		if ($type_rate == 'bil'){
			cmsCore::loadClass('billing'); 
			if ($plus){cmsBilling::income($this->User_id, $rate, 'Выполнение операции по игре',false);} 
				else{cmsBilling::pay($this->User_id, abs($rate), 'Выполнение операции по игре');}
			$error = false;
		}
		
	return $error;}
/* ==================================================================================================== */
/* ==================================================================================================== */
    
	public function AddNewGame($type_rate,$rate){
		$turn = rand(0,1);
		$x = rand(1,$this->Size_tabl);
		$y = rand(1,$this->Size_tabl);
		$sql = "INSERT INTO cms_intuit_games (user_host, user_client, user_turn,x, y, winner, status, rate, type_rate, date_create, date_lastaction)
                    VALUES ('$this->User_id', NULL, '$turn', '$x', '$y', NULL, '1', '$rate', '$type_rate', NOW(), NULL)";			
		$this->inDB->query($sql);
		$game_id = dbLastId('cms_intuit_games');
		return $game_id;
	}
/* ==================================================================================================== */
/* ==================================================================================================== */
	public function AddUserOnline($game_id){
		$sql = "INSERT INTO cms_intuit_online (user_id, game_id, date_motion)
                    VALUES ('$this->User_id', '$game_id',NOW())";			
		$this->inDB->query($sql);
		return;
	}
/* ==================================================================================================== */
/* ==================================================================================================== */	
	public function GetGames(){
		$sql = "SELECT * FROM cms_intuit_games WHERE user_client IS NULL and winner IS NULL and status = 1 and (date_create >=NOW()-300)";
		$result = $this->inDB->query($sql);
		$games = array();
		if ($this->inDB->num_rows($result)){
			while($game = $this->inDB->fetch_assoc($result)){$games[] = $game;}	
		}
		return $games;
	}
/* ==================================================================================================== */
/* ==================================================================================================== */	
	public function GenTabl(){
		$size = $this->Size_tabl;
		$html = '';
		for ($x=1; $x<=$size; $x++){
			$html .= '<tr>';
			for ($y=1; $y<=$size; $y++){
				$html .= '<td id="pol_'.$x.'_'.$y.'" onclick="SendData('.$x.','.$y.')">';
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
	return $html;
	}
/* ==================================================================================================== */
/* ==================================================================================================== */		
	
	public function EnterInGame($user_id,$game_id){
		$sql = "UPDATE cms_intuit_games SET 
								user_client ='$user_id',
								status ='2',
								date_lastaction = NOW()
								WHERE id = '$game_id'";
		$this->inDB->query($sql);
	return;}
	
/* ==================================================================================================== */
/* ==================================================================================================== */		

	
	public function CheckTurn(){
		if ($this->GameInfo['you_role'] == 'host'){
			If ($this->GameInfo['user_turn'] == '1'){
				return true;} else return false;
		} else{
			If ($this->GameInfo['user_turn'] == '1'){
				return false;} else return true;
			}
	}

	public function RaschetDoCeli($x1,$y1){
		$x2 = $this->GameInfo['x'];
		$y2 = $this->GameInfo['y'];
		$res1 = (abs($x1-$x2));
		$res2 = (abs($y1-$y2));
		if ($res1> $res2){
			return $res1;
		} else return $res2;	 
	}
	
	public function GetTurnXY(){
		$data = array();
		$game_id = $this->GameInfo['id'];
		$sql = "SELECT * FROM cms_intuit_games_log WHERE game_id = '$game_id' and user_id <> '$this->User_id' and user_get = 0 LIMIT 1";
		$result = $this->inDB->query($sql);
		if ($this->inDB->num_rows($result)){
			$result = $this->inDB->fetch_assoc($result);
			$data['data'] = '1';
			$data['x'] = $result['x'];
			$data['y'] = $result['y'];
			$time = date('G:i:s');
			$raschet = $this->RaschetDoCeli($data['x'],$data['y']);
			$data['raschet'] = $raschet; 
			$prefix = $this->format_by_count($raschet, 'шаге', 'шагах', 'шагах');
			$data['status_msg'] = $time.' Я нахожусь в '.$raschet.' '.$prefix.'<br>';
			$id = $result['id'];
			$sql = "UPDATE cms_intuit_games_log SET user_get = '1' WHERE id = '$id'";
			$this->inDB->query($sql);
			return $data;
		}
	}
	
/* ==================================================================================================== */
/* ==================================================================================================== */		
	
	
	public function GameTurn($x,$y){
		$game_id = $this->GameInfo['id'];
		$user_id = $this->User_id;
		$x_turn  = $this->GameInfo['x'];
		$y_turn  = $this->GameInfo['y'];

		if (($x == $x_turn) and ($y == $y_turn)){
			$sql = "UPDATE cms_intuit_games SET winner ='$user_id', status = '3' WHERE id = '$game_id'";
			$this->inDB->query($sql);
			}
			$sql = "INSERT INTO cms_intuit_games_log (game_id, user_id, x, y, date_motion, user_get)
                    VALUES ('$game_id', '$user_id', '$x', '$y', NOW(), '0')";			
			$this->inDB->query($sql);
		
			if ($this->GameInfo['user_turn'] == 1) {$next_turn = 0;}else {$next_turn = 1;}	
			$sql = "UPDATE cms_intuit_games SET user_turn ='$next_turn', date_lastaction = NOW() WHERE id = '$game_id'";
			$this->inDB->query($sql);
			
	}
	
	public function CheckTimeOut(){
		//Проверяем игры которые ожидают подключения игрока
		$game_id = $this->GameInfo['id'];
		$sql = "SELECT id FROM cms_intuit_games WHERE status = '1' and ((NOW()-date_create)>'$this->TimeOutWaiting') and id = '$game_id' LIMIT 1 ";
		$result = $this->inDB->query($sql);
		if ($this->inDB->num_rows($result)){
			$this->PayProcess($this->GameInfo['user_host'],$this->GameInfo['type_rate'],$this->GameInfo['rate']);
			$this->CloseGame($game_id);
		}
		
		//проверяем ходы
		if ($this->GameInfo['status']== '2'){
			$sql ="SELECT * FROM cms_intuit_games_log WHERE game_id = '$game_id' ORDER BY game_id ASC LIMIT 1";
			$result = $this->inDB->query($sql);
			// Проверяем есть ли ходы в этой игре
			if ($this->inDB->num_rows($result)){
				$game = $this->inDB->fetch_assoc($result);
				if ((strtotime($game['date_motion']) + $this->TimeOutTurn ) < time()){$this->CloseGame($game_id);}		 
				} else {
					//если нет ходов то сверяем дату начала игры + время ожидания с текущей датой
					if ((strtotime($this->GameInfo['date_lastaction']) + $this->TimeOutTurn ) < time()){$this->CloseGame($game_id);}		 
					}
		 }
		return;
	}
	
	public function CheckTimeOutCrone(){
		//Проверяем игры которые ожидают подключения игрока
		$sql = "SELECT id FROM cms_intuit_games WHERE status = '1' and ((NOW()-date_create)>'$this->TimeOutWaiting')";
		$result = $this->inDB->query($sql);
		$games = array();
		if ($this->inDB->num_rows($result)){
			while($game = $this->inDB->fetch_assoc($result)){
			$this->CloseGame($game['id']);
			}

		}
		
		//проверяем ходы
		$sql ="SELECT * FROM `cms_intuit_games_log` WHERE game_id  ORDER BY game_id ASC LIMIT 2";
        $sql = "SELECT *
                FROM cms_users u
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
                WHERE u.is_locked = 0 AND u.is_deleted = 0 AND DATE_FORMAT(u.logdate, '%Y-%m-%d')='$today'
                ORDER BY u.logdate DESC";
		
		return;
	}
	
	public function CloseGame($game_id){
		$sql = "UPDATE cms_intuit_games SET status ='3' WHERE id = '$game_id'";
		$this->inDB->query($sql);		
	}
	public function GetMotiveClose(){
		if ((is_null($this->GameInfo['winner'])) AND (is_null($this->GameInfo['user_client']))){
			$msg = "Время ожидания подключения другого игрока истекло, ставка возвращена";
			return $msg;
		}
		if ((!is_null($this->GameInfo['winner'])) AND ($this->GameInfo['status'] == '3')){
			if ($this->User_id == $this->GameInfo['winner']){
				$msg = "Вы выиграли ";
				$this->PayProcess($this->User_id,$this->GameInfo['type_rate'],$this->GameInfo['rate']*2);
				} else {$msg = "Вы проиграли ";}
			return $msg;
		}
		if ((is_null($this->GameInfo['winner'])) AND ($this->GameInfo['status'] == '3') AND (!is_null($this->GameInfo['user_client']))){
			if (($this->GameInfo['you_role']== 'host')){
				if  ($this->GameInfo['user_turn'] == '1'){$msg = 'Привышено ожидание вашего хода, вы проиграли';} else {$msg = 'Привышено ожидание хода соперника, вы выиграли';}
			}else {if  ($this->GameInfo['user_turn'] == '0'){$msg = 'Привышено ожидание вашего хода, вы проиграли';}else {$msg = 'Привышено ожидание хода соперника, вы выиграли';}}
			return $msg;
		}
	}
	
	public function OnlineOut(){
		$this->inDB->query("DELETE FROM cms_intuit_online WHERE user_id = '$this->User_id'");
	}

	public function AddTypeRateInForm(){
	$html = '';
	if ($this->config['rate_rat']){$html .= '<option value="rat">Рейтинг</option>';}
	if ($this->config['rate_kar']){$html .= '<option value="kar">Карма</option>';}
	if ($this->config['rate_bil']){$html .= '<option value="bil">Баллы</option>';}
	return $html;}
	
	public function format_by_count($count, $form1, $form2, $form3)
	{
		$count = abs($count) % 100;
		$lcount = $count % 10;
		if ($count >= 11 && $count <= 19) return($form3);
		if ($lcount >= 2 && $lcount <= 4) return($form2);
		if ($lcount == 1) return($form1);
		return $form3;
	}
}