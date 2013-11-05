<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class model_vote extends CI_Model {

    function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }
	
	public function vote_user($group_id, $user_id, $punishment_id, $status)
	{
		$this->db->trans_begin();

		$where_data = array(
			'group_id' => $group_id,
			'user_id' => $user_id,
			'punishment_id' => $punishment_id
		);
		
		$data = array(
		   'status' => $status
		);
		
		$this->db->where($where_data);
		$this->db->update('vote',$data);
	
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
		
		$where_data = array(
			'punishment_id' => $punishment_id
		);
		
		$this->db->select('vote_id,status');
		$this->db->where($where_data);
		$this->db->from('vote');
		$query = $this->db->get();
		
		// Check vote count
		if ($query->num_rows() > 0)
		{
			$i = 0;
			$total = 0;
			$count = 0;
			foreach ($query->result_array() as $row)
			{
				$total++;
				if ($row['status'] == ACOMPLISHED)
				{
					$count++;
				}
			}			
			
			if ($count * 2 > $total)
			{
				$this->db->trans_commit();
				return true;					
			}
			else
			{
				$this->db->trans_commit();
				print_success();
				return false;
			}
		}
		else
		{
			$this->db->trans_rollback();
			print_error(DATABASE_ERROR);
			return null;
		}
	}
}

/* End of file model_vote.php */
/* Location: ./application/model/model_vote.php */