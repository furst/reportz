<?php

namespace controller;

require_once('model/Report.php');
require_once('model/Testcase.php');
require_once('model/ReportFilled.php');
require_once('model/TestcaseFilled.php');
require_once('Email.php');

class Report {

	/**
	 * Used to reach some helper-methods
	 * @var Slim-object
	 */
	private $app;

	/**
	 * @var Report-object
	 */
	private $report;

	/**
	 * @var Testcase-object
	 */
	private $testcase;

	/**
	 * @var ReportFilled-object
	 */
	private $reportFilled;

	/**
	 * @var TestcaseFilled-object
	 */
	private $testcaseFilled;

	/**
	 * Holder for session
	 * @var string
	 */
	private static $sessionMessageHolder = 'sessionMessageHolder';

	/**
	 * Holder for session
	 * @var string
	 */
	private static $sessionInputHolder = 'sessionInputHolder';

	public function __construct($app) {
		$this->app = $app;
		$this->report = new \model\Report();
		$this->testcase = new \model\Testcase();
		$this->reportFilled = new \model\ReportFilled();
		$this->testcaseFilled = new \model\TestcaseFilled();
	}

	/**
	 * Get message stored in session
	 * @return string
	 */
	public function getMessage() {
		$message = "";
		if (isset($_SESSION[self::$sessionMessageHolder])) {
			$message = $_SESSION[self::$sessionMessageHolder];
			unset($_SESSION[self::$sessionMessageHolder]);
		}

		return $message;
	}

	/**
	 * Get latest input stored in session
	 * @return object
	 */
	public function getInput() {
		$input = "";
		if (isset($_SESSION[self::$sessionInputHolder])) {
			$input = $_SESSION[self::$sessionInputHolder];
			unset($_SESSION[self::$sessionInputHolder]);
		}

		return $input;
	}

	/**
	 * Set message and latest input in session
	 * @param string $message
	 * @param string $post
	 */
	public function setMessage($message, $post = "") {
		$_SESSION[self::$sessionMessageHolder] = $message;
		$_SESSION[self::$sessionInputHolder] = $post;
	}

	public function get_dashboard() {

		// Get reports and filled reports
		$reports = $this->report->getReports(5);
		$filledReports = $this->reportFilled->getAllFilledReports(5);

		$this->app->render('dashboard.html', array(
								"reports" => $reports,
								"filledReports" => $filledReports
							));
	}

	public function getReports() {

		$reports = $this->report->getReports();

		$this->app->render('reports.html', array("reports" => $reports));
	}

	/**
	 * @param  string $uniqueName
	 */
	public function getReport($uniqueName) {

		$report = $this->report->get($uniqueName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$testcases = $this->testcase->getCases($uniqueName);

		$filled = new \model\ReportFilled();
		$reportsFilled = $filled->getFilledReports($this->report->id);

		$message = $this->getMessage();

		$this->app->render('report.html', array(
							"report" => $this->report,
							"uniqueName" => $uniqueName,
							"testcases" => $testcases,
							"reportsFilled" => $reportsFilled,
							"message" => $message
						));
	}

	/**
	 * @param  string $uniqueName
	 */
	public function get_publicReport($uniqueName) {

		$this->report->get($uniqueName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$testcases = $this->testcase->getCases($uniqueName);

		$message = $this->getMessage();
		$input = $this->getInput();

		$this->app->render('public-report.html', array(
							"report" => $this->report,
							"uniqueName" => $uniqueName,
							"testcases" => $testcases,
							"message" => $message,
							"input" => $input
						));
	}

	/**
	 * @param  string $uniqueName
	 * @param  string $uniqueUserId
	 */
	public function get_editPublicReport($uniqueName, $uniqueUserId) {

		$this->report->get($uniqueName);

		$testcases = $this->testcase->getCases($uniqueName);

		$filledTestcases = $this->reportFilled->getfilledTestcases($this->report->id, $uniqueUserId);
		$this->reportFilled->getFilledReport($this->report->id, $uniqueUserId);

		// Redirect to 404 if no match is found
		if (empty($this->report->name) || empty($this->reportFilled->uniqueId)) {
			$this->app->notFound();
		}

		$message = $this->getMessage();

		$this->app->render('edit-filled-report.html', array(
							"report" => $this->report,
							"uniqueName" => $uniqueName,
							"testcases" => $testcases,
							"filledTestcases" => $filledTestcases,
							"username" => $this->reportFilled->username,
							"message" => $message
						));
	}

	/**
	 * @param  string $uniqueName
	 * @param  string $uniqueUserId
	 */
	public function post_editPublicReport($uniqueName, $uniqueUserId) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		$this->report->get($uniqueName);

		$testcases = $this->testcase->getCases($uniqueName);

		$this->reportFilled->getFilledReport($this->report->id, $uniqueUserId);

		foreach ($post as $key => $value) {
			if ($key != 'username' && $key != 'report_id' && $key != 'email') {
				$this->testcaseFilled->update($this->reportFilled->id, $value, $key);
			}
		}

		$filledTestcases = $this->reportFilled->getfilledTestcases($this->report->id, $uniqueUserId);

		$this->setMessage(\view\Message::reportUpdated());

		$this->app->redirect($uniqueUserId);
	}

	/**
	 * @param  string $uniqueName
	 */
	public function post_publicReport($uniqueName) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$this->report->get($uniqueName);

			$testcases = $this->testcase->getCases($uniqueName);

			$this->reportFilled->create($post);

			foreach ($post as $key => $value) {
				if ($key != 'username' && $key != 'report_id' && $key != 'email') {
					$this->testcaseFilled->create($this->reportFilled->id, $value, $key);
				}
			}

			\Email::send($post->email, $this->report->name, $this->reportFilled->uniqueId, $this->report->uniqueName);

			$this->app->redirect('../../success');

		} catch (\Exception $e) {
			$message = $e->getMessage();
			$this->app->render('public-report.html', array(
							"report" => $this->report,
							"uniqueName" => $uniqueName,
							"testcases" => $testcases,
							"message" => $message,
							"input" => $post
						));
		}
	}

	/**
	 * @param  string $uniqueName
	 * @param  string $uniqueUserId
	 */
	public function get_viewPublicReport($uniqueName, $uniqueUserId) {

		$this->report->get($uniqueName);

		$testcases = $this->testcase->getCases($uniqueName);

		$filledTestcases = $this->reportFilled->getfilledTestcases($this->report->id, $uniqueUserId);
		$this->reportFilled->getFilledReport($this->report->id, $uniqueUserId);

		// Redirect to 404 if no match is found
		if (empty($this->report->name) || empty($this->reportFilled->uniqueId)) {
			$this->app->notFound();
		}

		$this->app->render('view-public-report.html', array(
							"report" => $this->report,
							"uniqueName" => $uniqueName,
							"testcases" => $testcases,
							"filledTestcases" => $filledTestcases,
							"filledReport" => $this->reportFilled
						));
	}

	/**
	 * @param  string $uniqueName
	 */
	public function post_commentPublicReport($uniqueName) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$comment = new \model\Comment();
			$comment->create($post);

		} catch (\Exception $e) {

			$this->app->redirect($uniqueName);
		}

		$this->app->redirect($uniqueName);
	}

	/**
	 * @param  string $uniqueName
	 */
	public function get_oldPublicReport($uniqueName) {

		$this->report->get($uniqueName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$message = $this->getMessage();
		$input = $this->getInput();

		$this->app->render('get-old-report.html', array(
							"name" => $this->report->name,
							"message" => $message,
							"input" => $input
						));
	}

	/**
	 * @param  string $uniqueName
	 */
	public function post_oldPublicReport($uniqueName) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$this->report->get($uniqueName);

			$filledReport = new \model\ReportFilled();
			$filledReport->checkUserId($post->code, $this->report->id);
		} catch (\Exception $e) {
			$this->setMessage($e->getMessage(), $post);
			$this->app->redirect($uniqueName);
		}

		$this->app->redirect("$uniqueName/$post->code");
	}

	/**
	 * @param  string $uniqueName
	 * @param  string $testcaseId
	 */
	public function get_editTestcase($uniqueName, $testcaseId) {

		$this->report->get($uniqueName);

		$this->testcase->getCase($testcaseId);

		// Redirect to 404 if no match is found
		if (empty($this->report->name) || empty($this->testcase->name) || $this->testcase->report != $uniqueName) {
			$this->app->notFound();
		}

		$message = $this->getMessage();

		$this->app->render('edit-testcase.html', array(
							"report" => $this->report,
							"testcase" => $this->testcase,
							"uniqueName" => $uniqueName,
							"message" => $message
						));
	}

	/**
	 * @param  string $uniqueName
	 * @param  string $testcaseId
	 */
	public function post_editTestcase($uniqueName, $testcaseId) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$this->report->get($uniqueName);

			$this->testcase->update($testcaseId, $post);

			$this->setMessage(\view\Message::testcaseUpdated());
		} catch (\Exception $e) {
			$this->setMessage($e->getMessage());
			$this->app->redirect($testcaseId);
		}

		$this->app->redirect($testcaseId);
	}

	public function get_createReport() {
		$message = $this->getMessage();
		$input = $this->getInput();

		$this->app->render('new-report.html', array("message" => $message, "input" => $input));
	}

	public function post_createReport() {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$this->report->create($post);

			$this->setMessage(\view\Message::reportCreated());
		} catch (\Exception $e) {
			$this->setMessage($e->getMessage(), $post);
			$this->app->redirect("new-report");
		}

		$this->app->redirect("new-report/" . $this->report->uniqueName);
	}

	/**
	 * @param  string $uniqueName
	 */
	public function get_editReport($uniqueName) {

		$this->report->get($uniqueName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$message = $this->getMessage();

		$this->app->render('edit-report.html', array(
							"report" => $this->report,
							"message" => $message
						));
	}

	/**
	 * @param  string $uniqueName
	 */
	public function delete_report($uniqueName) {

		$this->report->get($uniqueName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$this->report->delete($uniqueName);

		$this->app->redirect("../../reports");
	}

	/**
	 * @param  string $uniqueName
	 */
	public function post_editReport($uniqueName) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$this->report->update($uniqueName, $post);

			$this->setMessage(\view\Message::reportUpdated());
		} catch (\Exception $e) {
			$this->setMessage($e->getMessage());
			$this->app->redirect($uniqueName);
		}

		$this->app->redirect("../report/" . $this->report->uniqueName);
	}

	/**
	 * @param  string $reportName
	 */
	public function get_createTestcase($reportName) {

		$this->report->get($reportName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$message = $this->getMessage();
		$input = $this->getInput();

		$this->app->render('new-testcase.html', array(
								"report" => $this->report,
								"uniqueName" => $reportName,
								"message" => $message,
								"input" => $input
							));
	}

	/**
	 * @param  string $reportName
	 */
	public function post_createTestcase($reportName) {

		// Get posted data
		$post = (object)$this->app->request()->post();

		try {
			$this->report->get($reportName);

			$this->testcase->create($post, $reportName);

			$this->setMessage(\view\Message::testcaseAdded());

		} catch (\Exception $e) {
			$this->setMessage($e->getMessage(), $post);
			$this->app->redirect($reportName);
		}

		$this->app->redirect($reportName);
	}

	/**
	 * @param  string $uniqueName
	 * @param  string $testcaseId
	 */
	public function delete_testcase($uniqueName, $testcaseId) {

		$this->report->get($uniqueName);

		$this->testcase->getCase($testcaseId);

		// Redirect to 404 if no match is found
		if (empty($this->report->name) || empty($this->testcase->name) || $this->testcase->report != $uniqueName) {
			$this->app->notFound();
		}

		$this->testcase->delete($testcaseId);

		$this->app->redirect("../../$uniqueName");
	}

	/**
	 * @param  string $uniqueName
	 */
	public function duplicateReport($uniqueName) {

		$this->report->get($uniqueName);

		// Redirect to 404 if no match is found
		if (empty($this->report->name)) {
			$this->app->notFound();
		}

		$this->report->duplicate($uniqueName);

		$this->app->redirect("../../reports");
	}
}







