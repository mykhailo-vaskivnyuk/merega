<div class='content'>
<?php
	require_once 'content_menu.php';
?>
	<div class='content_data'>
<?php
		require_once './html/sub_menu.php';
		
		require_once './html/messages.php';

		switch($this->response['content']['data']['template']){
			case 'authorize':
				require_once 'templates/authorize.php';
				break;
			case 'registration':
				require_once 'templates/registration.php';
				break;
			case 'data':
				require_once 'templates/data.php';
				break;
			case 'data_circle_tree':
				require_once 'templates/data_circle_tree.php';
				break;
			case 'data_iam':
				require_once 'templates/data_iam.php';
				break;
			case 'data_net':
				require_once 'templates/data_net.php';
				break;
			case 'goal':
				require_once 'templates/goal.php';
				break;			
			case 'notification':
				require_once 'templates/notification.php';
				break;
			case 'delete':
				require_once 'templates/delete.php';
				break;
			case 'invitation':
				require_once 'templates/invitation.php';
				break;
			case 'disconnect':
				require_once 'templates/disconnect.php';
				break;
			case 'statistic':
				require_once 'templates/statistic.php';
				break;
			case 'connect':
				require_once 'templates/connect.php';
				break;
			case 'first':
				require_once 'templates/first.php';
				break;
			case 'create':
				require_once 'templates/create.php';
				break;
			case 'vote':
				require_once 'templates/vote.php';
				break;				
			default:
				require_once 'templates/default.php';
		}
?>
	</div>
</div>

<div style='clear: left'></div>