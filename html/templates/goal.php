<?php
//print_array($this->response['content']['data']);
$form_data = $this->response['content']['data']['goal'];
	if($content_data['operation'] == 'edit'){ ?>
		<form method='post' action='<?php echo $form_data['action']?>' class='goal' style=''>

			<textarea name='text' class = '' style=''><?php echo $form_data['net_goal']?></textarea>
			
			<input name='operation' type='hidden' value='write'/>		
			<button type='submit'>Зберегти</button>
		</form>
		<form method='post' style='margin-top: 0' action='<?php echo $content_data['cancel']['action'] ?>' class='with_cboxes'>
			<div style='float: right; padding: 5px 0 0 20px; height: 100%'></div>
			<button type='submit'>Не зберігати</button>
		</form>		
<?php
	}
	else{ ?>
		<form method='post' action='<?php echo $form_data['action']?>' class='goal' style=''>

			<textarea name='text' class = 'disabled' readonly style=''><?php echo $form_data['net_goal']?></textarea>
			
			<input name='operation' type='hidden' value='edit'/>
<?php
		if($form_data['action']){ ?>
			<button type='submit'>Редагувати</button>
<?php
		} ?>
		</form>
<?php
	} ?>