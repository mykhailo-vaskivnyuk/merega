<div class='menu'>
	<ul class='menu'>
<?php
	foreach($this->response['menu']['left'] as $menu_item){
?>		
		<li class='menu'>
			<a class='<?php if($menu_item['active'] == true) echo 'active'?>'
				href='<?php echo $menu_item['link']?>'>
					<?php echo $menu_item['text']?>
			</a>
		</li>
<?php
	}

	foreach($this->response['menu']['right'] as $menu_item){
	//print_array($menu_item);
?>
		<li class='menu' rel='menu' style='float: right; margin: 0 10px 0 0; text-align: right; width: 60px'>
			<a class='<?php if($menu_item['active'] == true) echo 'active'?>'
				href='<?php echo $menu_item['link']?>'>
					<?php echo $menu_item['text']?>
			</a>
			
<?php
			if($menu_item['sub_menu']){
?>
				<ul class='sub_menu' style='display: none; float: right; width: 120px'>
<?php
				foreach($menu_item['sub_menu'] as $sub_menu_item){
?>	
					<li style='display: block'>
						<a class='<?php if($sub_menu_item['active'] == true) echo 'active'?>' style='display: block'
							href='<?php echo $sub_menu_item['link'] ?>' >
							<?php echo $sub_menu_item['text'] ?>
						</a>
					</li>
<?php
				}
?>					
				</ul>
<?php		} ?>
		</li>
<?php
	}
?>	
	</ul>
</div>

<div style='clear: both'></div>