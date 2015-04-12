<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('facebook');
	}

	public function index()
	{
		$this->load->library('facebook');

		$data['user_id'] = '';
		$data['user'] = array();

		if ($this->facebook->logged_in()) {

			$data['user_id'] = $this->facebook->user_id();
			$data['user'] = $this->facebook->user();

		}

		$this->load->view('example', $data);
	}

	public function logout()
	{

		$this->facebook->destroy_session();

		$this->index();

	}
}
