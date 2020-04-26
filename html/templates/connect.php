<?php
$forms = $content_data;
//print_array($forms);
?>
	<div class='note'>
		<div style='padding: 10px'>
			<?php echo $forms['connect']['text']?>
		</div>
	</div>	
<?php
	if($forms['operation'] == 'forbid'): ?>
		<form method='post' style='' action='<?php echo $forms['connect']['action']?>'>
			<button type='submit'>Ок</button>
		</form>
<?php
	else:
		if($forms['operation'] == 'allow'): ?>
		<form method='post' style='' action='<?php echo $forms['authorize']['action']?>'>
			<button type='submit'>Авторизуватись</button>
		</form>
		<form method='post' style='margin-top: 0' action='<?php echo $forms['registration']['action']?>'>
			<button type='submit' style='margin-top: 2px'>Зареєструватись</button>
		</form>
<?php
		elseif($forms['operation'] == 'allow_authorize'): ?>
		<form method='post' style='' action='<?php echo $forms['authorize']['action']?>'>
			<button type='submit'>Авторизуватись</button>
		</form>
<?php
		else: ?>
		<form method='post' style='' action='<?php echo $forms['registration']['action']?>'>
			<button type='submit'>Зареєструватись</button>
		</form>
<?php
		endif ?>
		<form method='post' style='margin-top: 0' action='<?php echo $forms['cancel']['action']?>'>
			<input name='operation' type='hidden' value='refuse'>
			<button type='submit'>Відмовитись</button>
		</form>
<?php	
	endif ?>