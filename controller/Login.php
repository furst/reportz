<?php

namespace controller;

require_once('model/User.php');
require_once('view/Message.php');

class Login {

	/**
	 * Used to reach some special helper-methods
	 * @var Slim-object
	 */
	private $app;

	/**
	 * @var User-object
	 */
	private $user;

	/**
	 * @var Message-object
	 */
	private $message;

	/**
	 * @param Slim-object
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->user = new \model\User();
	}

	/**
	 * Try to authenticate the user with the usermodel
	 */
	public function login() {
		$post = (object)$this->app->request()->post();
		try {
			$this->user->login($post->username, $post->password);
			$this->app->redirect('dashboard');
		} catch (\Exception $e) {
			$this->app->render('login.html',array(
				'error' => \view\Message::loginError(),
				'username' => $post->username
			));
		}
	}

	public function logout() {
		$this->user->logout();
		$this->app->redirect('login');
	}
}