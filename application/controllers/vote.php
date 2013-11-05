<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vote extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/vote
	 *
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();	
		$this->load->model('model_vote');
		$this->load->model('model_group');
		$this->load->model('model_punishment');
	}
	
	public function index()
	{
		print_error(INCOMPLETE_URI_ERROR);
	}
	
	public function vote_user()
	{
		$group_id = $this->input->get('group_id');
		$user_id = $this->input->get('user_id');
		$punishment_id = $this->input->get('punishment_id');
		$status = ACOMPLISHED;
		
		if (!$group_id || !$user_id || !$punishment_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_vote->vote_user($group_id, $user_id, $punishment_id, $status))
		{
			// Send broadcast email to inform deletion
			$group_users = $this->model_group->get_group_users($group_id);
			$punishment = $this->model_punishment->get_punishment_by_id($punishment_id);

			if ($punishment)
			{
				$count = count($group_users);
				
				// Get Punished User
				$to = '';
				for ($i = 0; $i < $count; $i++)
				{
					if ($group_users[$i]->user_id == $punishment->user_id)
					{
						$punished_user = $group_users[$i];
					}
					else
					{
						$to .= $group_users[$i]->email.',';
					}
				}

				if ($to)
				{
					$to = substr($to,0,strlen($to) - 1);
					$this->load->library('email');
					
					$this->email->from(APPLICATION_EMAIL, APPLICATION_NAME);
					$this->email->to($to); 

					$this->email->subject('[Punishment Completed] '.$punishment->description);
					$this->email->message('Horay! "'.$punished_user->user_name.'" has completed his/her punishment!');

					if (!$this->email->send())
					{
						print_error(SEND_MAIL_ERROR);
						return;
					}
					
					if ($this->model_punishment->delete_punishment($punishment_id))
					{
						print_success();
					}
					else
					{
						print_error(DATABASE_ERROR);
					}
				}
				else
				{
					print_success();
				}
			}			
			else
			{
				print_error(PUNISHMENT_DELETED_ERROR);
				return;
			}
		}
	}
}

/* End of file vote.php */
/* Location: ./application/controllers/vote.php */