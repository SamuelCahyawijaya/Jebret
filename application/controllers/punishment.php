<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Punishment extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/punishment
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
		$this->load->model('model_punishment');
		$this->load->model('model_group');
	}
	
	public function index()
	{
		print_error(INCOMPLETE_URI_ERROR);
	}
	
	public function get_punishment()
	{
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		
		if (!$user_id || !$group_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_punishment->get_punishment($user_id, $group_id);
		if($data)
		{
			print_response($data);
		}
	}
	
	public function finish_punishment()
	{
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$punishment = $this->input->post('punishment');
		
		if (!$user_id || !$group_id || !$punishment)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_punishment->get_punishment($user_id, $group_id, $punishment);
		if($data)
		{
			// Send broadcast email to all group member except user_id 
			$group_users = $this->model_group->get_group_users($group_id);
			$count = count($group_users);
			$j = 0;
			for ($i=0; $i < $count; $i++)
			{
				if ($group_users[$i]->user_id != $user_id)
				{
					$recipient[$j] = $group_users[$i];
					$j++;
				}
				else
				{
					$sender = $group_users[$i];
				}
			}
			
			$this->load->library('email');

			$count = count($recipient);
			for ($i = 0; $i < $count; $i++)
			{
				$url = SERVER_BASE_URL . 'vote?group_id='.$group_id.'&user_id='.$recipient[$i]->user_id.'&punishment_id='.$data[0]['punishment_id'];
				$this->email->from($sender->email, $sender->user_name);
				$this->email->to($recipient[$i]->email);
				
				$this->email->subject('[Punishment Finished] '.$sender->user_name.'('.$sender->email.')');
				$this->email->message('Please vote me if I have already finished my punishment "'.$data[0]['description'].'" : '.$url);

				if(!$this->email->send())
				{
					print_error(SEND_MAIL_ERROR);
					return;
				} 
			}
			print_success();
		}	
	}	
}

/* End of file punishment.php */
/* Location: ./application/controllers/punishment.php */