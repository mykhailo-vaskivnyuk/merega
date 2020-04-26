<!-- SCRIPTS -->
 <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.js"></script>  
 <script type="text/javascript">
	$(	function(){
			$('#restore').submit(
				function(e){
					$('#restore_email').val($('#authorize_email').val());
				}
			);
			
			$('.contacts_data').click(
				function(e){
					$(this).submit();
				}			
			);

			$('form').submit(
				function(e){
					$('.message_error, .message_success').css('display', 'none');
				}
			);
			
			$('#sub_menu').click(
				function(e){
					var menu = $('#sub_menu > a');
					var sub_menu = $('#sub_menu ul');
					if(sub_menu.css('display') == 'block'){
						menu.removeClass('active');
						sub_menu.css('display', 'none');
					} else {
						menu.addClass('active');
						sub_menu.css('display', 'block');
					}
				}			
			);
			
			$('li.menu').click(
				function(e){
					var menu = $(this).children('a');
					var sub_menu = $(this).children('ul');
					if(sub_menu.css('display') == 'block'){
						menu.removeClass('active');
						sub_menu.css('display', 'none');
					} else {
						menu.addClass('active');
						sub_menu.css('display', 'block');
					}
				}			
			);
			
			$('input[name=voice]').click(
				function(e){
					$(this).parent().parent().submit();
				}			
			);
		
	});
</script>