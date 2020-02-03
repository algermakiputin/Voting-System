<?php

class StaffController extends CI_Controller {

	public function login () {

		$this->load->view('manage/login');
		$this->load->view('template/footer');

	}

	public function loginUser() {
		
		$username = $this->form_validation->set_rules('username', 'Username', 'required');
		$password = $this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE ) {

			$this->session->set_flashdata('errorMessage', validation_errors());
			redirect(base_url() . "manage/login");
		
		}else {

			$this->load->model('UsersModel');
			
			if ($this->UsersModel->staff_login() == FALSE) {

				$this->session->set_flashdata('errorMessage','Incorrect Login Name Or Password');

				redirect('manage/login');
				
			}else {

				$data = $this->UsersModel->staff_login();
				if ($data) {
					$data = array (
						'id' => $data[0]->id,
						'name' => 'staff',
						'username' => $data[0]->username,
						'staff' => true
					);

					$this->session->set_userdata($data);
					redirect('/');
				}
			}

		}

	}

}

?>