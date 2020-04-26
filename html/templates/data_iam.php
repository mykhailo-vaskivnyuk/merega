<?php
$content_data = $this->response['content']['data'];
$form_data = $content_data['data'];
if($content_data['operation'] == 'edit'){
?>
		<form method='post' action='<?php echo $form_data['action']?>' class='with_cboxes'>
			<div style='float: right; padding: 7px 0 0 5px'>
				<input name='name' type='checkbox' title='Показувати в КОЛІ!' style='' <?php echo $form_data['name_show']?> />
			</div>
			<label>ім'я</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo  $form_data['name']?>'/>
			<div style='clear: right'></div>
			
			<div style='float: right; padding: 7px 0 0 5px'>
				<input name='email' type='checkbox' title='Показувати в КОЛІ!' style='' <?php echo $form_data['email_show']?> />
			</div>			
			<label>email</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo  $form_data['email']?>'/>
			<div style='clear: right'></div>

			<div style='float: right; padding: 7px 0 0 5px'>
				<input name='mobile' type='checkbox' title='Показувати в КОЛІ!' style='' <?php echo $form_data['mobile_show']?> />
			</div>				
			<label>мобільний</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo  $form_data['mobile']?>'/>
			<div style='clear: right'></div>
			
			<div style='float: right; padding: 5px 0 0 20px; #height: 45px'></div>			
			<input name='user_id' type='hidden' value='<?php echo $form_data['user_id'] ?>'/>
			<input name='operation' type='hidden' value='write'/>
			<button type='submit'>Зберегти</button>
		</form>	
		<form method='post' style='margin-top: 0' action='<?php echo $content_data['cancel']['action'] ?>' class='with_cboxes'>
			<div style='float: right; padding: 5px 0 0 20px; height: 100%'></div>
			<button type='submit'>Не зберігати</button>
		</form>
<?php
}
elseif($form_data['action']){
?>
		<form method='post' action='<?php echo $form_data['action']?>' class='with_cboxes'>
			<div style='float: right; padding: 7px 0 0 5px'>
				<input name='name' type='checkbox' title='Показувати в КОЛІ!' style='' disabled <?php echo $form_data['name_show']?> />
			</div>
			<label>ім'я</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo $form_data['name']?>'/>
			<div style='clear: right'></div>
			
			<div style='float: right; padding: 7px 0 0 5px'>
				<input name='email' type='checkbox' title='Показувати в КОЛІ!' style='' disabled <?php echo $form_data['email_show']?> />
			</div>
			<label>email</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo  $form_data['email']?>'/>
			<div style='clear: right'></div>
			
			<div style='float: right; padding: 7px 0 0 5px'>
				<input name='mobile' type='checkbox' title='Показувати в КОЛІ!' style='' disabled <?php echo $form_data['mobile_show']?> />
			</div>
			<label>мобільний</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo  $form_data['mobile']?>'/>
			<div style='clear: right'></div>
			
			<div style='float: right; padding: 5px 0 0 20px; #height: 45px'></div>			
			<input name='operation' type='hidden' value='edit'/>			
			<button type='submit'>Редагувати</button>
		</form>
<?php
}
else{
?>
		<form method='post' action=''>

			<label>ім'я</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo $form_data['name']?>'/>
			<div></div>

			<label>email</label>
			<input name='email' type='text' class='disabled' readonly
				value='<?php echo  $form_data['email']?>'/>
			<div></div>
			
			<label>мобільний</label>
			<input name='mobile' type='text' class='disabled' readonly
				value='<?php echo  $form_data['mobile']?>'/>
			<div></div>
			
		</form>
<?php
}
?>






