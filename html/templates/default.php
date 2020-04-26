<?php
if(!empty($content_data['default']))
	$form = $content_data['default'];
?>
		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $form['text']?>
			</div>
		</div>
		<form method='post' style='' action='<?php echo $form['action']?>'>
			<button type='submit'>ОК</button>
		</form>
		<!--
		<form method='post' style='margin-top: 0' action='<?php //echo $form_cancel['action']?>'>
			<button type='submit'>Відмовитись</button>
		</form>
		-->