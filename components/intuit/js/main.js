function GoPlay(game_id){
        $.getJSON('/components/intuit/ajax/get_data.php?opt=ingame&game_id='+ game_id, function(data){
	            if(data.error >= 0) {
                $('#messedge').html(data.msg);
				}
	            if(data.status == 'ok') {
                 window.location.href = '/intuit/';
				}
        });
}

function CreateGame(){
	core.message('Создать новую игру');
	$.post('/components/intuit/addform.php', {do: 'add'}, function(data) {
		if(data){
		  $('#popup_message').html(data);
		  is_form = $('#msgform').html();
		  if(is_form != null){
			 $('#popup_ok').val(core.send).show();
		  }
		  $('#popup_progress').hide();
		}
	});
	$('#popup_ok').click(function() {
		$('#popup_ok').attr('disabled', 'disabled');
		$('#popup_progress').show();
		var options = {
			success: showResponseAdd,
			dataType: 'json'
		};
		$('#msgform').ajaxSubmit(options);
	});
}

function showResponseAdd(result, statusText, xhr, $form){

	$('#popup_progress').hide();
	$('.sess_messages').fadeOut();

	if(statusText == 'success'){
		if(result.error == true){
			$('#error_mess').html(result.text);
			$('.sess_messages').fadeIn();
			$('#popup_ok').attr('disabled', '');
		} else {
			window.location.href = "/intuit/";
			//	core.box_close();
		}
	} else {
		core.alert(statusText, 'Ошибка');
	}

}