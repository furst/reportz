<?php

namespace model;

require_once("model/Connection.php");

class Comment {

	/**
	 * @param  string $testcaseId
	 * @return array with comments
	 */
	public function getComments($testcaseId) {

		$mysqli = Connection::openSecond();

		$stmt = $mysqli->prepare("SELECT name, content FROM comment WHERE testcase_id = ?;");

		$stmt->bind_param('s', $testcaseId);

		$stmt->execute();

		$stmt->bind_result($name, $content);

		// Loop the results and fetch into an array
		$comments = array();
		while ($stmt->fetch()) {
			$comments[] = array("name" => $name, "content" => $content);
		}

		$stmt->close();

		$mysqli->close();

		return $comments;
	}

	/**
	 * [create description]
	 * @param  post-object
	 */
	public function create($post) {

		try {
			$name = Validation::checkName($post->name);
			$content = Validation::checkDescription($post->content);
			$testcaseId = $post->testcase_id;

			$sql = "INSERT INTO comment (name, content, testcase_id) VALUES (?, ?, ?);";
			$stmt = Connection::prepare($sql, $name, $content, $testcaseId);

			$stmt->close();
		} catch (\Exception $e) {
			throw $e;
		}
	}
}