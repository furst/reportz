<?php

namespace model;

require_once("model/Connection.php");
require_once("model/Validation.php");

class Report {

	/**
	 * Table fields
	 * @var string
	 */
	public $id;
	public $name;
	public $uniqueName;

	/**
	 * @param  string $reportName
	 */
	public function get($reportName) {

		$sql = "SELECT id, name, unique_name FROM report WHERE unique_name = ?;";
		$stmt = Connection::prepare($sql, $reportName);

		$stmt->bind_result($id, $name, $uniqueName);

		while ($stmt->fetch()) {
			$this->id = $id;
			$this->name = $name;
			$this->uniqueName = $uniqueName;
		}

		$stmt->close();
	}

	/**
	 * @param  string $id
	 */
	public function getById($id) {

		$mysqli = Connection::openSecond();

		$stmt = $mysqli->prepare("SELECT id, name, unique_name FROM report WHERE id = ?;");

		$stmt->bind_param('s', $id);

		$stmt->execute();

		$stmt->bind_result($id, $name, $uniqueName);

		while ($stmt->fetch()) {
			$this->id = $id;
			$this->name = $name;
			$this->uniqueName = $uniqueName;
		}

		$stmt->close();

		$mysqli->close();
	}

	/**
	 * @param  string $limit optional
	 */
	public function getReports($limit = 9999) {

		$sql = "SELECT name, unique_name FROM report ORDER BY created DESC LIMIT ?";
		$stmt = Connection::prepare($sql, $limit);

		$stmt->bind_result($name, $uniqueName);

		$reports = array();
		while ($stmt->fetch()) {
			$reports[] = array("name" => $name, "unique_name" => $uniqueName);
		}

		$stmt->close();

		return $reports;
	}

	/**
	 * @param  post-object $post
	 */
	public function create($post) {

		try {
			$name = Validation::checkName($post->name);
			$uniqueName = $this->generateUniqueName($name);

			$sql = "INSERT INTO report (name, unique_name) VALUES (?, ?);";
			$stmt = Connection::prepare($sql, $name, $uniqueName);

			$this->name = $name;
			$this->uniqueName = $uniqueName;

			$stmt->close();
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $name
	 * @return string
	 */
	private function generateUniqueName($name) {
		$uniqueName = str_replace("'", '', $name);
		$uniqueName = preg_replace('/[åöä]/', '', $uniqueName);
		$uniqueName = str_replace(" ", "-", $uniqueName);

		$isUnique = true;

		$sql = "SELECT unique_name FROM report WHERE unique_name = ?;";
		$stmt = Connection::prepare($sql, $uniqueName);

		if ($stmt->fetch() != NULL) {
			$uniqueName = $uniqueName . "-2";
			$isUnique = false;
		}

		$stmt->close();

		if (!$isUnique) {
			$uniqueName = $this->generateUniqueName($uniqueName);
		}

		return $uniqueName;
	}

	/**
	 * @param  string $uniqueName
	 * @param  post-object $post
	 */
	public function update($uniqueName, $post) {

		try {
			$name = Validation::checkName($post->name);
			$newUniqueName = $this->generateUniqueName($name);

			$sql = "UPDATE report SET name = ?, unique_name = ? WHERE unique_name = ?;";
			$stmt = Connection::prepare($sql, $name, $newUniqueName, $uniqueName);

			$stmt->close();

			$testcase = new \model\Testcase();
			$testcase->updateReport($newUniqueName, $uniqueName);

			$this->uniqueName = $newUniqueName;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $uniqueName
	 */
	public function delete($uniqueName) {

		$sql = "DELETE FROM report WHERE unique_name = ?;";
		$stmt = Connection::prepare($sql, $uniqueName);

		$stmt->close();

		$testcase = new \model\Testcase();
		$testcase->deleteByReport($uniqueName);

	}

	/**
	 * @param  string $uniqueName
	 */
	public function duplicate($uniqueName) {

		//--- Get report

		$sql = "SELECT name FROM report WHERE unique_name = ?;";
		$stmt = Connection::prepare($sql, $uniqueName);

		$stmt->bind_result($name);

		while ($stmt->fetch()) {
			$reportName = array("name" => $name);
		}

		$stmt->close();

		//--- Get testcases

		$sql = "SELECT name, description FROM testcase WHERE report = ?;";
		$stmt = Connection::prepare($sql, $uniqueName);

		$stmt->bind_result($name, $description);

		$testcases = array();
		while ($stmt->fetch()) {
			$testcases[] = array(
							"name" => $name,
							"description" => $description
						);
		}

		$stmt->close();

		//--- Create new report

		$this->create((object)$reportName);

		//--- Create new testcases

		foreach ($testcases as $testcase) {
			$testcase = (object)$testcase;

			$testcaseModel = new \model\Testcase();
			$testcaseModel->create($testcase, $this->uniqueName);
		}
	}
}








