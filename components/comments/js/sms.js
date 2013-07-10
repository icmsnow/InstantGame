function CheckCode(){
  var activcode;
  var phone;

  activcode = document.getElementById('activcode').value;
  phone = document.getElementById('phone').value;

  $.ajax({
   type: "POST",
   url: '/components/comments/ajax/sms.php',
   data: {opt: "activ", phone: phone, activcode: activcode},
   success: function(data){
    // пришёл ответ от проведения платёжки
      if (data=='400') 
     {
         alert('Ошибка!\nВы слишком быстро повторили попытку входа.\nПовторите попытку более, чем через 10 секунд.');
     }else if (data=='403') {
         alert('Ошибка!\nНеверный Код.');
    }else if (data=='200') {
        alert('Успех!\nНомер подтверждён.');
		 vis1();
    }else
        alert(data);
   },
   error: function(event, request, settings,error)
   {
       alert('Сервер не отвечает.\nПовторите попытку.');
   },
   timeout: 4000
  });
 }



function vis() {
	document.getElementById('tdcode').style.display='';
	document.getElementById('tdphone').style.display='none';
    }

function vis1() {
	document.getElementById('tdcode').style.display='none';
    } 
 
 
 
function SendSMS(){
  var phone;
  phone = document.getElementById('phone').value;

  $.ajax({
   type: "POST",
   url: '/components/comments/ajax/sms.php',
   data: {opt: "send", phone: phone},
   success: function(data){
    // пришёл ответ от проведения платёжки
      if (data=='400') 
     {
         alert('Ошибка!\nТолько по Росии');
     }else if (data=='403') {
         alert('Ошибка!\nОбратитесь к администратору сайта.');
    }else if (data=='200') {
		vis();
		alert('Успех!\nСообщение отправленно.');		 
    }else
        alert(data);
   },
   error: function(event, request, settings,error)
   {
       alert('Сервер не отвечает.\nПовторите попытку.');
   },
   timeout: 4000
  });
  }
