<div class='content_menu'>
	<ul class='content_menu'>
<?php
		foreach($this->response['content']['menu']['left'] as $menu_item){
?>		
			<li class='content_menu'>
				<a class='<?php if($menu_item['active'] == true) echo 'active'?>'
					href='<?php echo $menu_item['link']?>'>
						<?php echo $menu_item['text']?>
				</a>
			</li>
<?php
		}

		$menu = $this->response['content']['menu']['right'];
		if($menu){
			$menu_item = $menu[1];
?>
			<li class='content_menu' id='sub_menu' style='float: right; margin: 0 10px 0 0; text-align: right'>
				<a class='<?php if($menu_item['active'] == true) echo 'active'?>'
					href='<?php echo $menu_item['link']?>'>
						<?php echo $menu_item['text']?>
				</a>
				<ul class='sub_menu' style='display: none'>
<?php
				foreach($menu_item['sub_menu'] as $sub_menu_item){
?>	
					<li>
						<a class='<?php if($sub_menu_item['active'] == true) echo 'active'?>'
							href='<?php echo $sub_menu_item['link'] ?>' >
							<?php echo $sub_menu_item['text'] ?>
						</a>
					</li>
<?php
				}
?>					
				</ul>
			</li>
<?php
		}
?>	
	</ul>
</div>