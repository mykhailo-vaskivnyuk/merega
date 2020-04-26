<?php
$form = $content_data['invitation'];
$form_cancel = $content_data['cancel'];
//print_array($form);
	if($content_data['operation'] == 'ready'): ?>
		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $form['text']?>
			</div>
		</div>
		<form method='post' style='' action='<?php echo $form['action']?>'>
			<label>email</label>
			<input name='email' type='text'
				value=''/>
			<div></div>		
			<input name='user_id' type='hidden' value='
				<?php echo  $form['user_id']?>'/>
			<input name='member_node' type='hidden' value='
				<?php echo  $form['member_node']?>'/>
			<input name='operation' type='hidden' value='create'/>
			<button type='submit'>Сформувати</button>
		</form>		
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<button type='submit'>Не формувати</button>
		</form>
<!------------------------------------------------------------------------------------------------>
<?php	elseif($content_data['operation'] == 'waiting'): ?>
		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $form['text']?>
			</div>
		</div>
		
		<form method='post' action='<?php echo $form['action']?>'>

<?php
		if(!$form['email']) { ?>
			<label>запрошення</label>	
			<input name='invite' type='text' class = 'disabled' readonly
				value='<?php echo $form['invite']?>'/>
			<div></div>
<?php
		} ?>
<!--
			<label>ім'я</label>
			<input name='name' type='text' class = 'disabled' readonly
				value='<?php echo  $form['name']?>'/>
			<div></div>
-->			
			<label>ім'я для мене</label>	
			<input name='list_name' type='text' class = 'disabled' readonly
				value='<?php echo $form['list_name']?>'/>
			<div></div>
<?php
		if($form['email']) { ?>			
			<label>email</label>
			<input name='email' type='text' class = 'disabled' readonly
				value='<?php echo $form['email']?>'/>
			<div></div>
<?php
		} ?>
<!--			
			<label>мобільний</label>
			<input name='mobile' type='text' class = 'disabled' readonly
				value='<?php echo $form['mobile']?>'/>
			<div></div>
-->
			<label style='line-height: 27px; vertical-align: top'>примітка</label>
			<textarea name='note' class = 'disabled' readonly><?php echo $form['note'] ?></textarea>
			<div></div>
			
			<input name='operation' type='hidden' value='edit'/>		
			<button type='submit'>Редагувати</button>
		</form>	
		<form method='post' style='margin-top: 0' action='<?php echo $form['action']?>'>
			<input name='user_id' type='hidden' value='
				<?php echo  $form['user_id']?>'/>
			<input name='member_node' type='hidden' value='
				<?php echo  $form['member_node']?>'/>
			<input name='operation' type='hidden' value='delete'/>
			<button type='submit' style='margin-top: 2px'>Скасувати</button>
		</form>		
		<!--
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<button type='submit'>Відмовитись</button>
		</form>
		-->
<!------------------------------------------------------------------------------------------------>
<?php	//ДУЖЕ СХОЖЕ НА ПОПЕРЕДНЮ ЧАСТИНУ
		elseif($content_data['operation'] == 'edit'): ?>
		<form method='post' action='<?php echo $form['action']?>'>
<?php
		if(!$form['email']) { ?>
			<label>запрошення</label>
			<input name='invite' type='text' class = 'disabled' readonly
				value='<?php if($form['email']) echo 'приховано'; else echo $form['invite']?>'/>
			<div></div>
<?php
		} ?>
<!--
			<label>ім'я</label>
			<input name='name' type='text' class = 'disabled' readonly
				value='<?php echo  $form['name']?>'/>
			<div></div>
-->			
			<label>ім'я для мене</label>	
			<input name='list_name' type='text'
				value='<?php echo $form['list_name']?>'/>
			<div></div>
<?php
		if($form['email']) { ?>	
			<label>email</label>
			<input name='email' type='text' class = 'disabled' readonly
				value='<?php echo $form['email']?>'/>
			<div></div>
<?php
		} ?>
<!--
			<label>мобільний</label>
			<input name='mobile' type='text' class = 'disabled' readonly
				value='<?php echo $form['mobile']?>'/>
			<div></div>
-->
			<label style='line-height: 27px; vertical-align: top'>примтіка</label>
			<textarea name='note'><?php echo $form['note'] ?></textarea>
			<div></div>

			<input name='user_id' type='hidden' value='
				<?php echo  $form['user_id']?>'/>
			<input name='member_node' type='hidden' value='
				<?php echo  $form['member_node']?>'/>
<!-- можливо ще потрібно user_node -->
			<input name='operation' type='hidden' value='write'/>		
			<button type='submit'>Зберегти</button>
		</form>	
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<button type='submit'>Не зберігати</button>
		</form>
<!------------------------------------------------------------------------------------------------>
<?php	elseif($content_data['operation'] == 'connected'): ?>
		<div class='note'>
			<div style='padding: 10px'>
				<?php echo $form['text']?>
			</div>
		</div>
		<form method='post' style='' action='<?php echo $form['action']?>'>
<!--
			<label>запрошення</label>	
			<input name='invite' type='text' class = 'disabled' readonly
				value='<?php //echo $form['invite']?>'/>
			<div></div>
-->
			<label>ім'я</label>
			<input name='name' type='text' class = 'disabled' readonly
				value='<?php echo  $form['name']?>'/>
			<div></div>
			
			<label>ім'я для мене</label>	
			<input name='list_name' type='text' class = 'disabled' readonly
				value='<?php echo $form['list_name']?>'/>
			<div></div>
		
			<label>email</label>
			<input name='email' type='text' class = 'disabled' readonly
				value='<?php echo $form['email']?>'/>
			<div></div>
			
			<label>мобільний</label>
			<input name='mobile' type='text' class = 'disabled' readonly
				value='<?php echo $form['mobile']?>'/>
			<div></div>

			<label style='line-height: 27px; vertical-align: top'>примітка</label>
			<textarea name='note' class = 'disabled' readonly><?php echo $form['note'] ?></textarea>
			<div></div>	

			<input name='user_id' type='hidden' value='
				<?php echo  $form['user_id']?>'/>
			<input name='member_node' type='hidden' value='
				<?php echo  $form['member_node']?>'/>
			<input name='member_id' type='hidden' value='
				<?php echo  $form['member_id']?>'/>				
			<input name='operation' type='hidden' value='approve'/>
			<button type='submit'>Ідентифікувати</button>
		</form>
		<form method='post' style='margin-top: 0' action='<?php echo $form['action']?>'>
			<input name='user_id' type='hidden' value='
				<?php echo  $form['user_id']?>'/>
			<input name='member_node' type='hidden' value='
				<?php echo  $form['member_node']?>'/>
			<input name='member_id' type='hidden' value='
				<?php echo  $form['member_id']?>'/>				
			<input name='operation' type='hidden' value='refuse'/>
			<button type='submit' style='margin-top: 2px'>Відмовити</button>
		</form>		
		<form method='post' style='margin-top: 0' action='<?php echo $form_cancel['action']?>'>
			<button type='submit'>Подумати</button>
		</form>
<?php	endif ?>