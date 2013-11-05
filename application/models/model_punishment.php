<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class model_punishment extends CI_Model {

    function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }
	
	public function get_punishment($user_id, $group_id, $punishment)
	{
		$this->db->trans_begin();

		$data = array(
		   'user_id' => $user_id ,
		   'group_id' => $group_id,
		   'description' => $punishment
		);
		
		$this->db->from('punishment');
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
				$i = 0;
				foreach ($query->result_array() as $row)
				{
					$punishments[$i] = $row;
					$i++;
				}
				return $punishments;
			}
			else
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
		}
	}
	
	public function get_punishment_by_id($punishment_id)
	{
		$this->db->trans_begin();

		$data = array(
		   'punishment_id' => $punishment_id
		);
		
		$this->db->from('punishment');
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
				return $query->first_row();
			}
			else
			{
				print_error(NO_RESULT_ERROR);
				return null;
			}
		}
	}
	
	public function delete_punishment ($punishment_id)
	{				
		$this->db->trans_begin();
		
		// Delete punishment
		$data = array(
		   'punishment_id' => $punishment_id ,
		);
		
		$this->db->where($data);
		$this->db->delete('punishment');
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		else
		{	
			$this->db->trans_commit();
			return true;
		}
	}
}

/* End of file model_punishment.php */
/* Location: ./application/model/model_punishment.php */