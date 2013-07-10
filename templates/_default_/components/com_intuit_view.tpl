{add_css file='components/intuit/css/style.css'}
{add_js file='components/intuit/js/main.js'}
{add_js file='includes/jquery/tabs/jquery.ui.min.js'}
{add_css file='includes/jquery/tabs/tabs.css'}

{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			$("#gamestabs > ul#tabs").tabs();
		});
	</script>
{/literal}

	<div id="gamestabs">
				<ul id="tabs">
					<li><a href="#upr_games"><span>Игра</span></a></li>
                    <li><a href="#upr_help"><span>Как играть?</span></a></li>
				</ul>
				<div id="upr_games">
					<div id="messedge"></div>
						{if $games}
							<a href="javascript:void(0);" onclick="CreateGame();" title="">создать игру</a>
							<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td >Номер игры</td>
											<td >Дата создания</td>
											<td >Ставка</td>
											<td >Вид ставки</td>
											<td >Играть</td>
										</tr>	
							{foreach key=id item=game from=$games}
										<tr>
											<td >{$game.id}</td>
											<td >{$game.date_create}</td>
											<td >{$game.rate}</td>
											{if $game.type_rate == "rat"}
											<td >рейтинг</td>
											{/if}
											{if $game.type_rate == "kar"}
											<td >карма</td>
											{/if}
											{if $game.type_rate == "bil"}
											<td >баллы</td>
											{/if}					
											<td ><a href="javascript:void(0);" onclick="GoPlay({$game.id});" title="">играть</a></td>
										</tr>			
							{/foreach}
							</table>
						{else}
						К сожалению нет ни одной игры для отображения, вы можете стать первым. <a href="javascript:void(0);" onclick="CreateGame();" title="">создать игру</a>
						{/if}
				</div>
				<div id="upr_help">Описание игры</div>
	<div>



