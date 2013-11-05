<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class model_event extends CI_Model {
    function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }

	public function create_event($event_name, $group_id, $creator_user_id, $location_name, $latitude, $longitude, $event_time, $cancel_time)
	{
		$this->db->trans_begin();

		// Check if event exist
		$data = array(
		   'event_name' => $event_name,
		   'group_id' => $group_id
		);
			
		$query = $this->db->get_where('event', $data);
		
		if ($this->db->trans_status() === false)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else if ($query->num_rows() > 0)
		{
			$this->db->trans_rollback();
			print_error(EVENT_ALREADY_EXIST_ERROR);
			return false;
		}
		
		// Insert Data
		$data = array(
		   'event_name' => $event_name ,
		   'group_id' => $group_id ,
		   'creator_user_id' => $creator_user_id ,
		   'location_name' => $location_name,
		   'latitude' => $latitude ,
		   'longitude' => $longitude ,
		   'event_time' => $event_time ,
		   'cancel_time' => $cancel_time 
		);
		
		$this->db->insert('event', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else
		{		
			$data = array(
			   'event_name' => $event_name ,
			   'group_id' => $group_id ,
			   'creator_user_id' => $creator_user_id ,
			   'location_name' => $location_name,
			   'latitude' => $latitude ,
			   'longitude' => $longitude ,
			   'event_time' => $event_time ,
			   'cancel_time' => $cancel_time 
			);
		
			$this->db->from('event');
			$this->db->where($data);
			$query = $this->db->get();
			
			$event = $query->first_row();
			$event_id = $event->event_id;
			
			$data = array(
			   'event_id' => $event_id,
			   'user_id' => $creator_user_id,
			   'group_id' => $group_id,
			   'status' => WILL_ATTEND
			);
			
			$this->db->insert('user_event', $data); 
		
			if (!$this->db->trans_status())
			{
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				return false;
			}
			else
			{
				$this->db->trans_commit();
				return $event;
			}
		}
	}

	public function edit_event($event_id, $event_name, $location_name, $latitude, $longitude, $event_time, $cancel_time)
	{
		$this->db->trans_begin();
		
		$data = array(
			'event_id' => $event_id
		);

		$update_data = array(
		   'event_name' => $event_name ,
		   'location_name' => $location_name,
		   'latitude' => $latitude ,
		   'longitude' => $longitude ,
		   'event_time' => $event_time ,
		   'cancel_time' => $cancel_time 
		);
		
		$this->db->where($data); 
		$this->db->update('event', $update_data); 
		
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
	
	public function remove_event($event_id)
	{
		$this->db->trans_begin();

		$data = array(
		   'event_id' => $event_id ,
		);
		
		$this->db->delete('event', $data); 
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		
		$this->db->delete('user_event', $data); 
		
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
	
	public function add_attendee($event_id, $user_ids, $group_id)
	{
		$this->db->trans_begin();
		
		$data = array();
		for ($i = 0; $i < count($user_ids); $i++)
		{
			$data[$i] = array (
			   'event_id' => $event_id,
			   'user_id' => $user_ids[$i],
			   'group_id' => $group_id,
			   'status' => WILL_ATTEND
		   );
		}
			
		$this->db->insert_batch('user_event', $data); 
		
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
	
	public function remove_attendee($event_id, $user_ids)
	{
		$this->db->trans_begin();
		
		$data = array(
		   'event_id' => $event_id
		);
			
		$this->db->where($data); 
		$this->db->where_in('user_id', $user_ids); 
		$this->db->delete('user_event'); 
		
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
	
	public function close_event($event_id)
	{
		$this->db->trans_begin();
		
		// Get Event
		$data = array(
		   'event_id' => $event_id
		);
			
		$query = $this->db->get_where('event', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}

		if ($query->num_rows() == 0)
		{
			$this->db->trans_rollback();
			print_error(NO_RESULT_ERROR);
			return false;
		}
		
		$event = $query->first_row();

		// Check If Event Closed
		if ($event->is_closed)
		{
			$this->db->trans_rollback();
			print_error(EVENT_CLOSED_ERROR);
			return false;
		}
		
		// Update User Event
		$data = array(
		   'event_id' => $event_id,
		   'status' => WILL_ATTEND
		);
		
		$update_data = array(
			'status' => NOT_COME
		);

		$this->db->where($data);
		$this->db->update('user_event', $update_data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else
		{			
			// Rekap ke User Group
			$data = array(
			   'event_id' => $event_id
			);
				
			$query = $this->db->get_where('user_event', $data); 
			
			if (!$this->db->trans_status())
			{
				$this->db->trans_rollback();
				print_error(DATABASE_ERROR);
				return false;
			}

			$this->db->trans_commit();
			if ($query->num_rows() == 0)
			{
				$this->db->trans_rollback();
				print_error(NO_RESULT_ERROR);
				return false;
			}
			
			$i = 0;
			foreach ($query->result_array() as $row)
			{
				$event_attendee[$i] = $row;
				$i++;
			}
			
			$count = count($event_attendee);
			for ($i = 0; $i < $count; $i++)
			{
				$user_event = $event_attendee[$i];
				switch ($user_event['status'])
				{
					case ATTEND : $column = 'number_of_on_time'; break;
					case ABSENT : $column = 'number_of_absent'; break;
					case LATE : $column = 'number_of_late'; break;
					case NOT_COME : $column = 'number_of_miss_schedule'; break;
				}
				
				$data = array(
				   'user_id' => $user_event['user_id'],
				   'group_id' => $user_event['group_id']
				);

				$this->db->set($column, $column.'+1', false);
				$this->db->where($data);
				$this->db->update('user_group');
				
				if (!$this->db->trans_status())
				{
					$this->db->trans_rollback();
					print_error(DATABASE_ERROR);
					return false;
				}
			}
			
			$data = array(
			   'event_id' => $event_id
			);

			$update_data = array(
			   'is_closed' => 1
			);
			
			$this->db->where($data);
			$this->db->update('event', $update_data);
			
			$this->db->trans_commit();
			return true;
		}
	}
	
	public function check_in($user_id, $event_id, $latitude, $longitude)
	{
		$this->db->trans_begin();
				
		// Get Event
		$data = array(
		   'event_id' => $event_id
		);
			
		$query = $this->db->get_where('event', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}

		if ($query->num_rows() == 0)
		{
			$this->db->trans_rollback();
			print_error(NO_RESULT_ERROR);
			return false;
		}
		
		$event = $query->first_row();
		
		// Check If Event Closed
		if ($event->is_closed)
		{
			$this->db->trans_rollback();
			print_error(EVENT_CLOSED_ERROR);
			return false;
		}
		
		// Check Location
		require_once 'autoload.php';		
		
		$geotools = new \League\Geotools\Geotools();
		$coordA   = new \League\Geotools\Coordinate\Coordinate(array($latitude, $longitude));
		$coordB   = new \League\Geotools\Coordinate\Coordinate(array($event->latitude, $event->longitude));
		$distance = $geotools->distance()->setFrom($coordA)->setTo($coordB)->flat();

		if ($distance > VALID_MINIMUM_CHECKIN_DISTANCE)
		{
			$this->db->trans_rollback();
			print_error(INVALID_LOCATION_ERROR);
			return false;
		}
		
		date_default_timezone_set('Asia/Jakarta');
		$current_time = date("Y-m-d h:i:s");
		$status = ($current_time <= $event->event_time) ? ATTEND : LATE;
		
		$data = array(
		   'event_id' => $event_id,
		   'user_id' => $user_id
		);
		
		$update_data = array(
			'status' => $status
		);
			
		$this->db->where($data);
		$this->db->update('user_event', $update_data); 
		
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
	
	public function absent($user_id, $event_id)
	{
		$this->db->trans_begin();
		
		// Get Event		
		$data = array(
		   'event_id' => $event_id
		);
			
		$query = $this->db->get_where('event', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}

		if ($query->num_rows() == 0)
		{
			$this->db->trans_rollback();
			print_error(NO_RESULT_ERROR);
			return false;
		}
		
		$event = $query->first_row();
		
		// Check Time
		date_default_timezone_set('Asia/Jakarta');
		$current_time = date("Y-m-d h:i:s");
		if ($event->cancel_time < $current_time)
		{
			$this->db->trans_rollback();
			print_error(INVALID_CANCEL_TIME_ERROR);
			return false;
		}
		
		$data = array(
		   'event_id' => $event_id,
		   'user_id' => $user_id
		);
		
		$update_data = array(
			'status' => ABSENT
		);
			
		$this->db->where($data);
		$this->db->update('user_event', $update_data); 
		
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
		
	public function get_event($event_id)	
	{
		$this->db->trans_begin();
		
		$data = array(
		   'event_id' => $event_id
		);
			
		$query = $this->db->get_where('event', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else
		{
			if ($query->num_rows() == 0)
			{
				$this->db->trans_rollback();
				print_error(NO_RESULT_ERROR);
				return false;
			}
			else
			{
				$this->db->trans_commit();
				return ($query->first_row());
			}
		}
	}
	
	public function get_event_attendee($event_id)
	{
		$this->db->trans_begin();
		
		$data = array(
		   'event_id' => $event_id
		);
			
		$query = $this->db->get_where('user_event', $data); 
		
		if (!$this->db->trans_status())
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return false;
		}
		else
		{
			$this->db->trans_commit();
			if ($query->num_rows() > 0)
			{			
				$i = 0;
				foreach ($query->result_array() as $row)
				{
					$attendee[$i] = $row;
					$i++;
				}
				return $attendee;
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

/* End of file model_event.php */
/* Location: ./application/model/model_event.php */