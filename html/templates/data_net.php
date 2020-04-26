<?php
$content_data = $this->response['content']['data'];
$form_data = $content_data['data'];
//print_array($form_data);
if($content_data['operation'] == 'edit'){
?>
		<form method='post' action='<?php echo $form_data['action']?>'>
			<label>назва спільноти</label>
			<input name='name' type='text'
				value='<?php echo  $form_data['name']?>'/>
			<div></div>
<?php		foreach($form_data['links'] as $link_key => $net_link){ ?>
				<label><?php echo  $link_key?>-й ресурс</label>
				<input name='link_name_<?php echo  $link_key ?>' type='text'
					value='<?php echo  $net_link['resource_name']?>'/>
				<div></div>
				<label>лінк</label>
				<input name='link_value_<?php echo  $link_key ?>' type='text'
					value='<?php echo  $net_link['resource_link']?>'/>
				<div></div>	
<?php		} ?>
			<input name='user_id' type='hidden' value='<?php echo  $form_data['user_id']?>'/>
			<input name='net_id' type='hidden' value='<?php echo  $form_data['net_id']?>'/>
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
			<label>назва спільноти</label>
			<input name='' type='text' class='disabled' readonly
				value='<?php echo  $form_data['name']?>'/>
			<div></div>
<?php		foreach($form_data['links'] as $link_key => $net_link){ ?>
				<label><?php echo  $link_key ?>-й ресурс</label>
				<input name='' type='text' class='disabled' readonly
					value='<?php echo  $net_link['resource_name']?>'/>
				<div></div>
				<label>лінк</label>
				<a class='' href='<?php echo  $net_link['resource_link']?>' target='_blank'>
					<input name='' type='text' class='disabled'
						value='<?php echo  $net_link['resource_link']?>'/>
				</a>
				<div></div>			
<?php		} ?>	
			<input name='operation' type='hidden' value='edit'/>
<?php
			if($form_data['action']){ ?>
				<button type='submit'>Редагувати</button>
<?php
			} ?>
		</form>
<?php
}
?>