<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct() {
		
		parent::__construct();

		if (!$this->session->userdata('id')) {
		
			redirect('login');
		}
	}

	public function index() {
		$this->load->model('VotesModel');
		$result = $this->VotesModel->checkUserVote( $this->session->userdata('id'));

		$this->load->model('PollModel');

		$data['polls'] = [];
		
		
		
		if ($this->session->userdata('staff')) {
			$data['polls'] = $this->PollModel->getAll();
		}else {
			$data['polls'] = $this->PollModel->getAllPublishedPoll();
		}
		
		$data['results'] = $result;
		
		$data['votes'] = array();	

		foreach ($data['polls'] as $poll) {
			array_push($data['votes'],$this->VotesModel->countVote($poll->id));
		}

		$this->load->view('template/header',$data);
		$this->load->view('pages/home');
		$this->load->view('template/footer');

	}

	public function reloadPage() {
		$this->load->model('VotesModel');
		$voteResults = $this->VotesModel->checkUserVote( $this->session->userdata('id'));

		$this->load->model('PollModel');
		$polls = $this->PollModel->getAll();
 
		$results = $voteResults;
		
		$votes = array();	
		$pollSummary = "";

		foreach ($polls as $p) {
			array_push($votes,$this->VotesModel->countVote($p->id));
		}

		// start here
		$output = "";
		$counter = 0;
				
		$voted = [];
		$today = date("Y-m-d");
		foreach($results as $result) {
			foreach ($result as $res) {
				array_push($voted, $res);
			}
		}
		if ($polls) {
			foreach ($polls as $poll) {

			$end_time = date_format(date_create($poll->end_time), 'M d Y');
			$today_time = strtotime($today);
			$end_date = strtotime($end_time);
			$ended = false;
		 
			if ($end_date < $today_time) {
				$ended = true;
			} 
			
			$output .= "
			<div class='poll poll-home' id='poll-$poll->id'>
				<div class='poll-content'>
					<div class='poll-name'>
						$poll->name 
					</div>
					<div class='poll-desctipion'>
						 $poll->tag_line
					</div>
					<div class='poll-summary'>
			";
			

				if (in_array($poll->id, $voted) && $votes[$counter] == 1 ){
				
					$pollSummary = "You Voted this";

				}if (!in_array($poll->id, $voted) && $votes[$counter] == 1) {
					$pollSummary = "$votes[$counter] people voted";
				}if (in_array($poll->id, $voted) && $votes[$counter] > 1 ){
					$total = $votes[$counter] - 1;
					$pollSummary = "You and $total other people voted this";

				} if ($votes[$counter] == 0) {
					$pollSummary = "No votes yet";
				}

				if ($this->session->userdata('staff')) {
					$pollSummary = "$votes[$counter] people voted";
				}

				$output .= $pollSummary;

		

				$output .= "</div>";
				$startDate =  date_format(date_create($poll->start_time),'M, d Y');
				$endDate = date_format(date_create($poll->end_time), 'M, d Y');
				if ($ended) {

					$output .= "<div class='poll-time'>Voting end</div>";

				}else {
					$output .= "
						<div class='poll-time'>
						From: 
							<span >
								$startDate
							</span><span>
							To:	$endDate
							</span>
						</div>
					";
				}
			
			$output .= "
				<div class='actions'>
					
			";
					
							
			if (!$this->session->userdata('staff')) {
				if (!$ended) {
					if (!in_array($poll->id, $voted)){
						
						$output .= "<a class='btn btn-info view' data='$poll->id'>VOTE</a>";
						
					}
				}
			}else {
				
				if ($poll->publish) {
					$output .= "<a class='btn btn-success' id='publish-$poll->id' data='$poll->id'>PUBLISHED</a>";
				}else {
					$output .= "
						<a class='btn btn-info edit-publish' id='publish-$poll->id' data='$poll->id'>PUBLISH</a>
						

					";
				}

				$output .= " <a class='btn btn-danger delete' id='delete-$poll->id' data='$poll->id'>DELETE</a>";
			}
			$output .= " <a class='btn btn-primary status' data='$poll->id'>VIEW</a> ";
				$output .= "
							
						</div>
					</div>
				</div>
				";			
		

				$counter++;
			}
		}else {
			$output = "
			<div class='poll poll-home' >
				<div class='poll-content'>
					
						<p>No active poll At the moment....</p>	
				
					
				</div>
			</div>

			";
		}
		

		echo $output;
	}


	
}	

?>