		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $this->response['content']['data']['delete']['text']?>
			</div>
		</div>
		<form method='post' style='' action='<?php echo $this->response['content']['data']['delete']['action']?>'>
			<input name='user_id' type='hidden' value='
				<?php echo  $this->response['content']['data']['delete']['user_id']?>'/>	
			<button type='submit'>Видалити акаунт</button>
		</form>		
		<form method='post' style='margin-top: 0' action='<?php echo $this->response['content']['data']['cancel']['action']?>'>
			<button type='submit'>Відмовитись</button>
		</form>