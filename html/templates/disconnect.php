<?php
$form = $content_data['disconnect'];
$form_cancel = $content_data['cancel'];

	if($content_data['operation'] == 'ready'):
?>
		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $form['text']?>
			</div>
		</div>
		<form method='post' style='' action='<?php echo $form['action']?>'>
			<input name='user_id' type='hidden' value='
				<?php echo  $form['user_id']?>'/>
			<input name='net_id' type='hidden' value='
				<?php echo  $form['net_id']?>'/>
			<input name='operation' type='hidden' value='disconnect'/>
			<button type='submit'>Від'єднатись</button>
		</form>		
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<button type='submit'>Відмовитись</button>
		</form>
<?php	endif ?>