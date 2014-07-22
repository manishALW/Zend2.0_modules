// JavaScript Document
$(document).ready(function(){
$('#email').focus();

	jQuery('#newsletter_subscription').validate(
	{
		rules:{
			email:{
				required:true,
				email:true
			}
			
		},
		messages:{
			email:{
				required:'Please enter email address.',
				email:'Please enter valid email address.'
			}
		}
	});
	jQuery('#newsletter_subscription').submit(function(){
			var responseTextVar='';
			var email=$('#email').val();
			if (email!='') {
				var parameter='email='+email;
				jQuery.ajax({
					type:'get',
					data: parameter,
					url:baseUrl+'/newsletter/checkemail',
					async: false,
					success:function(responseText)
					{
						responseTextVar=responseText;
					}
				});
			}
			if(responseTextVar==1){
				$('#emailExists').show();
				return false;
			}else{
				$('#emailExists').hide();
				return true;
			}
	});

});

