<?php

namespace model;

require_once("model/Connection.php");

class TestcaseFilled {

	/**
	 * [create description]
	 * @param  string $reportFilledId
	 * @param  bool $isCompleted
	 * @param  string $testcaseId
	 */
	public function create($reportFilledId, $isCompleted, $testcaseId) {

		$isCompleted = ($isCompleted === 'true');

		$mysqli = Connection::openSecond();

		$stmt = $mysqli->prepare("INSERT INTO testcase_filled (report_filled_id, is_completed, testcase_id) VALUES (?, ?, ?);");

		$stmt->bind_param('sss', $reportFilledId, $isCompleted, $testcaseId);

		$stmt->execute();

		$stmt->close();

		$mysqli->close();
	}

	/**
	 * [create description]
	 * @param  string $reportFilledId
	 * @param  bool $isCompleted
	 * @param  string $testcaseId
	 */
	public function update($reportFilled, $isCompleted, $testcaseId) {

		$isCompletedBool = ($isCompleted === 'true');

		$mysqli = Connection::openSecond();

		$stmt = $mysqli->prepare("UPDATE testcase_filled SET is_completed = ? WHERE report_filled_id = ? AND testcase_id = ?;");

		$stmt->bind_param('sss', $isCompletedBool, $reportFilled, $testcaseId);

		$stmt->execute();

		if ($stmt->affected_rows == 0) {
			$this->create($reportFilled, $isCompleted, $testcaseId);
		}

		$stmt->close();

		$mysqli->close();
	}
}