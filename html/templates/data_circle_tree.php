<?php
$content_data = $this->response['content']['data'];
$form_data = $content_data['data'];
//print_array($form_data);
if($content_data['operation'] == 'edit'){
?>
		<form method='post' action='<?php echo $form_data['action']?>'>
			<label>ім'я</label>
			<input name='name' type='text' class='disabled' readonly
				value='<?php echo  $form_data['name']?>'/>
			<div></div>
			<label>ім'я для мене</label>
			<input name='list_name' type='text'
				value='<?php echo  $form_data['list_name']?>'/>
			<div></div>
			<label>email</label>
			<input name='email' type='text' class='disabled' readonly
				value='<?php echo  $form_data['email']?>'/>
			<div></div>
			<label>мобільний</label>
			<input name='mobile' type='text' class='disabled' readonly
				value='<?php echo  $form_data['mobile']?>'/>
			<div></div>
			<label style='line-height: 27px; vertical-align: top'>примітка</label>
			<textarea name='note'><?php echo $form_data['note'] ?></textarea>
			<div></div>
			<input name='user_id' type='hidden' value='<?php echo $form_data['user_id'] ?>'/>
			<input name='member_node' type='hidden' value='<?php echo $form_data['member_node'] ?>'/>
			<input name='member_id' type='hidden' value='<?php echo $form_data['member_id'] ?>'/>
			<input name='operation' type='hidden' value='write'/>
			<button type='submit'>Зберегти</button>
		</form>
		<form method='post' style='margin-top: 0' action='<?php echo $content_data['cancel']['action'] ?>'>
			<button type='submit'>Не зберігати</button>
		</form>	
<?php
}
else{
?>
		<form method='post' action='<?php echo $form_data['action']?>'>
			<label>ім'я</label>
			<input name='name' type='text' class='disabled' readonly
				value='<?php echo  $form_data['name']?>'/>
			<div></div>

			<label>ім'я для мене</label>
			<input name='list_name' type='text' class='disabled' readonly
				value='<?php echo  $form_data['list_name']?>'/>
			<div></div>

			<label>email</label>
			<input name='email' type='text' class='disabled' readonly
				value='<?php echo  $form_data['email']?>'/>
			<div></div>
			
			<label>мобільний</label>
			<input name='mobile' type='text' class='disabled' readonly
				value='<?php echo  $form_data['mobile']?>'/>
			<div></div>

			<label style='line-height: 27px; vertical-align: top'>примітка</label>
			<textarea name='note' class='disabled' readonly><?php echo $form_data['note'] ?></textarea>
			<div></div>
			
			<input name='operation' type='hidden' value='edit'/>			
			<button type='submit'>Редагувати</button>
		</form>
<?php
}
?>