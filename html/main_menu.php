<div class='main_menu'>

	<ul>
		<!-- item_01 -->
		<li class='left'>
			<a href='<?php echo $this->response['main_menu'][1]['link']?> '>
				<?php echo $this->response['main_menu'][1]['text']?>
			</a>
		</li>
		
		<!-- item_03 -->
		<li class='right'>
			<?php if($this->response['main_menu'][3]){?>
			<a href='<?php echo $this->response['main_menu'][3]['link']?>'>
				<?php echo $this->response['main_menu'][3]['text']?>
			</a>
			<?php }
			else echo '&nbsp;'?>
		</li>

		<!-- item_02 -->
		<li class='center'>
			<a class='active'
				href='<?php echo $this->response['main_menu'][2]['link']?>'>
				<?php echo $this->response['main_menu'][2]['text']?>
			</a>
		</li>
	</ul>
</div>