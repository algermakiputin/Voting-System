<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PollModel extends CI_Model {

	function getPoll ( $id ) {

		$query = $this->db->get_where('poll', array('id' => $id));
		return $query->result();

	}

	function getPollGroup ( $poll_id ) {

		$query = $this->db->get_where('poll_group_items', array('poll_id' => $poll_id));

		return $query->result();

	}

	function updateImage($id, $file_name) {
		$data = array(
			'avatar' => $file_name
		);

		$this->db->where('id', $id);
		return $this->db->update('group_choices', $data);

	}


	function getGroupChoices ( $group_id ) {
	
		$query = $this->db->get_where('group_choices', array('group_id' => $group_id));

		return $query->result_array();

	}

	function getAll() {
		$query;

		$this->db->order_by('id', 'desc');
		
		if ($this->session->userdata('staff')) {

			$query = $this->db->get('poll');

		}else {

			$query = $this->db->get_where('poll',array(
				'publish' => 1,
				'end_time >' => date('Y m d')
			));

		}
		

		return $query->result();

	}

	function getAllPublishedPoll() {
		$this->db->order_by('id', 'desc');
		$query = $this->db->get_where('poll',array(
			'publish' => 1,
			'end_time >' => date('Y m d')
		));

		return $query->result();
	}

	function publishPoll($id) {
		$data = array(
			'publish' => 1
		);

		$this->db->where('id', $id);
		return $this->db->update('poll', $data);
	}

	function lastInsertID() {
		return $this->db->insert_id();
	}

	function insertPoll($poll_name, $tag_line, $start, $end, $private){
		$data = array (
			'name' => $poll_name,
			'start_time' => $start,
			'end_time' => $end,
			'tag_line' => $tag_line,
			'publish' => 0,
			'private' => $private
		);

		if ($this->db->insert('poll', $data)) {
			return $this->lastInsertID();
		}

	}

	function insertGroups ($groups, $poll_id) {
		$group_id = array();
		foreach ($groups as $group) {
			$data = array (
				'name' => $group,
				'poll_id' => $poll_id
			);
			if ($this->db->insert('poll_group_items', $data))
				array_push($group_id, $this->lastInsertID());

		} 

		return $group_id;

		

	}

	public function insertGroupChoices($choices, $group_id, $poll_id) {

		$counter = 0;
		foreach ($choices as $choice) {

			foreach( $choice as $ch) {
				$data = array (
					'name' => $ch,
					'group_id' => $group_id[$counter],
					'poll_id' => $poll_id
				);

				$this->db->insert('group_choices', $data);

				
			}

			$counter++;

		}

	}

	function deletePoll($id) {
		$this->db->where(array('id' => $id));
		$this->db->delete('poll');
		return $this->db->affected_rows();
	}

	function deleteGroup ($id) {
		$this->db->where(array('poll_id' => $id));
		$this->db->delete('poll_group_items');
		return $this->db->affected_rows();
	}

	function deleteChoice ($id) {
		$this->db->where(array('poll_id' => $id));
		$this->db->delete('group_choices');
		return $this->db->affected_rows();
	}

}

?>