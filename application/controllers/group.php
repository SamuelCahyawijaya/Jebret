<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/group
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
		$this->load->model('model_group');
	}
	
	public function index()
	{
		print_error(INCOMPLETE_URI_ERROR);
	}
	
	public function create_group()
	{
		$creator_user_id = $this->input->post('creator_user_id');
		$group_name = $this->input->post('group_name');
		$group_description = $this->input->post('group_description');

		if (!$creator_user_id || !$group_name || !$group_description)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$config['upload_path'] = BASEPATH.'/image/';
		$config['allowed_types'] = 'jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$config['file_name']  = md5(date(DATE_RFC822));
		$config['overwrite']  = true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('group_photo'))
		{
			print_error(UPLOAD_FILE_ERROR);
			return;
		}
		
		$upload_data = $this->upload->data();
		$group_photo = $upload_data['file_name'];

		$data = $this->model_group->create_group($creator_user_id, $group_name, $group_description, $group_photo);
		if($data)
		{
			print_response($data);
		}
	}
	
	public function edit_group()
	{
		$group_id = $this->input->post('group_id');
		$group_name = $this->input->post('group_name');
		$group_description = $this->input->post('group_description');
		
		if (!$group_id || !$group_name || !$group_description)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}
		
		if($this->model_group->edit_group($group_id, $group_name, $group_description))
		{
			print_success();
		}
	}
	
	public function edit_group_photo()
	{
		$group_id = $this->input->post('group_id');
		$group = $this->model_group->get_group_info($group_id);	
		
		$config['upload_path'] = BASEPATH.'/image/';
		$config['allowed_types'] = 'jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$config['file_name']  = $group->group_photo;
		$config['overwrite']  = true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('group_photo'))
		{
			print_error(UPLOAD_FILE_ERROR);
			return;
		}
		else
		{
			print_success();
		}
	}
	
	public function remove_group()
	{
		$group_ids = $this->input->post('group_ids');
		
		if (!$group_ids)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$group_ids = explode(',',$group_ids);
		if($this->model_group->remove_group($group_ids))
		{
			print_success();
		}
	}
	
	public function add_group_member()
	{
		$group_id = $this->input->post('group_id');
		$user_ids = $this->input->post('user_ids');
		
		if (!$group_id || ! $user_ids)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$user_ids = explode(',',$user_ids);
		if($this->model_group->add_group_member($group_id,$user_ids))
		{
			print_success();
		}
	}
	
	public function leave_group()
	{
		$group_id = $this->input->post('group_id');
		$user_id = $this->input->post('user_id');
		
		if (!$group_id || ! $user_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if($this->model_group->leave_group($group_id,$user_id))
		{
			print_success();
		}
	}
	
	public function remove_group_member()
	{
		$group_id = $this->input->post('group_id');
		$user_ids = $this->input->post('user_ids');
		
		if (!$group_id || ! $user_ids)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$user_ids = explode(',',$user_ids);
		if($this->model_group->remove_group_member($group_id,$user_ids))
		{
			print_success();
		}
	}
	
	public function tutup_buku()
	{
		$group_id = $this->input->post('group_id');
		$punishment = $this->input->post('punishment');
		
		if (!$group_id || !$punishment)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		if ($this->model_group->tutup_buku($group_id, $punishment))
		{
			print_success();
		}
	}
	
	public function get_group_users()
	{
		$group_id = $this->input->get('group_id');
		
		if (!$group_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$group_users = $this->model_group->get_group_users($group_id);
		if ($group_users)
		{
			print_response($group_users);
		}
	}
	
	public function get_group_events()
	{
		$group_id = $this->input->get('group_id');
		
		if (!$group_id)
		{
			print_error(INCOMPLETE_PARAMETER_ERROR);
			return;
		}

		$group_events = $this->model_group->get_group_events($group_id);
		if ($group_events)
		{
			print_response($group_events);
		}
	}
}

/* End of file group.php */
/* Location: ./application/controllers/group.php */