<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/user
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
		$this->load->model('model_user');	
	}
	
	public function index()
	{
		print_error(INCOMPLETE_URI_ERROR);
	}
	
	public function login()
	{
		$user_name = $this->input->post('user_name');
		$password = $this->input->post('password');
		
		if (!$user_name || !$password)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}
		
		$data = $this->model_user->login($user_name, $password);
		if($data)
		{
			print_response($data);
		}
	}
	
	public function renew_token_login()
	{
		$user_name = $this->input->post('user_name');
		$password = $this->input->post('password');
		$fb_token = $this->input->post('fb_token');
		$fb_token_valid_time = $this->input->post('fb_token_valid_time');
		
		if (!$user_name || !$password || !$fb_token || !$fb_token_valid_time)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}
		
		$data = $this->model_user->renew_token_login($user_name, $password, $fb_token, $fb_token_valid_time);
		if($data)
		{
			print_response($data);
		}
	}
	
	public function signup()
	{
		$user_name = $this->input->post('user_name');
		$user_photo = $this->input->post('user_photo');
		$password = $this->input->post('password');
		$fb_id = $this->input->post('fb_id');
		$email = $this->input->post('email');
		$phone_number = $this->input->post('phone_number');
		$fb_token = $this->input->post('fb_token');
		$fb_token_valid_time = $this->input->post('fb_token_valid_time');
				
		if (!$user_name || !$user_photo || !$password || !$fb_id || !$email || !$phone_number || !$fb_token || !$fb_token_valid_time)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_user->signup($user_name, $user_photo, $password, $fb_id, $email, $phone_number, $fb_token , $fb_token_valid_time))
		{
			print_success();
		}
	}
		
	public function get_user_group()
	{
		$user_id = $this->input->get('user_id');				
		if (!$user_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_user->get_user_group($user_id);
		if($data)
		{
			print_response($data);
		}		
	}
	
	public function get_user_event()
	{
		$user_id = $this->input->get('user_id');				
		if (!$user_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_user->get_user_event($user_id);
		if($data)
		{
			print_response($data);
		}		
	}
		
	public function get_user_info()
	{
		$user_id = $this->input->get('user_id');
		if (!$user_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_user->get_user_info($user_id);
		if($data)
		{
			print_response($data);
		}		
	}

	public function search_user()
	{
		$group_id = $this->input->get('group_id');
		$query = $this->input->get('query');
		
		if (!$group_id || !$query)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_user->search_user($group_id,$query);
		if($data)
		{
			print_response($data);
		}		
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */