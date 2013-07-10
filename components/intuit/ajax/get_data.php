<?php

	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	include(PATH.'/core/ajax/ajax_core.php');

    cmsCore::loadModel('intuit');
    $model = new cms_model_intuit();
	
	$User_id = $inUser->id;
	
	$x = $inCore->request('x', 'int');
	$y  = $inCore->request('y', 'int');
	$opt  = $inCore->request('opt', 'str');
	$game_id  = $inCore->request('game_id', 'int');
	if ($game_id) {$Gameinfo = $model->LoadGame($game_id);}
	$size = $model->Size_tabl;
	
//  if (!$inUser->id) { cmsCore::halt(); }
	
	if ($opt=='ingame' and $Gameinfo){
		
		if (($Gameinfo['you_role'] == 'none') and ($Gameinfo['status'] == 1)){
			if ($model->CheckBalance($Gameinfo['type_rate'],$Gameinfo['rate'])){
				if ($model->PayProcess($User_id,$Gameinfo['type_rate'],$Gameinfo['rate'],false)){
						$data['error'] = "1";
						$data['msg'] = "Платёж не прошел";	
						}else{
							$model->EnterInGame($User_id,$Gameinfo['id']);
							$model->AddUserOnline($Gameinfo['id']);
							$data['status'] = "ok";
							}	
					}else{
					$data['error'] = "1";
					$data['msg'] = "Ваш баланс не позволяет вам войти в игру";	
				}
		}else{
			$data['error'] = "1";
			$data['msg'] = "Вы не можете подключится к этой игре, выберите другую";
			}
	}
		
	if ($opt=='turn'){
		if ((($x >= 0) and ($x <= $size)) and ((($y>=0) and ($x <=$size)))){
			if ($model->CheckTurn()){
				$model->GameTurn($x,$y);
				$data['status'] = 'turn_ok';
				$data['x'] = $x;
				$data['y'] = $y;
				$time = date('G:i:s');
				$raschet = $model->RaschetDoCeli($x,$y);
				$data['raschet'] = $raschet; 
				$prefix = $model->format_by_count($raschet, 'шаге', 'шагах', 'шагах');
				$data['status_msg'] = $time.' Я нахожусь в '.$raschet.' '.$prefix.'<br>';
				$data['msg'] = 'Ожидание хода другого игрока';
				$data['block'] = "on";
			}else{
				$data['error'] = "1";
				$data['msg'] = "Сейчас не ваш ход";
				$data['block'] = "on";
			}
		}
	}
	
	if ($opt=='data'){
		if ($model->GameInfo['id']){
			if ($model->GameOpen){
					if ($model->GameInfo['status'] == 2){				
						If ($model->CheckTurn()){
							$data = $model->GetTurnXY();
							$data['error'] = "1";
							$data['msg'] = "Ваш ход";
							$data['block'] = "off";						
						} else{
								$data['error'] = "1";
								$data['msg'] = "Ожидание хода другого игрока";
								$data['block'] = "on";								
								}
					}else{
						$data['error'] = "1";
						$data['msg'] = "Ожидаем подключения другово игрока";
						$data['block'] = "on";}
			} else{
				$data = $model->GetTurnXY();
				$data['game_close'] = "2";
				$model->OnlineOut();
				//cmsCore::addSessionMessage($model->GameMsg);
				$data['msg'] = $model->GameMsg;
				//закрываем игру прячем основной див
				//выводим сообщение
			}
		}else{
			$data['error'] = "1";
			$data['msg'] = "Вы вне игре";
			$data['block'] = "on";}

	}
	echo(json_encode($data));

?>