<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PollController extends CI_Controller {

	public function view ($num) {

		$group_data = array();
		$choices_name = array();
		$output = "";

		$this->load->model('PollModel');
		$baseUrl = base_url();
		$poll = $this->PollModel->getPoll($num);
		
		$poll_data = array(
			'name' => $poll[0]->name,
			'id' => $poll[0]->id,
			'tag_line' => $poll[0]->tag_line
		);

		$output = "<div class='poll'>";

		if ($this->session->userdata('staff')) {
			$output .= "<div class='prev'>";
			$output .= "<a class='previous btn btn'>&laquo; Back to homepage</a>";
			$output .= "</div>";
			$output .="<div class='poll-content'>";
			$output .= "<div class='poll-box'>";
			$output .= "<div class='poll-name '> $poll_data[name]</div>";
			$output .= "<div class=' poll-description'>$poll_data[tag_line]</div>";
			$output .= "</div>";
			$output .= "<form id='poll-form' method='POST'>";
		
		}else {
		
			$output .= "<h3 class='' style='padding:50px 15px'>$poll_data[name]</h3>";
		}
		
		$poll_group_items = $this->PollModel->getPollGroup($poll_data['id']);

		foreach ($poll_group_items as $group_items) {

			array_push($choices_name, $group_items->id);
			$output .= "<div class=' choice-title'><h3 > $group_items->name </h3></div>";
			$choices = $this->PollModel->getGroupChoices($group_items->id);
			$output .= "<div class='choices'>";
			
			foreach ($choices as $choice) {
				$output .= "
				<div class='row choice-row '>
					<div class='col-md-9 vertical-align  '>
						";

				if ($choice['avatar']) {
					$output .= "
						<div class='img-wrapper '>
							<img src='$baseUrl/uploads/$choice[avatar]'>
						</div>
					";
				}else {
					$output .= "
						<div class='img-wrapper '>
							<img src='$baseUrl/Assets/images/default.png'>
						</div>
					";

				}

				$output .=	"<span class='choice-name'>
							$choice[name]
						</span>
					</div>
					<div class='col-md-3 vertical-align text-center '>
						<div class='options'>
						<label for='$choice[id]' class=''> 
							<input type='radio' name='$group_items->id' value='$choice[id]' id='$choice[id]'> 
							<img />
						</label>
						</div>
					</div>
					
				</div>
				";
			}
			$output .= "</div>";
		}
		$output .= "<p class='text-center text' id='review'>* Please review your votes *</p>";
		$output .= "<div class='poll-action'> <input type='button' id='submit-poll' class='btn btn-primary form-control btn-lg' value='Submit Vote'> </div>";
		$output .= "</form>";
		$output .= "</div>";
		$output .= "</div>";
		
		echo $output;

		?>
		<script type="text/javascript">
			$("#submit-poll").click(function(e) {

				var radio_group = <?php echo json_encode($choices_name)  ?> ;
				var choices = [];
				var groups = [];
				for (i = 0; i < radio_group.length; i++ ) {
					groups.push(radio_group[i]);
					if (!$('input[name='+radio_group[i]+']').is(":checked")) {
						$('#modal').modal('show'); 
						$('.modal-header').addClass('bg-danger');
						$('.modal-body').text('You must fill up all the required fields');
						return false;
					}else {
						choices.push($('input[name='+radio_group[i]+']:checked').val())
					}

				}
		
				var id = <?php echo $this->session->userdata('id'); ?>
				
				if (radio_group.lenght === choices.lenght) {
					$("#submit-poll").prop('disabled', true);
					$.ajax({
				        type: "post",
				     	url : window.location.href + "vote/save",
				     	data : {
				     		'user_id' : <?php echo $this->session->userdata('id') ?>,
				     		'group_id' : groups,
				     		'choices_id' : choices,
				     		'poll_id' : <?php echo $num; ?>
				     	},
				     	success : function (data) {
				     		$('#main').innerHTML = "";
				     		$('#modal').modal('show'); 
							$('.modal-header').removeClass('bg-danger');
							$('.modal-header').addClass('bg-success');
							$('.modal-body').text('Your vote has been submitted');
							$("#submit-poll").prop('disabled', false);
				     		$("#main").innerHTML = "";
							$.ajax({
								type : "get",
								url : base_url + "reload",
								contentType: "application/json",
								success : function (data) {
									$(".main").innerHTML = "";
									
									$(".main").html(data);
									$(document).scrollTop(0);
									DeletePoll();
									
								}
							})
				     		
				     	}

					});

					
				}
			
			})
		</script>

		<?php
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