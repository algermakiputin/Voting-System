<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PagesController extends CI_Controller {

	public function register()
	{
		if (!$this->session->userdata['staff']) {
			redirect(base_url());
		}

		 $this->load->view('pages/register');


	}

	public function login()
	{
		
		$this->session->sess_destroy();
		$this->load->view('pages/login');
		$this->load->view('template/footer');
	}

}

?>