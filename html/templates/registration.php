<?php
	$form_data = $this->response['content']['data']['registration'];
	$form_cancel = $this->response['content']['data']['cancel'];
	$operation = $this->response['content']['data']['operation']?>
		<form method='post' action='<?php echo $form_data['action']?>'>
			<label>ім'я</label>
			<input name='name' type='text'
				value='<?php echo  $form_data['name']?>'/>
			<div></div>
			<label>email</label>
			<input name='email' type='text' <?php if($operation == 'allow_registration') echo "class='disabled' readonly" ?>
				value='<?php echo  $form_data['email']?>'/>
			<div></div>
<!--			<label>пароль</label>
			<input name='password' type='password'/>
			<div></div> -->
<!--		<label>мобільний</label>
			<input name='mobile' type='text'
				value='<?php //echo  $form_data['mobile']?>'/>	
			<div></div> -->			
			<button type='submit'>Зареєструватись</button>
		</form>
<?php
	if($operation == 'allow_registration'){ ?>
		<form method='post' action='<?php echo $form_cancel['action'] ?>' style='margin-top: 0'>
			<button type='submit'>Відмовитись</button>
		</form>
<?php
	}
	else{ ?>
		<form method='post' action='<?php echo $form_cancel['action'] ?>' style='margin-top: 0'>
			<button type='submit'>Вийти</button>
		</form>
<?php
	} ?>