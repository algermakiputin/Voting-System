<?php

class UsersModel extends CI_Model {

	function search_user($query) {
		$this->db->select('*');
		$this->db->from('users');
		$this->db->like('name', $query, "BOTH");

		return $this->db->get()->result_array();
	}

	public function save() {

		$this->load->library('encryption');

		$data = array(
			'name' => $this->input->post('name'),
			'username' => strtolower($this->input->post('username')),
			'password' => strtolower($this->input->post('password')),
			'grade' => $this->input->post('grade'),
			'section' => $this->input->post('section'),
			'voted' => 0
		);

		if ( $this->db->insert('users', $data)) 
			return true;
		return false;
	}

	function deleteAllUsers() {
		return $this->db->empty_table('users');
	}

	function pagination($offset) {
		$query = $this->db->get('users',10,$offset);
		return $query->result_array();
	}

	function resetAllVotes() {
		$data = array(
			'voted' => 0
		);
		$this->db->update('users', $data);
	}

	function edit_user($id, $data) {

		 $this->db->update('users', $data, array('id' => $id));
		var_dump($data);
	}

	public function login() {
		
		$this->load->library('encryption');

		$query = $this->db->get_where('users',array(
			'username' => strtolower($this->input->post('username'))
		));

		if ($query->num_rows() ) {
			
			$hash_pwd = $query->row()->password;

			if ($hash_pwd == strtolower( $this->input->post('password') ) ) 

				return $data = array (
					'id' => $query->row()->id,
					'username' => $query->row()->username,
					'name' => $query->row()->name
				);
				
	
		}
		return false;
	}

	public function staff_login() {
		
		$query = $this->db->get_where('staff', array(
			'username' => $this->input->post('username'),
			'password' => $this->input->post('password')
		));

		if ($query->num_rows() == 1) {

			return ($query->result());

		}
	}


	function getAllUsers() {

		$this->db->order_by("name", "asc"); 
		$query = $this->db->get('users',10, 0);
		return $query->result_array();

	}

	function delete_user($id) {
		return $this->db->delete('users', array('id' => $id));
	}

	function filterUsersBySection($section) {
		$this->db->order_by("name", "asc"); 

		$query = $this->db->get_where('users', array(
			'section' => $section
		));

		if ($section == "novote") {
			
			$this->db->order_by("name", "asc"); 
			$query = $this->db->get_where('users', array(
				'voted' => 0
			));
		}

		return $query->result_array();
	}

	function importCV($data) {
	
		$this->db->insert_batch('users',$data);
	
	}

	function num_rows() {
		return $this->db->get('users')->num_rows();
	}



}

?>