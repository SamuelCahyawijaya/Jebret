<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class model_group extends CI_Model {

    function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }
	
	public function create_group($creator_user_id, $group_name, $group_description, $group_photo)
	{
		$this->db->trans_begin();
		
		// Check If Group Exist
		$data = array(
			'creator_user_id' => $creator_user_id, 
			'group_name' => $group_name
		);

		$this->db->where($data);
		$this->db->from('group');
		$query = $this->db->get();
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			
			unlink(BASEPATH.'/image/'.$group_photo);
						
			return null;
		}
		
		$group = $query->first_row();
		if (count($group) > 0)
		{
			$this->db->trans_rollback();
			print_error(GROUP_ALREADY_EXIST_ERROR);

			unlink(BASEPATH.'/image/'.$group_photo);
						
			return null;
		}
		
		$data = array(
		   'creator_user_id' => $creator_user_id,
		   'group_name' => $group_name,
		   'group_description' =>  $group_description,
		   'group_photo' => $group_photo
		);
		
		$this->db->insert('group', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);

			unlink(BASEPATH.'/image/'.$group_photo);
						
			return false;
		}
		else
		{		
			$this->db->from('group');
			$this->db->where($data);
			$query = $this->db->get();
			
			$group = $query->first_row();
			$group_id = $group->group_id;
			
			$data = array(
				'group_id' => $group_id,
				'user_id' => $creator_user_id ,
				'number_of_late' => 0,
				'number_of_miss_schedule' => 0,
				'number_of_on_time' => 0,
				'number_of_absent' => 0,
				'status' => NOT_PUNISHED
			);
			
			$this->db->insert('user_group', $data); 
		
			if (!$this->db->trans_status())
			{
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				
				unlink(BASEPATH.'/image/'.$group_photo);
						
				return false;
			}
			else
			{
				$this->db->trans_commit();
				return $group;
			}
		}		
	}
	
	public function edit_group($group_id, $group_name, $group_description)
	{
		$this->db->trans_begin();

		// Update Group
		$data = array (
			'group_id' => $group_id
		);
		
		$update_data = array(
		   'group_name' => $group_name,
		   'group_description' =>  $group_description
		);
		
		$this->db->where($data);
		$this->db->update('group', $update_data); 
		
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
	
	public function remove_group($group_ids)
	{
		$this->db->trans_begin();

		$this->db->where_in('group_id',$group_ids);
		$query = $this->db->get('group'); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		$i = 0;
		foreach ($query->result() as $row)
		{
			$group_photos[$i] = $row->group_photo;
			$i++;
		}
		
		// Remove From Log
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('log'); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove From Vote
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('vote'); 		
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove Group Punishment
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('punishment'); 		
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove User Event
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('user_event'); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove Group Event
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('event');
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove User Group
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('user_group');
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove Group
		$this->db->where_in('group_id',$group_ids);
		$this->db->delete('group');
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Delete Group Image
		$count = count($group_photos);
		for ($i = 0; $i < $count; $i++)
		{
			unlink(BASEPATH.'/image/'.$group_photos[$i]);
			$i++;
		}
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		$this->db->trans_commit();
		return true;
	}
	
	public function add_group_member($group_id, $user_ids)
	{
		$this->db->trans_begin();
		
		$data = array();
		for ($i = 0; $i < count($user_ids); $i++)
		{
			$data[$i] = array (
			   'group_id' => $group_id,
			   'user_id' => $user_ids[$i],
			   'status' => NOT_PUNISHED
		   );
		}
			
		$this->db->insert_batch('user_group', $data); 
		
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
	
	public function leave_group($group_id, $user_id)
	{
		$this->db->trans_begin();
		
		$data = array(
		   'group_id' => $group_id,
		   'user_id' => $user_id
		);
		
		// Remove From Log
		$this->db->where($data); 
		$this->db->delete('log'); 		
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove From Vote
		$this->db->where($data); 
		$this->db->delete('vote'); 		
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove From Punishment
		$this->db->where($data); 
		$this->db->delete('punishment'); 		
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove From User Event
		$this->db->where($data); 
		$this->db->delete('user_event'); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}

		// Remove From User Group
		$this->db->where($data); 
		$this->db->delete('user_group'); 
		
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
	
	public function remove_group_member($group_id, $user_ids)
	{
		$this->db->trans_begin();
		
		$data = array(
		   'group_id' => $group_id
		);
			
		// Remove From Punishment
		$this->db->where($data); 
		$this->db->where_in('user_id', $user_ids); 
		$this->db->delete('punishment'); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove From Event
		$this->db->where($data); 
		$this->db->where_in('user_id', $user_ids); 
		$this->db->delete('user_event'); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		// Remove From Group
		$this->db->where($data); 
		$this->db->where_in('user_id', $user_ids); 
		$this->db->delete('user_group'); 
		
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
	
	public function tutup_buku($group_id, $punishment)
	{
		$this->db->trans_begin();

		// Get Group Info
		$this->db->from('group');
		$this->db->where_in('group_id', $group_id);
		$query = $this->db->get();
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		
		$group = ($query->first_row());
		
		// Get Group User
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

		$i = 0;
		foreach ($query->result() as $row)
		{
			$user_ids[$i] = $row->user_id;
			$group_users[$i] = $row;
			$i++;
		}
		
		// Tutup Buku
		$count = count($group_users);
		$j = 0;
		$k = 0;
		for ($i = 0; $i < $count; $i++)
		{
			$group_user = $group_users[$i];
			
			$total_event = ($group_user->number_of_late) + ($group_user->number_of_miss_schedule) + ($group_user->number_of_on_time) + ($group_user->number_of_absent);
			if ($total_event == 0)
			{
				$user_point = 0;
			}
			else
			{
				$user_point = ((($group_user->number_of_late * -0.5) + ($group_user->number_of_miss_schedule * 1) + ($group_user->number_of_on_time * 1) + ($group_user->number_of_absent * -0.25))) / $total_event; 
			}
			
			if ($user_point < 0)
			{
				// Insert To Punishment
				$punishment_data[$j] = array(
					'group_id' => $group_id,
					'user_id' => $group_user->user_id,
					'user_point' => $user_point,
					'description' => $punishment,
				);				
				$j++;
		
				$status = PUNISHED;
			}
			else
			{
				$status = NOT_PUNISHED;
			}
			
			// Update UserGroup
			$update_data = array(
				'number_of_late' => 0,
				'number_of_miss_schedule' => 0,
				'number_of_on_time' => 0,
				'number_of_absent' => 0,
				'status' => $status
			);
				
			$data = array (
				'group_id' => $group_id,
				'user_id' => $group_user->user_id,
			);
			
			$this->db->where($data);
			$this->db->update('user_group',$update_data);
			
			if (!$this->db->trans_status())
			{			
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				return false;
			}
		}
		
		if (isset($punishment_data))
		{
			// Insert Punishment Data
			$this->db->insert_batch('punishment',$punishment_data);
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				return false;
			}
			
			// Get Punishment
			$count = count($punishment_data);		
			for ($i = 0; $i < $count; $i++)
			{
				$user_ids[$i] = $punishment_data[$i]['user_id'];
				$group_ids[$i] = $punishment_data[$i]['group_id'];
			}
			
			$this->db->from('punishment');
			$this->db->where_in('group_id', $group_ids);
			$this->db->where_in('user_id', $user_ids);
			$this->db->where('description', $punishment);
			$query = $this->db->get();
		
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				return null;
			}

			// Create Vote Data
			$i = 0;
			$user_count = count($group_users);
			echo ($user_count);
			foreach ($query->result() as $row)
			{
				for ($j = 0; $j < $user_count; $j++)
				{
					if ($group_users[$j]->user_id != $row->user_id)
					{
						// Insert To Vote
						$vote_data[$i] = array (
							'user_id' => $group_users[$j]->user_id,
							'group_id' => $row->group_id,
							'punishment_id' => $row->punishment_id,
							'status' => NOT_ACOMPLISHED
						);
						$i++;
					}
				}
			}
			
			if (isset($vote_data))
			{
				$this->db->insert_batch('vote',$vote_data);
				if ($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					print_error(DATABASE_ERROR);
					return false;
				}
			}
		}
		
		$this->db->trans_commit();
		return true;
	}
			
	public function get_group_users($group_id)
	{
		$this->db->trans_begin();

		$data = array(
			'group_id' => $group_id
		);
		
		$query = $this->db->get_where('user_group', $data); 
		
		if ($query->num_rows() == 0)
		{
			print_error(NO_RESULT_ERROR);
			return null;
		}
		else
		{
			$i = 0;
			foreach ($query->result() as $row)
			{
				$user_ids[$i] = $row->user_id;
				$i++;
			}
			$this->db->where_in('user_id', $user_ids);
			$this->db->from('user');
			$query = $this->db->get();
			
			if (!$this->db->trans_status())
			{			
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				return false;
			}
			else
			{
				$i = 0;
				$this->db->trans_commit();
				foreach ($query->result() as $row)
				{
					$group_users[$i] = $row;
					$i++;
				}
				return ($group_users);			
			}
		}
	}
	
	public function get_group_events($group_id)
	{
		$this->db->trans_begin();

		$data = array(
			'group_id' => $group_id
		);
		
		$query = $this->db->get_where('event', $data); 
		if ($query->num_rows() == 0)
		{
			print_error(NO_RESULT_ERROR);
			return null;
		}
		else
		{
			$this->db->trans_commit();
			$i = 0;
			foreach ($query->result() as $row)
			{
				$group_events[$i] = $row;
				$i++;
			}
			return ($group_events);			
		}
	}
	
	public function get_group_info($group_id)
	{
		$this->db->trans_begin();
		
		$this->db->from('group');
		$this->db->where_in('group_id', $group_id);
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
			return ($query->first_row());
		}
	}
}

/* End of file model_group.php */
/* Location: ./application/model/model_group.php */