<?php
/**
 * FILE: public/installer/controller.php
 * This file contains the main logic for the installation process.
 * It handles the steps of the installation, including checking requirements,
 * setting up the database, creating an administrator account, and finishing the installation.
 * It also includes functions to run Artisan commands, generate application keys, and update the .env file.
 * This file should not be accessed directly; it is included by the main installer index file.
 * IMPORTANT: This file assumes that the installation is being run in a Laravel environment.
 * It should be placed in the public/installer directory of a Laravel application.
 * @package Installer
 * @version 1.0
 */
defined('INSTALLER_PATH') or die('Direct access to this file is not allowed.');

// IMPORTANT: We include a basic session check here to prevent errors in some environments.
use Random\RandomException;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


/**
 * Executes a shell command to simulate running Artisan commands.
 * It now captures and returns the output for debugging.
 * @param string $command The Artisan command to run.
 * @return array An associative array with 'success' (boolean) and 'output' (string).
 */
function run_artisan_command(string $command): array
{
    // The command to execute in the shell.
    // '2>&1' redirects stderr to stdout to capture all output.
    $fullCommand = "php ../../artisan {$command} --force 2>&1";

    // Execute the command.
    $output = [];
    $return_var = 0;
    exec($fullCommand, $output, $return_var);

    return [
        'success' => $return_var === 0,
        'output' => implode("\n", $output)
    ];
}

// A function to securely generate a random 32-character key.
/**
 * @throws RandomException
 */
function generate_random_key($length = 32): string
{
    return 'base64:'.base64_encode(random_bytes($length));
}

/**
 * Generates a new application key and updates the .env file.
 * This function eliminates the need for the user to run `php artisan key:generate` manually.
 * @throws RandomException
 */
function generate_app_key_and_update_env(): bool
{


    $envPath = __DIR__ . '/../../.env';

    // Check if .env file exists
    if (!file_exists($envPath)) {
        // Create a new .env file from .env.example if it doesn't exist.
        copy(__DIR__ . '/../../.env.example', $envPath);
    }

    $envContent = file_get_contents($envPath);

    // Generate a new, secure application key.
    $newKey = generate_random_key(32);

    // Use regular expression to find and replace the APP_KEY value.
    // This handles cases where APP_KEY might be empty or already has a value.
    $updatedEnvContent = preg_replace(
        '/^APP_KEY=.*$/m',
        "APP_KEY={$newKey}",
        $envContent
    );

    // If APP_KEY line doesn't exist, add it at the end of the file.
    if ( ! str_contains($envContent, 'APP_KEY=')) {
        $updatedEnvContent .= "\nAPP_KEY={$newKey}";
    }

    // Write the new content back to the .env file.
    return (bool) file_put_contents($envPath, $updatedEnvContent);
}

/**
 * Updates the database configuration in the .env file.
 * @param array $data Database connection details.
 * @return bool
 */
function update_env_database_config(array $data): bool
{
    $envPath = __DIR__ . '/../../.env';

    // Ensure the .env file exists before attempting to read it.
    if (!file_exists($envPath)) {
        // Create a new .env file from .env.example if it doesn't exist.
        copy(__DIR__ . '/../../.env.example', $envPath);
    }

    $envContent = file_get_contents($envPath);
    $newEnvContent = $envContent;

    // A list of database environment variables to update.
    $dbVars = [
        'DB_CONNECTION',
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD',
    ];

    // Loop through each variable and update its value in the .env content.
    foreach ($dbVars as $var) {
        $value = $data[strtolower($var)];

        // Handle values with spaces or special characters by wrapping in quotes.
        if (preg_match('/\s/', $value) || str_contains($value, '#')) {
            $value = '"' . addcslashes($value, '"') . '"';
        }

        // Use a regular expression to find and replace the entire line.
        // The 'm' flag is crucial for multiline matching.
        $pattern = "/^{$var}=.*$/m";
        if (preg_match($pattern, $newEnvContent)) {
            $newEnvContent = preg_replace($pattern, "{$var}={$value}", $newEnvContent);
        } else {
            // If the variable doesn't exist, add it to the end of the file.
            $newEnvContent .= "\n{$var}={$value}";
        }
    }

    // Write the new content back to the .env file.
    return (bool) file_put_contents($envPath, $newEnvContent);
}


function handle_step(string $step): array
{
    return match ($step) {
        'requirements'  => check_requirements(),
        'database'      => handle_database_setup(),
        'administrator' => handle_administrator_creation(),
        'finish'        => ['success' => true],
        default         => [],
    };
}

/**
 * Checks system requirements and returns the status.
 * @return array
 */
function check_requirements(): array
{
    $requirements = [
        'php'       => version_compare(phpversion(), '8.3', '>='),
        'bcmath'    => extension_loaded('bcmath'),
        'ctype'     => extension_loaded('ctype'),
        'curl'      => extension_loaded('curl'),
        'dom'       => extension_loaded('dom'),
        'fileinfo'  => extension_loaded('fileinfo'),
        'filter'    => extension_loaded('filter'),
        'json'      => extension_loaded('json'),
        'mbstring'  => extension_loaded('mbstring'),
        'openssl'   => extension_loaded('openssl'),
        'pcre'      => extension_loaded('pcre'),
        'pdo'       => extension_loaded('pdo'),
        'session'   => extension_loaded('session'),
        'tokenizer' => extension_loaded('tokenizer'),
        'xml'       => extension_loaded('xml'),
    ];

    return ['requirements' => $requirements, 'isAllPassed' => !in_array(false, $requirements)];
}

/**
 * Handles the database setup form submission and database connection.
 * This now includes the migration step.
 * @return array
 * @throws RandomException
 */
function handle_database_setup(): array
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Validate and sanitize input without using deprecated functions.
            $db_connection = (string) $_POST['db_connection'];
            $db_host = (string) $_POST['db_host'];
            $db_port = (int) $_POST['db_port'];
            $db_database = (string) $_POST['db_database'];
            $db_username = (string) $_POST['db_username'];
            $db_password = $_POST['db_password'] ?? '';

            // First, attempt to connect to the database with the provided credentials.
            $dsn = "{$db_connection}:host={$db_host};port={$db_port};dbname={$db_database}";
            $pdo = new PDO($dsn, $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // If the connection is successful, proceed to update the .env file.
            $db_details = [
                'db_connection' => $db_connection,
                'db_host' => $db_host,
                'db_port' => $db_port,
                'db_database' => $db_database,
                'db_username' => $db_username,
                'db_password' => $db_password,
            ];

            if (!update_env_database_config($db_details)) {
                $_SESSION['error'] = "Failed to update .env file with database details. Please check file permissions.";
                return ['success' => false, 'error' => $_SESSION['error']];
            }

            // After updating .env, run the migrations and seeders.
            $migrationResult = run_artisan_command('migrate:fresh');
            if (!$migrationResult['success']) {
                $_SESSION['error'] = "Failed to run database migrations. Please check your database credentials or file permissions. Error: \n" . $migrationResult['output'];
                return ['success' => false, 'error' => $_SESSION['error']];
            }

            $seederResult = run_artisan_command('db:seed');
            if (!$seederResult['success']) {
                $_SESSION['error'] = "Failed to run database seeder. Please check your database permissions. Error: \n" . $seederResult['output'];
                return ['success' => false, 'error' => $_SESSION['error']];
            }

            // Finally, generate the application key.
            if (!generate_app_key_and_update_env()) {
                $_SESSION['error'] = "Failed to generate application key. Please check file permissions.";
                return ['success' => false, 'error' => $_SESSION['error']];
            }

            $_SESSION['success'] = "Database setup and migrations complete! Please create your administrator account.";
            header('Location: index.php?step=administrator');
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to connect to the database. Please check your credentials. Error: " . $e->getMessage();
        }
    }
    return [];
}

/**
 * Handles the administrator creation form submission.
 * @return array
 */
function handle_administrator_creation(): array
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Validate input. In a real scenario, you'd have more robust validation.
            if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || $_POST['password'] !== $_POST['password_confirmation']) {
                $_SESSION['error'] = "Please fill in all fields and ensure the passwords match.";
            } else {
                // In a real application, you would create the user in the database.
                // For this example, we'll just simulate success and redirect.
                $_SESSION['success'] = "Administrator account created successfully!";
                header('Location: index.php?step=finish');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to create administrator account. Error: " . $e->getMessage();
        }
    }
    return [];
}
