<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/log
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
		$this->load->model('model_log');
	}
	
	public function index()
	{
		print_error(INCOMPLETE_URI_ERROR);
	}
	
	public function get_log()
	{
		$user_id = $this->input->get('user_id');
		$group_id = $this->input->get('group_id');
		
		if (!$user_id || !$group_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_log->get_log($user_id, $group_id);
		if($data)
		{
			print_response($data);
		}
	}
	
	public function add_log()
	{
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$event_id = $this->input->post('event_id');
		$action = $this->input->post('action');
				
		if (!$user_id || !$group_id || !$event_id || !$action)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_log->add_log($user_id, $group_id, $event_id, $action))
		{
			print_success();
		}
	}
}

/* End of file log.php */
/* Location: ./application/controllers/log.php */