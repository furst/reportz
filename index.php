<?php
require_once('vendor/autoload.php');
require_once('controller/Login.php');
require_once('controller/Report.php');
require_once('model/Connection.php');

\model\Connection::open();

session_start();

// creating controller and slim object
$app = new \Slim\Slim();
$loginController = new \controller\Login($app);
$reportController = new \controller\Report($app);

/**----- ROUTES: LOGIN ----- **/
$app->get('/', function () use ($app) {
    $app->render('home.html');
});

$app->get("/login", function () use ($app) {
	if (\model\User::isLoggedIn()) {
		$app->redirect('dashboard');
	} else {
		$app->render('login.html');
	}
});

$app->post("/login", function () use ($loginController) {
	$loginController->login();
});

$app->get("/logout", function () use ($loginController, $app) {
	$loginController->logout();
	$app->redirect('login');
});

/**----- ROUTES: REPORTS ----- **/
$app->get("/dashboard", function () use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->get_dashboard();
});

$app->get("/reports", function () use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->getReports();
});

$app->get("/new-report", function () use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->get_createReport();
});

$app->post("/new-report", function () use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->post_createReport();
});

// TODO: blocka om man försöker gå till en rapport som inte finns
$app->get("/new-report/:name", function ($name) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->get_createTestcase($name);
});

$app->post("/new-report/:name", function ($name) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->post_createTestcase($name);
});

$app->get("/report/:name", function ($name) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->getReport($name);
});

$app->get("/public/report/:name", function ($name) use ($app, $reportController) {
	$reportController->get_publicReport($name);
});

$app->get("/success", function () use ($app, $reportController) {
	$app->render('success.html');
});

$app->post("/public/report/:name", function ($name) use ($app, $reportController) {
	$post = (object)$app->request()->post();

	if (!isset($post->username)) {
		$reportController->post_commentPublicReport($name, $post);
	} else {
		$reportController->post_publicReport($name);
	}
});

$app->get("/public/edit-report/:name/:uniqueId", function ($name, $uniqueId) use ($app, $reportController) {
	$reportController->get_editPublicReport($name, $uniqueId);
});

$app->post("/public/edit-report/:name/:uniqueId", function ($name, $uniqueId) use ($app, $reportController) {
	$post = (object)$app->request()->post();

	if (!isset($post->report_id)) {
		$reportController->post_commentPublicReport($name);
	} else {
		$reportController->post_editPublicReport($name, $uniqueId);
	}
});

$app->get("/view-report/:name/:uniqueId", function ($name, $uniqueId) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->get_viewPublicReport($name, $uniqueId);
});

$app->get("/public/edit-report/:name", function ($name) use ($app, $reportController) {
	$reportController->get_oldPublicReport($name);
});

$app->post("/public/edit-report/:name", function ($name) use ($app, $reportController) {
	$reportController->post_oldPublicReport($name);
});

$app->get("/report/:name/edit-testcase/:testcase", function ($reportName, $testcase) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->get_editTestcase($reportName, $testcase);
});

$app->post("/report/:name/edit-testcase/:testcase", function ($reportName, $testcase) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->post_editTestcase($reportName, $testcase);
});

$app->get("/report/:name/duplicate", function ($reportName) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->duplicateReport($reportName);
});

$app->get("/reports/:name/delete", function ($reportName) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->delete_Report($reportName);
});

$app->get("/edit-report/:name", function ($reportName) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->get_editReport($reportName);
});

$app->post("/edit-report/:name", function ($reportName) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->post_editReport($reportName);
});

$app->get("/report/:name/delete-testcase/:testcase", function ($reportName, $testcase) use ($app, $reportController) {
	if (!\model\User::isLoggedIn()) {
		$app->redirect('login');
	}

	$reportController->delete_testcase($reportName, $testcase);
});

$app->run();

\model\Connection::close();






