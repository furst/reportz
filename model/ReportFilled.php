<?php

namespace model;

require_once("model/Connection.php");

class ReportFilled {

	/**
	 * Table fields
	 * @var string
	 */
	public $id;
	public $username;
	public $uniqueId;

	/**
	 * @param  string $post
	 */
	public function create($post) {

		try {
			$username = Validation::checkName($post->username);
			$email = Validation::checkEmail($post->email);
			$reportId = $post->report_id;
			$uniqueId = uniqid($username);

			$sql = "INSERT INTO report_filled (username, report_id, unique_id) VALUES (?, ?, ?);";
			$stmt = Connection::prepare($sql, $username, $reportId, $uniqueId);

			$this->id = Connection::getId();

			$stmt->close();

			$this->uniqueId = $uniqueId;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $reportId
	 * @return array
	 */
	public function getFilledReports($reportId) {

		$sql = "SELECT username, unique_id FROM report_filled WHERE report_id = ? ORDER BY created DESC;";
		$stmt = Connection::prepare($sql, $reportId);

		$stmt->bind_result($username, $uniqueId);

		$reports = array();
		while ($stmt->fetch()) {
			$reports[] = array("username" => $username, "unique_id" => $uniqueId);
		}

		$stmt->close();

		return $reports;
	}

	/**
	 * @param  integer $limit optional
	 * @return array
	 */
	public function getAllFilledReports($limit = 9999) {

		$sql = "SELECT username, report_id, unique_id FROM report_filled ORDER BY created DESC LIMIT ?;";
		$stmt = Connection::prepare($sql, $limit);

		$stmt->bind_result($username, $reportId, $uniqueId);

		$reports = array();
		while ($stmt->fetch()) {
			$reportModel = new Report();
			$reportModel->getById($reportId);
			$reports[] = array("username" => $username, "unique_id" => $uniqueId, "report" => $reportModel);

		}

		$stmt->close();

		return $reports;
	}

	/**
	 * @param  string $reportId
	 * @param  string $uniqueUserId
	 */
	public function getFilledReport($reportId, $uniqueUserId) {

		$sql = "SELECT id, username, unique_id FROM report_filled WHERE report_id = ? AND unique_id = ?;";
		$stmt = Connection::prepare($sql, $reportId, $uniqueUserId);

		$stmt->bind_result($id, $username, $uniqueId);

		while ($stmt->fetch()) {
			$this->id = $id;
			$this->username = $username;
			$this->uniqueId = $uniqueId;
		}

		$stmt->close();
	}

	/**
	 * @param  string $reportId
	 * @param  string $uniqueUserId
	 * @return array
	 */
	public function getFilledTestcases($reportId, $uniqueUserId) {

		$sql = "SELECT is_completed, testcase_id FROM report_filled INNER JOIN testcase_filled ON report_filled.id = testcase_filled.report_filled_id WHERE unique_id = ? AND report_id = ?;";
		$stmt = Connection::prepare($sql, $uniqueUserId, $reportId);

		$stmt->bind_result($isCompleted, $testcaseId);

		// Loop the results and fetch into an array
		$filledTestcases = array();
		while ($stmt->fetch()) {
			$filledTestcases[] = array("is_completed" => $isCompleted, "testcase_id" => $testcaseId);
		}

		$stmt->close();

		return $filledTestcases;
	}

	/**
	 * @param  string $code
	 * @param  string $reportId
	 */
	public function checkUserId($code, $reportId) {

		$sql = "SELECT unique_id FROM report_filled WHERE unique_id = ? AND report_id = ?;";
		$stmt = Connection::prepare($sql, $code, $reportId);

		$stmt->bind_result($uniqueId);

		$stmt->fetch();

		if (empty($uniqueId)) {
			throw new \Exception("Den angivna koden kan ej hittas till denna rapport");
		}

		$stmt->close();
	}
}





