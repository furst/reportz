<?php

namespace model;

require_once("model/Connection.php");
require_once("model/Comment.php");
require_once("model/Validation.php");

class Testcase {

	/**
	 * Table fields
	 * @var string
	 */
	public $name;
	public $description;
	public $report;

	/**
	 * @param  string $uniqueName
	 * @return array
	 */
	public function getCases($uniqueName) {
		$comment = new Comment();

		$sql = "SELECT id, name, description FROM testcase WHERE report = ?;";
		$stmt = Connection::prepare($sql, $uniqueName);

		$stmt->bind_result($id, $name, $description);

		$testcases = array();
		while ($stmt->fetch()) {
			$comments = $comment->getComments($id);
			$testcases[] = array(
							"id" => $id,
							"name" => $name,
							"description" => $description,
							"comments" => $comments
						);
		}

		$stmt->close();

		return $testcases;
	}

	/**
	 * @param  string $testcaseId
	 */
	public function getCase($testcaseId) {

		$sql = "SELECT name, description, report FROM testcase WHERE id = ?;";
		$stmt = Connection::prepare($sql, $testcaseId);

		$stmt->bind_result($name, $description, $report);

		while ($stmt->fetch()) {
			$this->name = $name;
			$this->description = $description;
			$this->report = $report;
		}

		$stmt->close();
	}

	/**
	 * @param  string $testcaseId
	 * @param  post-object $post
	 */
	public function update($testcaseId, $post) {

		try {
			$name = Validation::checkName($post->name);
			$description = Validation::checkDescription($post->description);

			$sql = "UPDATE testcase SET name = ?, description = ? WHERE id = ?;";
			$stmt = Connection::prepare($sql, $name, $description, $testcaseId);

			$stmt->close();

			$this->name = $name;
			$this->description = $description;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $newReportName
	 * @param  string $oldReportName
	 */
	public function updateReport($newReportName, $oldReportName) {

		$sql = "UPDATE testcase SET report = ? WHERE report = ?;";
		$stmt = Connection::prepare($sql, $newReportName, $oldReportName);

		$stmt->close();
	}

	/**
	 * [create description]
	 * @param  post-object $post
	 * @param  string $reportName
	 */
	public function create($post, $reportName) {

		try {
			$name = Validation::checkName($post->name);
			$description = Validation::checkDescription($post->description);

			$sql = "INSERT INTO testcase (name, description, report) VALUES (?, ?, ?);";
			$stmt = Connection::prepare($sql, $name, $description, $reportName);

			$stmt->close();
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $testcaseId
	 */
	public function delete($testcaseId) {

		$sql = "DELETE FROM testcase WHERE id = ?;";
		$stmt = Connection::prepare($sql, $testcaseId);

		$stmt->close();

	}

	/**
	 * @param  string $reportName
	 */
	public function deleteByReport($reportName) {

		$sql = "DELETE FROM testcase WHERE report = ?;";
		$stmt = Connection::prepare($sql, $reportName);

		$stmt->close();

	}
}








