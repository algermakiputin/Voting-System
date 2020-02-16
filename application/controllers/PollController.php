<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PollController extends CI_Controller {

	public function view ($num) {

		$group_data = array();
		$choices_name = array();
		$output = "";

		$election = $this->db->where('id', $num)->get('poll')->row();
		$positions = $this->db->where('poll_id', $election->id)->get('poll_group_items')->result();

		foreach ($positions as $position) {

			$candidates = $this->db->select('group_choices.*')
											->from('group_choices') 
											->where('group_choices.group_id', $position->id)
											->get()
											->result();
 
			$position->candidates = $candidates;
		}

		$data['election'] = $election;
		$data['positions'] = $positions;

		echo $this->load->view('poll/vote_election', $data, true);
	}
 
	function status ($num) {

		$this->load->model('PollModel');
		$this->load->model('VotesModel');

		$data = [];
		$election = $this->db->where('id', $num)->get('poll')->row();
		$positions = $this->db->where('poll_id', $election->id)->get('poll_group_items')->result();
		$allowed_users = $this->db->select('private_allowed_users.user_id, users.name, users.username')
											->from('private_allowed_users')
											->join('users', 'users.id = private_allowed_users.user_id')
											->where('private_allowed_users.poll_id', $election->id)
											->get()
											->result();

 
		$users = [];

		if ($election->private) {

			$query = "SELECT * FROM users
							WHERE (users.id) NOT IN 
							( 
								SELECT user_id 
								FROM private_allowed_users
								WHERE poll_id = '$election->id'
							)


						";

			$users = $this->db->query($query)->result();


		}
		
		foreach ($positions as $position) {

			$candidates = $this->db->select('group_choices.*')
											->from('group_choices') 
											->where('group_choices.group_id', $position->id)
											->get()
											->result();

			foreach ($candidates as $candidate) {

				$candidate->votes = $this->db->where('choice_votes.choice_id', $candidate->id)
														->get('choice_votes')
														->num_rows();
			}
 
			$position->candidates = $candidates;
		}
		  

		$data['users'] = $users;
		$data['election'] = $election;
		$data['election_status'] = strtotime($election->start_time) > strtotime($election->end_time) ? "Ended" : "Ongoing";
		$data['votes'] = $this->VotesModel->countVote($election->id);
		$data['positions'] = $positions;
		$data['allowed_users'] = $allowed_users;

		echo $this->load->view('poll/single_poll_view', $data, true);
		
	}

	public function insert_private_user() {

		$id = $this->input->post('id');
		$poll_id = $this->input->post('poll_id');

		$this->db->insert('private_allowed_users', [
				'user_id' => $id,
				'poll_id' => $poll_id
			]);
	}

	function sortByVote($a, $b)
	{
	    $a = $a['weight'];
	    $b = $b['weight'];

	    if ($a == $b) return 0;
	    return ($a < $b) ? -1 : 1;
	}

	public function do_upload()
    {
    	$file = explode('.', $_FILES['image_file']['name']);
    	$image_name = $file[0];
    	$constantImageName = $file[0];
    	$image_extension = $file[1];
    	$newName = "";

    	$path  = $_SERVER['DOCUMENT_ROOT'] . '/' . 'poll/uploads/' . $image_name . "." . $image_extension;
		$counter = 1;
		while(file_exists($path)) {
			$image_name = $constantImageName . $counter; 
			$path  = $_SERVER['DOCUMENT_ROOT'] . '/' . 'poll/uploads/' . $image_name . "." . $image_extension;
			$counter++;
		}

		$newName = $image_name . "." . $image_extension;
		$choice_id = $_POST['id'];

		$config['upload_path']          = './uploads/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name'] = $newName;
       	$this->load->library('upload', $config);
   
    	if (isset($_FILES['image_file'])) {

    		

           	if ( ! $this->upload->do_upload('image_file'))
            {
                    
            }
            else
            {
            	
            	$data = $this->upload->data();
            	$this->load->model('PollModel');
            	$this->PollModel->updateImage($choice_id, $newName);
            	echo $newName;
 			
            }
    	}
    }



	public function newPoll() {
	 
		$this->load->model('PollModel');
		$poll_name = $this->input->post('poll_name');
		$tag_line = $this->input->post('tag_line');
		$groups = $this->input->post('groups');
		$choices = $this->input->post('choices');
		$start_time = $this->input->post('start_time');
		$end_time = $this->input->post('end_time');
		$private = $this->input->post('private');
		$currentGroupID = array();
 


		$currentID = $this->PollModel->insertPoll($poll_name, $tag_line, $start_time, $end_time, $private);

	 
		$currentGroupID = $this->PollModel->insertGroups($groups, $currentID);
		 

		
		$this->PollModel->insertGroupChoices($choices, $currentGroupID,$currentID);
		
	}

	public function deletePoll() {
		$this->load->model('PollModel');
	
		$id = $this->input->post('id'); 
		
		$this->PollModel->deletePoll($id);
	
		$this->PollModel->deleteGroup($id);

		$this->PollModel->deleteChoice($id);
					 
	
	}
 

	function publish () {
		$id = $this->input->post('id');
		$this->load->model('PollModel');
		if ($this->PollModel->publishPoll($id)) {
			echo "ok";
		}

	}
 
	
}

?>