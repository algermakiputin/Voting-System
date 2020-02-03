<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsersController extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function loginUser() {

		$username = $this->form_validation->set_rules('username', 'Username', 'required');
		$password = $this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE ) {

			$this->session->set_flashdata('errorMessage', validation_errors());
			redirect('/login');
		
		}else {

			$this->load->model('UsersModel');
			
			if ($this->UsersModel->login() == FALSE) {

				$this->session->set_flashdata('errorMessage','Incorrect Login Name Or Password');

				redirect('/login');
				
			}else {

				$data = $this->UsersModel->login();
				$userData = array(
					'id' => $data['id'],
					'name' => $data['name'],
					'username' => $data['username'],
					'isLogin' => true
				);

				$this->session->set_userdata($data);
				
				redirect('/');

			}

		}

	}

	function delete_user() {

		$this->load->model('UsersModel');
		if ($this->UsersModel->delete_user($this->input->post('id'))) {
			echo "ok";
		}
	}

	public function saveUser() {
		
		$this->form_validation->set_rules('name', 'Full Name', 'required|trim');
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		$this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|trim|matches[password]');	
		if ($this->form_validation->run() == FALSE)
	    {

	    	$this->session->set_flashdata('message', validation_errors());

	    	echo "<div class='alert alert-danger'>";
	    	echo "<h5>Error Message</h5>";
	    	echo "<a class='close' data-dismiss='alert' aria-label='close' title='close'>×</a>";
	    	echo $this->session->flashdata('message');
	    	echo "</div>";

	    } else {

	    	$this->load->model('UsersModel');

	    	if ( $this->UsersModel->save() ) {

	    		$this->session->set_flashdata('successMessage', 'Register Successfully');
	    		echo "ok";
		    } 

	    }
		
	}

	function search_user() {
		$this->load->model('UsersModel');
		$users = $this->UsersModel->search_user($this->input->post('query'));
		$output = "";
		$output .= "<p style='padding:5px 10px;'>Result: " . count($users)."</p>";
		$output .= "<div id='table-container' >";
		if ( $users ) {
			$output .= "<table class='table table-bordered table-striped' id='users-table'> ";
			$output .= "
			<thead>
			<tr>
				<th>Number</th>
				<th>Name</th>
				<th>Username</th>
				<th>Password</th>
				<th>Grade</th>
				<th>Section</th>
				<th>Voted</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			";
			$counter = 1;
			foreach ($users as $users) {
				$output .= "
					<tr id='row$users[id]'>
						<td>$counter</td>
						<td>$users[name]</td>
						<td>$users[username]</td>
						<td>$users[password]</td>
						<td>$users[grade]</td>
						<td>$users[section]</td>
						
				";

				if ($users['voted']) {
					$output .= "<td>Yes</td>";
				}else {
					$output .= "<td>No</td>";
				}

				$output .= "
				<td>
					<button class='btn btn-info btn-sm edit' id='$users[id]'>Edit</button>
					<button class='btn btn-danger btn-sm delete' id='$users[id]'>Delete</button>
				</td>
				</tr>";
				$counter ++;
			}

			$output .= "</tbody></table>";
			$output .= "</div>";
			echo $output;
		}else {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;        No result found";
		}
	}

	function lagout() {

		$this->session->sess_destroy();
        redirect('login');
	}

	function getAllUsers() {

		$this->load->library('pagination');
		$this->load->model("UsersModel");
		$users = $this->UsersModel->getAllUsers();
		$config['base_url'] = base_url() . 'manage/pagination/';
		$config['first_url'] = 'manage/pagination/1'; 
		$config['total_rows'] = $this->UsersModel->num_rows() ;
		$config['per_page'] = 10;

		$config['full_tag_open'] = '<ul class="pagination" id="search_page_pagination">';
	  	$config['full_tag_close'] = '</ul>';
	  	$config['cur_tag_open'] = '<li class="active page-item"><a href="javascript:void(0)">';
	  	$config['num_tag_open'] = '<li>';
	  	$config['num_tag_close'] = '</li>';
	  	$config['cur_tag_close'] = '</a></li>';
	  	$config['first_link'] = 'First';
	  	$config['first_tag_open'] = '<li>';
	  	$config['first_tag_close'] = '</li>';
	  	$config['last_link'] = 'Last';
	  	$config['last_tag_open'] = '<li>';
	  	$config['last_tag_close'] = '</li>';
	  	$config['next_link'] = FALSE;
	  	$config['next_tag_open'] = '<li>';
	  	$config['next_tag_close'] = '</li>';
	  	$config['prev_link'] = FALSE;
	  	$config['prev_tag_open'] = '<li>';
	  	$config['prev_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$output = "";
		$output .= "<div class='poll'>";
	
		$output .= "<div class='pad-15'>";
		$output .= "<h4>Registered Voters</h4>";

		$output .= "<div class='filter'>
			<div class='form-inline'> 
			<div class='form-group mx-sm-3 mb-2'>
				<label for='saerch' class='sr-only'>Search</label>
				<input type='text' class='form-control' id='search' placeholder='search...'>
			</div>
			<button type='submit' class='btn btn-primary mb-2' id='search_btn'>Search</button>
			</div>
			
			
		</div>";
		$output .= "";
		$output .= "</div>";
		$output .= "<div id='table-container' >";
		if ( $users ) {
			$output .= "<table class='table table-bordered table-striped' id='users-table'> ";
			$output .= "
			<thead>
			<tr>
				<th>Number</th>
				<th>Name</th>
				<th>Username</th>
				<th>Password</th>
				<th>Grade</th>
				<th>Section</th>
				<th>Voted</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			";
			$counter = 1;
			foreach ($users as $users) {
				$output .= "
					<tr id='row$users[id]'>
						<td>$counter</td>
						<td>$users[name]</td>
						<td>$users[username]</td>
						<td>$users[password]</td>
						<td>$users[grade]</td>
						<td>$users[section]</td>
						
				";

				if ($users['voted']) {
					$output .= "<td>Yes</td>";
				}else {
					$output .= "<td>No</td>";
				}

				$output .= "
				<td>
					<button class='btn btn-info btn-sm edit' id='$users[id]'>Edit</button>
					<button class='btn btn-danger btn-sm delete' id='$users[id]'>Delete</button>
				</td>
				</tr>";
				$counter ++;
			}

			$output .= "</tbody></table>";
			$output .= '<nav>'.$this->pagination->create_links() . "</nav>";
			$output .= "
					<div class='users-delete-action pad-15'>
						<button class='btn btn-danger ' id='deleteAllUser'>Delete All User</button>
						
					</div>
						";
			$output .= "</div>";
			$output .= "</div>";

		}else {

			echo "No Result Found";
		}
		
		echo $output;

	}

	function filterUsers() {

		$section = $this->input->post('section');

		$this->load->model("UsersModel");
		$users = $this->UsersModel->filterUsersBySection($section);
 
		echo $this->output($users);
		
	}

	function output ($users) {

		$output = "";

		if ($users) {
			$output .= "<table class='table table-bordered table-striped' id='users-table'> ";
			
			$output .= "<tr>
				<th>Number</th>
				<th>Name</th>
				<th>Username</th>
				<th>Password</th>
				<th>Grade</th>
				<th>Section</th>
				<th>Voted</th>
				<th>Actions</th>
			</tr>";
			$counter = 1;
			foreach ($users as $users) {
				$output .= "
					<tr id='row$users[id]'>
						<td>$counter</td>
						<td>$users[name]</td>
						<td>$users[username]</td>
						<td>$users[password]</td>
						<td>$users[grade]</td>
						<td>$users[section]</td>
				";

				if ($users['voted']) {
					$output .= "<td>Yes</td>";
				}else {
					$output .= "<td>No</td>";
				}

				$output .= "
				<td>
					<button class='btn btn-info btn-sm edit' id='$users[id]'>Edit</button>
					<button class='btn btn-danger btn-sm delete' id='$users[id]'>Delete</button>
				</td>
				</tr>";
				$counter ++;
			}

			$output .= "</table>";
			$output .= "
				<div class='users-delete-action pad-15'>
					<button class='btn btn-danger' id='deleteAllUser'>Delete All User</button>
					<button class='btn btn-danger' id='resetVotes'>Reset Votes Status</button>
				</div>
					";

			return $output;

		}

		return "No Results";
	}
 
	function edit_user() {
		$data = array (
			'name' => $this->input->post('name'),
			'username' => $this->input->post('username'),
			'password' => $this->input->post('password'),
			'grade' => $this->input->post('grade'),
			'section' => $this->input->post('section')

		);

		$id = $this->input->post('id');
		$this->load->model('UsersModel');

		$query = $this->UsersModel->edit_user($id, $data);
		
	}

	function deleteAllUsers() {
		$this->load->model('UsersModel');
		$this->UsersModel->deleteAllUsers();	
	}

	function resetAllUserVotes() {
		$this->load->model('UsersModel');
		$this->UsersModel->resetAllVotes();

	}

	function pagination($num) {
		$this->load->library('pagination');
		$this->load->model('UsersModel');
		$config['base_url'] = base_url() . 'manage/pagination';
		$config['total_rows'] = $this->UsersModel->num_rows() ;
		$config['first_url'] = 'manage/pagination/1';
		$config['per_page'] = 10;
		$config['full_tag_open'] = '<ul class="pagination" id="search_page_pagination">';
	  	$config['full_tag_close'] = '</ul >';
	  	$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0)">';
	  	$config['num_tag_open'] = '<li>';
	  	$config['num_tag_close'] = '</li>';
	  	$config['cur_tag_close'] = '</a></li>';
	  	$config['first_link'] = 'First';
	  	$config['first_tag_open'] = '<li>';
	  	$config['first_tag_close'] = '</li>';
	  	$config['last_link'] = 'Last';
	  	$config['last_tag_open'] = '<li>';
	  	$config['last_tag_close'] = '</li>';
	  	$config['next_link'] = FALSE;
	  	$config['next_tag_open'] = '<li>';
	  	$config['next_tag_close'] = '</li>';
	  	$config['prev_link'] = FALSE;
	  	$config['prev_tag_open'] = '<li>';
	  	$config['prev_tag_close'] = '</li>';
		$output = "";
		$this->pagination->initialize($config);
		$results = $this->UsersModel->pagination($num);
		$output .= "<table class='table table-bordered table-striped' id='users-table'> ";
			$output .= "
			<thead>
			<tr>
				<th>Number</th>
				<th>Name</th>
				<th>Username</th>
				<th>Password</th>
				<th>Grade</th>
				<th>Section</th>
				<th>Voted</th>
				<th>Action</th>
			</tr>
			</thead>
			<tbody>
			";
		foreach ($results as $result) {
			$output .= "
			
				<tr id='row$result[id]'>
					<td>$num</td>
					<td>$result[name]</td>
					<td>$result[username]</td>
					<td>$result[password]</td>
					<td>$result[grade]</td>
					<td>$result[section]</td>
					<td>$result[voted]</td>
					<td>
						<button class='btn btn-info btn-sm edit' id='$result[id]'>Edit</button>
						<button class='btn btn-danger btn-sm delete' id='$result[id]'>Delete</button>
					</td>
				</tr>
			

			";
			$num++;
		}
		$output .= "</tbody> </table>";

		echo $output;
		echo $this->pagination->create_links();

		echo "
			<div class='users-delete-action pad-15'>
				<button class='btn btn-danger' id='deleteAllUser'>Delete All User</button>
				<button class='btn btn-danger' id='resetVotes'>Reset Votes Status</button>
			</div>
				";
	}

	

}
