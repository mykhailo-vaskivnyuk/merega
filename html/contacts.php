<div class='contacts'>
<?php
	if($this->response['contacts']):
?>
		<div class='contacts_menu'>
			<ul>		
				<li>
					<a class='active'
						href='<?php echo $this->response['contacts']['menu']['link']?>'>
							<?php echo $this->response['contacts']['menu']['text']?>
					</a>
				</li>
			</ul>
		</div>	
<?php
		foreach($this->response['contacts']['data']['circle'] as $key => $member):
			if(!$member):
?>
				<div class='disabled'>
		
				</div>
<?php
			elseif($key == 7):
?>				
				<form method='post' class='contacts_data circle_tree_down' action='
					<?php echo $member['action'] ?>'>
						<div class='marker_status'>
							<div class='marker <?php echo $member['status']['marker']?>'></div>
							<div class='dislike'></div>
							<div class='status'></div>
						</div>					
				</form>
<?php	
				continue;
			else:
?>
				<form method='post' class='contacts_data <?php if($member['active']) echo 'active';
					else echo $member['status']['css'] ?>' action='
					<?php echo $member['action'] ?>'>
						<div class='marker_status'>
							<div class='marker <?php echo $member['status']['marker']?>'></div>
							<div class='dislike'><?php echo $member['status']['dislike']?></div>
							<div class='status'>
								<?php echo $member['status']['text']?>
							</div>
						</div>
						<?php echo $member['text'] ?>
				</form>
<?php		
			endif;
		endforeach;

		foreach($this->response['contacts']['data']['tree'] as $key => $member):
			if(!$member):
?>
				<div class='disabled'>
		
				</div>
<?php
			elseif($key == 0):
?>				
				<form method='post' class='contacts_data circle_tree_up' action='
					<?php echo $member['action'] ?>'>
						<div class='marker_status'>
							<div class='marker <?php echo $member['status']['marker']?>'></div>
							<div class='dislike'></div>
							<div class='status'></div>
						</div>
				</form>
<?php	
				continue;
			else:
?>
				<form method='post' class='contacts_data <?php if($member['active']) echo 'active';
					else echo $member['status']['css'] ?>' action='
					<?php echo $member['action'] ?>'>
						<div class='marker_status'>
							<div class='marker <?php echo $member['status']['marker']?>'></div>
							<div class='dislike'><?php echo $member['status']['dislike']?></div>
							<div class='status'>
								<?php echo $member['status']['text']?>
							</div>
						</div>
						<?php echo $member['text'] ?>
				</form>
<?php		
			endif;
		endforeach;
	else:
?>
	<div class='empty_contacts'>

	</div>
<?php
	endif;
?>
</div>