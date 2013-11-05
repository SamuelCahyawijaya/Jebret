<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/event
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
		$this->load->model('model_event');
	}
	
	public function index()
	{
		print_error(INCOMPLETE_URI_ERROR);
	}
	
	public function create_event()
	{
		$event_name = $this->input->post('event_name');
		$group_id = $this->input->post('group_id');
		$creator_user_id = $this->input->post('creator_user_id');
		$location_name = $this->input->post('location_name');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$time = $this->input->post('time');
		$cancel_time = $this->input->post('cancel_time');

		if (!$event_name ||!$group_id || !$creator_user_id || !$location_name || !$latitude || !$longitude || !$time || !$cancel_time)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$event = $this->model_event->create_event($event_name, $group_id, $creator_user_id, $location_name, $latitude, $longitude, $time, $cancel_time);
		if($event)
		{
			print_response($event);
		}
	}
	
	public function edit_event()
	{
		$event_id = $this->input->post('event_id');
		$event_name = $this->input->post('event_name');
		$location_name = $this->input->post('location_name');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$time = $this->input->post('time');
		$cancel_time = $this->input->post('cancel_time');

		if (!$event_id ||!$event_name || !$location_name || !$latitude || !$longitude || !$time || !$cancel_time)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_event->edit_event($event_id, $event_name, $location_name, $latitude, $longitude, $time, $cancel_time))
		{
			print_success();
		}
	}
	
	public function remove_event()
	{
		$event_id = $this->input->post('event_id');

		if (!$event_id )
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_event->remove_event($event_id))
		{
			print_success();
		}
	}
	
	public function add_attendee()
	{
		$event_id = $this->input->post('event_id');
		$user_ids = $this->input->post('user_ids');
		$group_id = $this->input->post('group_id');
		
		if (!$event_id || ! $user_ids || !$group_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$user_ids = explode(',',$user_ids);
		if($this->model_event->add_attendee($event_id,$user_ids,$group_id))
		{
			print_success();
		}
	}
	
	public function remove_attendee()
	{
		$event_id = $this->input->post('event_id');
		$user_ids = $this->input->post('user_ids');
		
		if (!$event_id || ! $user_ids)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$user_ids = explode(',',$user_ids);
		if($this->model_event->remove_attendee($event_id,$user_ids))
		{
			print_success();
		}
	}
	
	public function close_event()
	{
		$event_id = $this->input->post('event_id');

		if (!$event_id )
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_event->close_event($event_id))
		{
			print_success();
		}
	}
	
	public function check_in()
	{
		$user_id = $this->input->post('user_id');
		$event_id = $this->input->post('event_id');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');

		if (!$user_id || !$event_id || !isset($latitude) || !isset($longitude))
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_event->check_in($user_id, $event_id, $latitude, $longitude))
		{
			print_success();
		}
	}
	
	public function absent()
	{
		$user_id = $this->input->post('user_id');
		$event_id = $this->input->post('event_id');

		if (!$user_id || !$event_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_event->absent($user_id, $event_id))
		{
			print_success();
		}
	}
	
	public function get_event()
	{
		$event_id = $this->input->get('event_id');
		
		if (!$event_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}
		
		$data = $this->model_event->get_event($event_id);
		if($data)
		{
			print_response($data);
		}	
	}
	
	public function get_event_attendee()
	{
		$event_id = $this->input->get('event_id');

		if (!$event_id )
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$data = $this->model_event->get_event_attendee($event_id);
		if($data)
		{
			print_response($data);
		}
	}
}

/* End of file group.php */
/* Location: ./application/controllers/group.php */