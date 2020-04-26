<?php

	$forms = $this->response['content']['data'];
	$operation = $forms['operation'];
	//print_array($operation);?>
		<form method='post' action='<?php echo $forms['authorize']['action']?>'>
			<label>e-mail</label>
			<input name='email' type='text' id='authorize_email'
				value='<?php echo $forms['authorize']['email']?>'/>
			<div></div>
<?php
	if($operation != 'allow_authorize'){ ?>
			<label>пароль</label>
			<input name='password' type='password'/>
<?php
	}
?>			
			<button type='submit'>Увійти</button>
		</form>
<?php
	if($operation == 'allow'){ ?>
		<form id='restore' method='post' action='<?php echo $forms['restore']['action']?>' style='margin-top: 0'>
			<input name='email' type='hidden' id='restore_email'>
			<button type='submit' style='margin-top: 2px'>Вхід через email</button>
		</form>
		<form method='post' action='<?php echo $forms['first']['action']?>' style='margin-top: 0' >
			<button type='submit'>Я тут вперше</button>
		</form>
<?php
	}
	else{ ?>
		<form method='post' action='<?php echo $forms['cancel']['action']?>' style='margin-top: 0' >
			<button type='submit'>Відмовитись</button>
		</form>	
<?php	
	}
?>