<?php

class VotesController extends CI_Controller {

	function save () {

		$user_id = $this->input->post('user_id');
		$groups[] = $this->input->post('group_id');
		$choices[] = $this->input->post('choices_id');
		$poll_id = $this->input->post('poll_id');
		
		$this->load->model('VotesModel');
		
		for ($i = 0; $i < count($groups[0]); $i++) {

			$data = array(
				'user_id' => $user_id,
				'poll_id' => $poll_id,
				'group_id' => $groups[0][$i],
				'choice_id' => $choices[0][$i]
			);

			$this->VotesModel->save($data);

		}

		$this->VotesModel->saveUserVote(
			$data = array(
				'user_id' => $user_id,
				'poll_id' => $poll_id
			)
		);

		$this->VotesModel->updateUsersVote($user_id);

	}

	



}

?>