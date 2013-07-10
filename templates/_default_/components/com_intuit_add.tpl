<div class="cm_addentry">
    {if $is_user}
		<form action="/intuit/add" id="msgform" method="POST">
        <input type="hidden" name="csrf_token" value="{csrf_token}" />
 	
	<div style="margin-top:6px; display:block">
		<strong>Тип ставки: </strong>
		<select id="type_rate" style="width: 160px;" name="type_rate">
			{$type_rate}
		</select>
	</div>
	<div style="margin-top:6px; display:block">
		<strong>Сумма: </strong>
		<input type="text" size="5" name="rate" value="1"/> 
	</div>
	</form>
    <div class="sess_messages" {if !$notice}style="display:none"{/if}>
      <div class="message_info" id="error_mess">{$notice}</div>
    </div>	
 
	{else}
	          <p>Создавать игры могут только зарегистрированные пользователи <a href="/registration">зарегистрироваться</a></p>
		   <p>Если вы наш пользователь, то просто пройдите авторизацию <a href="/login">войти</a></p>

    {/if}
</div>