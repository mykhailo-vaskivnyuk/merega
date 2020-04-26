<?php
$forms = $content_data;
$form_cancel = $content_data['cancel'];
//print_array($form);
?>
		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $forms['create']['text']?>
			</div>
		</div>
<?php
	if($forms['operation'] == 'edit'): ?>
		<form method='post' style='' action='<?php echo $forms['create']['action']?>'>
			<label>назва спільноти</label>
			<input name='name' type='text'
				value='<?php echo $forms['create']['net_name']?>'/>
			<div></div>		
			<input name='user_id' type='hidden' value='
				<?php if(!empty($forms['create']['user_id'])) echo $forms['create']['user_id'] ?>'/>
			<input name='net_id' type='hidden' value='
				<?php if(!empty($forms['create']['net_id'])) echo $forms['create']['net_id'] ?>'/>
			<input name='operation' type='hidden' value='net_create'/>
			<button type='submit'>Створити</button>
		</form>		
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<button type='submit'>Не створювати</button>
		</form>
<?php
	elseif($forms['operation'] == 'forbid'): ?>
		<form method='post' style='' action='<?php echo $forms['create']['action']?>'>
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
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<input name='operation' type='hidden' value='cancel'>
			<button type='submit'>Відмовитись</button>
		</form>
<?php	
	endif ?>