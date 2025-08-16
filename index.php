<?php
// FILE: public/installer/index.php
// Main entry point that handles routing based on the 'step' parameter.

// Start the session to store and retrieve data between requests.
// This should be the ONLY place session_start() is called.
session_start();

$envPath = __DIR__ . '/../../.env';
// Check if the .env file exists.
if (file_exists($envPath)) {
    header('Location: /');
    exit; // Redirect to the home page if .env exists, indicating the installation is complete.
}

// Define the path to the installer directory.
define('INSTALLER_PATH', __DIR__);

// Include the controller file which contains all the installation logic.
require_once 'controller.php';

// Determine the current installation step from the URL.
// The default step is 'welcome' to show the initial landing page.
$step = $_GET['step'] ?? 'welcome';

// Get data for the current step from the controller.
$data = handle_step($step);

// Check for errors or success messages to be displayed in the views.
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

// Render the appropriate view based on the step.
switch ($step) {
    case 'welcome':
        require 'views/welcome.html.php';
        break;
    case 'requirements':
        require 'views/requirements.html.php';
        break;
    case 'database':
        require 'views/database.html.php';
        break;
    case 'administrator':
        require 'views/administrator.html.php';
        break;
    case 'finish':
        require 'views/finish.html.php';
        break;
    default:
        // Redirect to the welcome step if the step is invalid.
        header('Location: index.php?step=welcome');
        exit;
}
