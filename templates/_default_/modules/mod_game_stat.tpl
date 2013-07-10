<div class="mod_game_stat">
	{if $cfg.count_online}
		<div class="game_stat_title">Количество игроков:</div>
		<div style="font-size: 24px; font-weight:bold;">{$data.count_online}</div>
	{/if}
	{if $cfg.count_all}
		<div class="game_stat_title">Игр за всё время:</div>
		<div style="font-size: 24px; font-weight:bold;">{$data.count_all}</div>
	{/if}
	{if $cfg.count_month}
		<div class="game_online_title">Количество игр за месяц:</div>
		<div style="font-size: 24px; font-weight:bold;">{$data.count_month}</div>
	{/if}
	{if $cfg.count_week}
		<div class="game_online_title">Количество игр за неделю:</div>
		<div style="font-size: 24px; font-weight:bold;">{$data.count_week}</div>
	{/if}
	{if $cfg.count_yesterday}
		<div class="game_online_title">Количество игр вчера:</div>
		<div style="font-size: 24px; font-weight:bold;">{$data.count_yesterday}</div>
	{/if}
	{if $cfg.count_today}
		<div class="game_online_title">Количество игр сегодня:</div>
		<div style="font-size: 24px; font-weight:bold;">{$data.count_today}</div>
	{/if}
</div>
