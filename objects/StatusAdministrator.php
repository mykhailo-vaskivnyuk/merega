<?php

class StatusAdministrator
{
    private $site;
	private $data;
	private $user;
	private $response;
	private $parent;
	private $command;
	private $status_plus_1;
	
    public function __construct(&$site, &$parent)
    {
		$this->site = &$site;
		$this->data = &$site->data;
		$this->user = &$site->user;
		$this->response = &$site->response;
		$this->parent = &$parent;
		$this->command = &$site->data->command;
	}
    
	public function runStatusAdministrator()
    {
		$this->user->setUser('', '', 10);
		
		require_once 'objects/StatusPlus1.php';
		$this->status_plus_1 = new StatusPlus1($this->site, $this);
		
		$this->command['type'] = 'limit_on_vote';
		
		if($this->user->getUser('status') == 10){
			switch ($this->command['type']){
				case 'limit_on_vote':
					//print_array('HELLO');
					$this->chkLimitOnVote();
					break;
				default:
					//...
			}
			//if(!$this->data->redirection){
			//	$this->user->setUser('enter', false);
			//	$this->response->setResponse('', '', $this->command, $this->user, $this->data);
			//}
		}
		else //...
		return true;
	}
	
	private function chkLimitOnVote()
	{
		$nets = $this->status_plus_1->setNet('user_nets');
		//print_array($nets);
		foreach($nets as $net){
			$this->user->setUser('net', $net);
			$this->chkLimitOnVote();
			$this->user->setUser('net', $net);
			//print_array('HELLO');
			$this->status_plus_1->chkLimits('limit_on_vote');
		}
		return;
	}
}
?>