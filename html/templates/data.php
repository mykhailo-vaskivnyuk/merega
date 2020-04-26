<?php
if($this->response['content']['data']['operation'] == 'read'){
?>
		<form method='post' action='<?php echo $this->response['content']['data']['data']['action']?>'>
			<label>ім'я</label>
			<input name='name' type='text' class = 'disabled' readonly
				value='<?php echo  $this->response['content']['data']['data']['name']?>'/>
			<div></div>
			<label>email</label>
			<input name='email' type='text' class = 'disabled' readonly
				value='<?php echo  $this->response['content']['data']['data']['email']?>'/>
			<div></div>
			<!--
			<label>пароль</label>
			<input name='password' type='password' class = 'disabled' readonly />
			<div></div>
			-->
			<label>мобільний</label>
			<input name='mobile' type='text' class = 'disabled' readonly
				value='<?php echo  $this->response['content']['data']['data']['mobile']?>'/>
			<div></div>
			<input name='operation' type='hidden' value='edit'/>			
			<button type='submit'>Редагувати</button>
		</form>
<?php
}
else{
?>
		<form method='post' action='<?php echo $this->response['content']['data']['data']['action']?>'>
			<label>ім'я</label>
			<input name='name' type='text'
				value='<?php echo  $this->response['content']['data']['data']['name']?>'/>
			<div></div>
			<label>email</label>
			<input name='email' type='text' class = 'disabled' readonly
				value='<?php echo  $this->response['content']['data']['data']['email']?>'/>
			<div></div>
			<label>мобільний</label>
			<input name='mobile' type='text'
				value='<?php echo  $this->response['content']['data']['data']['mobile']?>'/>
			<div></div>
			<label>пароль</label>
			<input name='password' type='password'/>
			<div></div>
			<input name='operation' type='hidden' value='write'/>
			<input name='user_id' type='hidden' value='
				<?php echo  $this->response['content']['data']['data']['user_id']?>'/>				
			<button type='submit'>Зберегти</button>
		</form>
<?php	if($this->response['content']['data']['data']['password']){ ?>
		<form method='post' action='<?php echo $this->response['content']['data']['data']['action']?>' style='margin-top: 0'>
			<input name='operation' type='hidden' value='reset_password'>
			<input name='user_id' type='hidden' value='
				<?php echo  $this->response['content']['data']['data']['user_id']?>'/>	
			<button type='submit' style='margin-top: 2px'>Скинути пароль</button>
		</form>
<?php	} ?>
		<form method='post' style='margin-top: 0' action='<?php echo $this->response['content']['data']['cancel']['action']?>'>		
			<button type='submit'>Не зберігати</button>
		</form>	
<?php
}
?>