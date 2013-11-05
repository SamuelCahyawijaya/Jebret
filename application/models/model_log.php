<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class model_log extends CI_Model {

    function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }
	
	public function add_log($user_id, $group_id, $event_id, $action)
	{
		$this->db->trans_begin();

		$data = array(
		   'user_id' => $user_id ,
		   'group_id' => $group_id ,
		   'event_id' => $event_id ,
		   'action' => $action
		);
		$this->db->insert('log', $data); 
	
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
	
	public function get_log($user_id, $group_id)
	{
		$this->db->trans_begin();

		$data = array(
		   'user_id' => $user_id ,
		   'group_id' => $group_id ,
		);
		
		$this->db->select(Array('user_id','group_id','event_id','action'));
		$this->db->from('log');
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
			{							$i = 0;
				foreach ($query->result_array() as $row)
				{
					$logs[$i] = $row;
					$i++;
				}
				return $logs;
			}
			else
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
		}
	}
}

/* End of file model_log.php */
/* Location: ./application/model/model_log.php */