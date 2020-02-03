<?php

class VotesModel extends CI_Model {

	public function save($data) {

		$this->db->insert('choice_votes', $data);
	}

	public function saveUserVote( $data ) {

		$this->db->insert('votes', $data);

	}

	public function updateUsersVote($id) {
		$data =array(
			'voted' => 1
		);

		$this->db->where('id', $id);
		$this->db->update('users',$data);
	}

	public function checkUserVote($user_id) {

		$this->db->select('poll_id');
		$this->db->where(array('user_id' => $user_id));
		$this->db->group_by('poll_id');
		
		return $this->db->get('votes')->result_array();

	}


	public function countVote( $id ) {
		
		$query = $this->db->get_where('votes', array('poll_id' => $id));

		return $query->num_rows();
	}

	function countChoiceVotes( $id ) {
		$query = $this->db->get_where('choice_votes',array('choice_id' => $id));
		return $query->num_rows();
	}

	public function countAllPollVotes( $id ) {
		
		$query = $this->db->get_where('votes', array('poll_id' => $id));

		return $query->result_array();
	}

	function getUserNames($id) {
		$this->db->select('name');
		$this->db->where(array('id' => $id));

		return $this->db->get('users')->result();
	}

	


}


?>