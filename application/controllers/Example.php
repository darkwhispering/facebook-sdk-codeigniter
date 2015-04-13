<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('facebook');
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('examples/start');
	}

	public function web_login()
	{

		$data['user'] = array();

		if ($this->facebook->logged_in())
		{
			$user = $this->facebook->user();

			if ($user['code'] === 200)
			{
				unset($user['data']['permissions']);
				$data['user'] = $user['data'];
			}

		}

		$this->load->view('examples/web', $data);
	}

	public function js_login()
	{
		$this->load->view('examples/js');
	}

	public function post()
	{

		header('Content-Type: application/json');

		$result = $this->facebook->publish_text($this->input->post('message'));
		echo json_encode($result);

	}

	public function logout()
	{
		$this->facebook->destroy_session();
		redirect('example/web_login', redirect);
	}
}
