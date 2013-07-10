{add_css file='components/intuit/css/style.css'}
{add_js file='components/intuit/js/play.js'}
{add_js file='components/intuit/js/jquery.timer.js'}
<div class="footer_bar" id="footer_bar">
	<div class="status" id="status"></div>
	<div class="rival" id="rival"></div>
</div>
<input id="block" name="block" type="hidden" value="off"/>
<input id="initgame" name="initgame" type="hidden" value="0"/>
<input id="TimeOutTurn" name="TimeOutTurn" type="hidden" value="{$TimeOutWaiting}"/>
<input id="status_flag" name="status_flag" type="hidden" value="0"/>
	<div class="status_bar" id="status_bar">
		<div class="title_bar" id="title_bar">Сообщения игры:</div>
		<div style="overflow: auto; height: 135px; top: 5px; text-align: left;" class="messedge" id="messedge"> </div>
	</div>
		<div class="intuit_list">
		<table width="" cellspacing="0" cellpadding="0" class="tb_game">
			{$tabl_body}
		</table>	
  </div>
  

 