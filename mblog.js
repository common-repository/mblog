setInterval("mblog_update();",3000);

function mblog_update(){
jQuery(document).ready(function($){
	$.ajax({
		type:"POST",
		url:'wp-content/plugins/mblog/BProc.php',
		data:'m=0',
		dataType: 'json',
		success: function(rsp){
			$("#mblog-window").html('');
			for (var i=0; i < rsp.length; i++){
				$("#mblog-window").append('<div class="mblog-entry"><span class="mblog-avt">'+rsp[i].Avt+'</span>'+'<p><span class="mblog-uname">'+rsp[i].Name+"</span>:"+rsp[i].Msg+'</p></div><div style="clear:both"></div>');
			}
		}
	});
});
}

function processInp(){
jQuery(document).ready(function($){
	var msg = $("#mblog-input").val();
	var uname = Get_Cookie('user');
	var entries = $('.mblog-entry').length;
	$.ajax({
		type:"POST",
		url:'wp-content/plugins/mblog/BProc.php',
		data:'m=1&msg='+msg+'&u='+uname,
		dataType: 'json',
		success: function(rsp){
				if (rsp == null){
					console.log('catch');
				}
				else{
					console.log(rsp);
					if (entries > 5){
						$('.mblog-entry').first().slideUp(800, function(){ $(this).remove();});
					}
					$("#mblog-window").append('<div class="mblog-entry"><span class="mblog-avt">'+rsp.Avt+'</span>'+'<p><span class="mblog-uname">'+rsp.Name+"</span>:"+rsp.Msg+'</p></div><div style="clear:both"></div>');
					$("#mblog-input").val("");
					}
				}
		});
	});
}

//Helper functions

function Get_Cookie( check_name ) {
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; 

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );
		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			// we need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if (!b_cookie_found)
	{
		return null;
	}
}
	