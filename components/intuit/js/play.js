$(document).ready(function(){  
            GetData();  
            setInterval('GetData()',5000);  
});  

function GameOver(msg){
	core.message('Игра окончена');
		if(msg){
		  $('#popup_message').html(msg);
		  $('#popup_cancel').hide();
		  $('#popup_ok').hide();
		  $('#popup_progress').hide();
		  $('#popup_close').show();
		}
	$('#popup_close').click(function() {
		window.location.href = "/intuit";
	});
}

function GetData(){
        $.getJSON('/components/intuit/ajax/get_data.php?opt=data', function(data){
	            if(data.game_close == '1') {
					window.location.href = "/intuit";
				}
	            if(data.game_close == '2') {
					$('#status').hide('slow');
					GameOver(data.msg);
				}
	            if(data.error == '1') {
                $('#status').html(data.msg);
				$('title').text(data.msg);
				}
				
	            if(data.data == '1') {
					if (data.x >='0'){
						$('#pol_'+ data.x +'_'+ data.y).css("background-color","red");
						$('#pol_'+ data.x +'_'+ data.y).html(data.raschet);
						k = $('#messedge').html();
						$('#messedge').html(data.status_msg+k);
					}
					$('#status').html(data.msg);
				}
				if (data.block == 'on'){
					$('#block').val('on');
				}
				if (data.block == 'off'){
					$('#block').val('off');
				}
        });
}


function SendData(x,y){
		if ($('#block').val() == 'off'){
			$.getJSON('/components/intuit/ajax/get_data.php?opt=turn&x='+ x + '&y=' + y, function(data){
					if (data.status =='turn_ok'){
						$('#pol_'+ data.x +'_'+ data.y).css("background-color", "green" );
						$('#pol_'+ data.x +'_'+ data.y).html(data.raschet);
						$('#status').html(data.msg);
						k = $('#messedge').html();
						$('#messedge').html(data.status_msg+k);
						$('#block').val('on');
						$('title').text(data.msg);
					}
					if(data.error == '1') {
					$('#status').html(data.msg);				
				}
			});
		}
}