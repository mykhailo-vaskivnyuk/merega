<?php
$forms = $content_data;
?>
	<div class='note'>
		<div style='padding: 10px'>
			<?php echo $forms['first']['text']?>
		</div>
	</div>	

		<form method='post' style='' action='<?php echo $forms['connect']['action']?>'>
			<button type='submit'>Долучитись</button>
		</form>
		<form method='post' style='margin-top: 0' action='<?php echo $forms['create']['action']?>'>
			<button type='submit' style='margin-top: 2px'>Створити</button>
		</form>		
		<form method='post' style='' action='<?php echo $forms['authorize']['action']?>'>
			<button type='submit'>Авторизація</button>
		</form>