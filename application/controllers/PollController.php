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

		$alphabet = array('A','B','C','D','E','F','G','H','I','J','k','L','M','N','O',
			'P','Q','R','S','T','U','V','W','X','Y','Z');

		$group_data = array();
		$choices_name = array();
		$output = "";
		$usersVoted = array();
		$table = "";
		$this->load->model('PollModel');
		$this->load->model('VotesModel');
		if (!$this->session->userdata('staff')) {
			echo "<style>.tableexport-caption {display:none !important;}</style>";
		}
		$poll = $this->PollModel->getPoll($num);

		$output = "<div class='poll' id='status'>";

		$poll_data = array(
			'name' => $poll[0]->name,
			'id' => $poll[0]->id,
			'tag_line' => $poll[0]->tag_line,
			'start_time' => $poll[0]->start_time,
			'end_time' => $poll[0]->end_time,
			'publish' => $poll[0]->publish
		);
		$start_time =  date_format(date_create($poll_data['start_time']),'M d Y');
		$end_time = date_format(date_create($poll_data['end_time']), 'M d Y');
		$poll_group_items = $this->PollModel->getPollGroup($poll_data['id']);
		$votes = $this->VotesModel->countVote($poll_data['id']);

		$today = date("Y-m-d");
		$today_time = strtotime($today);
		$end_date = strtotime($end_time);
		$ongoing = false;
		$status_message = "Ongoing";

		$status = "";
 		if ($end_date > $today_time) {
			$status = "- Initial Voting Results";
			$ongoing = true;
			
		}else {
			$status = "- Final Result";
			$status_message = 'Ended';

		}
		
		$allVotes = $this->VotesModel->countAllPollVotes($poll_data['id']);


		
		foreach ($allVotes as $allvote) {
			$name = $this->VotesModel->getUserNames($allvote['user_id']);
			if ($name)
				array_push($usersVoted, $name[0]->name);
		}

		$baseUrl = base_url();

		$output = "<div class='poll' id='results-view'>";
		$output .= "<div class='prev'>";
		$output .= "<a class='previous btn btn'>&laquo; Back to homepage</a>";
		$output .= "</div>";
		$output .= "<div class='poll-content'>";
		$output .= "<div class='poll-box'>";
		$output .= "<div class='poll-name'> $poll_data[name] $status</div>";

		$output .= "<div class='poll-description'>$poll_data[tag_line] </div>";
			$output .= "<div class='votes'><span class=''>Status: $status_message</span></div> ";
		$output .= "<div class='votes'><span class=''>$votes people voted</span><br> ";
 

		$output .= "
		<div class='poll-time'>
			<span class=''>
				 From: $start_time
			</span> To <span class=''>
				  $end_time
			</span>
		</div>

		";
		$output .= "</div>";
		$output .= "</div>";
	
		$table .= "<table id='results-table' >";
		foreach ($poll_group_items as $group_items) {
			
			array_push($choices_name, $group_items->id);

			$output .= "<div class=' choice-title'><h3 class='mx-auto'> $group_items->name </h3></div>";
			$output .= "<div class='choices'>";

			
			$table .= "<tr>
				<th>$group_items->name</th>
			</tr>";
			
			$choices = $this->PollModel->getGroupChoices($group_items->id);
		
			$i = 0;
			foreach ($choices as $choice) {

				$choice_votes = $this->VotesModel->countChoiceVotes($choice['id']);
				array_push($choices[$i], $choice_votes);
				
				$i++;
			}

			usort($choices, function ($item1, $item2) {
			    return $item2['0'] <=> $item1['0'];
			});
			
			$total_votes = 0;
			foreach( $choices as $choice) {
				$total_votes += $choice[0];

			}

			$count = 0;
			foreach ($choices as $choice) {
				
			 
				$table .= "
					<tr>
						<td>$choice[name]</td>
						<td>$choice[0] Votes</td>
					</tr>
				
				";
				$col1 = 5;
				$col2 = 5;
				if (!$this->session->userdata('staff') && $ongoing == true) {
					$col1 = 3;
					$col2 = 7;
				} 
				$output .= "
				<div class='row choice-row '>

					<div class='col-md-$col1 vertical-align '>
						<div class='img-wrapper  vertical-align' >

							";

						if (!$this->session->userdata('staff') && $ongoing == true) {
							$output .= "<img id='avatar$choice[id]' src='https://qtxasset.com/styles/author_medium/s3fs/field/image/blank%20silhouette_21.png?gNGXuJyf7YYOaHgfhlBb6NUXFTTnx6t7&itok=0GJ7nzu_'>";
						}else {
							if ($choice['avatar']) {
							$output .= "<img id='avatar$choice[id]' src='$baseUrl/uploads/$choice[avatar]'>";
							}else {
								$output .= "<img id='avatar$choice[id]' src='$baseUrl/Assets/images/default.png'>";
							}
						}
						
				if ($choice && $total_votes) {
					$percentage = floor(($choice[0] / $total_votes) * 100);
				}else {
					$percentage = 0;
				}
				
				if (!$this->session->userdata('staff') && $ongoing == true) {
					$choice['name'] = $alphabet[$count];
				}

				$count++;
				$output .=	"</div>
						<span class='choice-name'>
							&nbsp;$choice[name] 

						</span>  
						
					</div>
					
						
				";	
			 

				$output .= "<div class='col-md-$col2 vertical-align'>";
				if ($poll_data['publish'] && !$this->session->userdata('staff') && $ongoing == true) {
					$output .= "<span class='votes-bar'>
						 <span class='vote-percentage' style ='width:$percentage%'></span>
						 <div class='num_percentage'>$percentage%</span>
						</div>";
					 
				}
				$output .= "</div>";

				if (!$choice[0]) {
					$output .= "
						<div class='col-md-2 vertical-align '>
							<p class='mx-auto'>0 Vote</p>
						</div>
					";
				}else if ($choice[0] == 1) {
					$output .= "
					<div class='col-md-2 vertical-align '>
							<p class='mx-auto'>$choice[0] Vote</p>
					</div>
					";
				}else {
					$output .= "
					<div class='col-md-2 vertical-align '>
							<p class='mx-auto'>$choice[0] Votes</p>
						</div>
					";
				}

				

				if ($this->session->userdata('staff') && !$poll_data['publish']) {
					$output .= "
				
						<div class='col-md-12'>
							<button class='btn btn-info upload-avatar border-0' data-toggle='collapse' href='#collapse$choice[id]' aria-expanded='false' aria-controls='collapseExample'>
						    	Upload Avatar
						  	</button>
						</div>

					";
					$output .= "
					
					<div class='collapse' id='collapse$choice[id]'>
						<div class='col-md-12'>
							<form enctype='multipart/form-data' id='$choice[id]' name='upload_image' class='upload_form'>
								<div >
									<input type='file' class='image_file' name='image_file' id='$choice[id]'></input>
									<input type='submit' value='Upload' ></input>
								</div>
								
							</form>
						</div>
						</div>
					";
					?>
				

					<?php
				}

				$output .= "</div>";
				$i++;

				
			}

			$table .= "<tr><td></td></tr>";
			$output .= "</div>";
	
		}

		$output .= "</div>";
		$output .= "</div>";

		$table .= "</table>";
		echo $output;
		echo $table;
		
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
		$currentGroupID = array();

		$currentID = $this->PollModel->insertPoll($poll_name, $tag_line, $start_time, $end_time);

	 
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