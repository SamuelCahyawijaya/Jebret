<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class model_user extends CI_Model {

    function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }
	
	public function login($user_name, $password)
	{
		$this->db->trans_begin();

		$data = array(
		   'user_name' => $user_name  ,
		   'password' => md5($password) ,
		);
		
		$this->db->select(Array('user_id','user_name','user_photo','fb_id','email','phone_number', 'fb_token', 'fb_token_valid_time'));
		$this->db->from('user');
		$this->db->where($data);
		$query = $this->db->get();
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			$this->db->trans_commit();
			if ($query->num_rows() > 0)
			{
				$user = $query->first_row();
				if ($user->fb_token_valid_time - FACEBOOK_VALID_TIME_MARGIN <= time())
				{
					$this->db->trans_rollback();
					print_error(RENEW_TOKEN_NEEDED_ERROR);
					return null;
				}
				return ($user);
			}
			else
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
		}
	}
	
	public function renew_token_login($user_name, $password, $fb_token, $fb_token_valid_time)
	{
		$this->db->trans_begin();

		$data = array(
		   'user_name' => $user_name  ,
		   'password' => md5($password) ,
		);
		
		$update_data = array(
		   'fb_token' => $fb_token,
		   'fb_token_valid_time' => time() + $fb_token_valid_time - TIME_MARGIN
		);
		
		$this->db->where($data);
		$this->db->update('user', $update_data);
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		
		$this->db->select(Array('user_id','user_name','user_photo','fb_id','email','phone_number'));
		$this->db->from('user');
		$this->db->where($data);
		$query = $this->db->get();
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			$this->db->trans_commit();
			if ($query->num_rows() > 0)
			{			
				return ($query->first_row());
			}
			else
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
		}
	}
	
	public function signup($user_name, $user_photo, $password, $fb_id, $email, $phone_number, $fb_token, $fb_token_valid_time)
	{
		$this->db->trans_begin();

		// Check If User Exist
		$data = array(
			'user_name' => $user_name
		);

		$or_data = array(
			'fb_id' => $fb_id
		);
		
		$this->db->select('user_name');
		$this->db->from('user');
		$this->db->where($data);
		$this->db->or_where($or_data);
		$query = $this->db->get();
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else if ($query->num_rows() > 0)
		{
			$this->db->trans_rollback();
			print_error(USER_ALREADY_EXIST_ERROR);
			return false;
		}
		
		$data = array(
		   'user_name' => $user_name ,
		   'user_photo' => $user_photo ,
		   'password' => md5($password) ,
		   'fb_id' => $fb_id ,
		   'email' => $email ,
		   'phone_number' => $phone_number ,
		   'fb_token' => $fb_token,
		   'fb_token_valid_time' => time() + $fb_token_valid_time
		);
		$this->db->insert('user', $data); 
	
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
	}
		
	public function get_user_group($user_id)
	{
		$this->db->trans_begin();

		$data = array(
			'user_id' => $user_id
		);
		
		$this->db->select('group_id');
		$this->db->from('user_group');
		$this->db->where($data);
		$query = $this->db->get();
		if ($query->num_rows() == 0)
		{
			print_error(NO_RESULT_ERROR);
			return null;
		}
		
		$i = 0;
		foreach ($query->result_array() as $row)
		{
			$group_ids[$i] = $row['group_id'];
			$i++;
		}
		
		$this->db->from('group');
		$this->db->where_in('group_id', $group_ids);
		$query = $this->db->get();
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			$this->db->trans_commit();
			$i = 0;
			foreach ($query->result() as $row)
			{
				$group[$i] = $row;
				// $group[$i]->group_photo = SERVER_BASE_URL.'/system/image/'.$group[$i]->group_photo;
				$i++;
			}
			
			return $group;
		}
	}
		
	public function get_user_event($user_id)
	{
		$this->db->trans_begin();

		$data = array(
			'user_id' => $user_id
		);
		
		$this->db->select('event_id');
		$this->db->from('user_event');
		$this->db->where($data);
		$query = $this->db->get();
		if ($query->num_rows() == 0)
		{
			print_error(NO_RESULT_ERROR);
			return null;
		}
		
		$i = 0;
		foreach ($query->result_array() as $row)
		{
			$event_ids[$i] = $row['event_id'];
			$i++;
		}
		
		$this->db->from('event');
		$this->db->where_in('event_id', $event_ids);
		$query = $this->db->get();
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			$this->db->trans_commit();
			$i = 0;
			foreach ($query->result() as $row)
			{
				$event[$i] = $row;
				// $group[$i]->group_photo = SERVER_BASE_URL.'/system/image/'.$group[$i]->group_photo;
				$i++;
			}
			
			return $event;
		}
	}
		
	public function get_user_info($user_id)
	{
		$this->db->trans_begin();

		$data = array(
			'user_id' => $user_id
		);
		
		$this->db->select(Array('user_id','user_name','email','phone_number','user_photo'));
		$this->db->from('user');
		$this->db->where($data);
		$query = $this->db->get();
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			if ($query->num_rows() == 0)
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
		
			$this->db->trans_commit();
			return ($query->first_row());			
		}
	}
	
	public function search_user($group_id, $query)
	{
		$this->db->trans_begin();

		$data = array(
			'user_name' => $query
		);		
		
		$this->db->select(Array('user_id','user_name','email','phone_number','user_photo'));
		$this->db->from('user');
		$this->db->like($data);
		$query = $this->db->get();
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			if ($query->num_rows() == 0)
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
			
			$i = 0;
			foreach ($query->result() as $row)
			{
				$user_ids[$i] = $row->user_id;
				$query_users[$i] = $row;
				$i++;
			}
					
			// Get Group User Ids
			$data = array(
				'group_id' => $group_id
			);

			$query = $this->db->get_where('user_group', $data); 
			
			if ($query->num_rows() == 0)
			{
				$this->db->trans_rollback();
				print_error(NO_RESULT_ERROR);
				return null;
			}

			// Find user that not in group
			$i = 0;
			foreach ($query->result() as $row)
			{
				$group_users_ids[$i] = $row->user_id;
				$i++;
			}
			
			$count = count($user_ids);
			$j = 0;
			for ($i = 0; $i < $count; $i++)
			{
				if (!in_array($user_ids[$i],$group_users_ids))
				{
					$users[$j] = $query_users[$i];
					$j++;
				}
			}
			
			if (isset($users))
			{
				$this->db->trans_commit();
				return ($users);
			}
			else
			{
				$this->db->trans_rollback();
				print_error(NO_RESULT_ERROR);
				return null;
			}
		}
	}
}

/* End of file model_user.php */
/* Location: ./application/model/model_user.php */