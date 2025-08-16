<?php
// FILE: public/installer/views/database.html.php
// View for the database configuration step.
defined('INSTALLER_PATH') or die('Direct access to this file is not allowed.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration - Neon Installer</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        input, select {
            transition: all 0.2s ease-in-out;
        }
        input:focus, select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Database Configuration</h1>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">An error occurred: <?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    <form method="POST" action="index.php?step=database" class="space-y-6">
        <div>
            <label for="db_connection" class="block text-sm font-medium text-gray-700 mb-1">Connection Type</label>
            <select id="db_connection" name="db_connection" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="mysql">MySQL</option>
                <option value="pgsql">PostgreSQL</option>
            </select>
            <p class="mt-1 text-sm text-gray-500">Select your database connection type.</p>
        </div>
        <!-- Two-column layout for host and port -->
        <div class="md:grid md:grid-cols-2 md:gap-6">
            <div>
                <label for="db_host" class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="127.0.0.1" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., localhost or 127.0.0.1">
                <p class="mt-1 text-sm text-gray-500">The hostname or IP address of your database server.</p>
            </div>
            <div>
                <label for="db_port" class="block text-sm font-medium text-gray-700 mb-1">Database Port</label>
                <input type="text" id="db_port" name="db_port" value="3306" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 3306">
                <p class="mt-1 text-sm text-gray-500">The port number for your database connection.</p>
            </div>
        </div>
        <div>
            <label for="db_database" class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
            <input type="text" id="db_database" name="db_database" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., neon_app">
            <p class="mt-1 text-sm text-gray-500">The name of the database you want to use.</p>
        </div>
        <div>
            <label for="db_username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" id="db_username" name="db_username" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., root">
            <p class="mt-1 text-sm text-gray-500">The username to connect to your database.</p>
        </div>
        <div>
            <label for="db_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="db_password" name="db_password" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Leave blank if no password">
            <p class="mt-1 text-sm text-gray-500">The password for the database user.</p>
        </div>
        <div class="text-center mt-8">
            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                Connect & Continue
            </button>
        </div>
    </form>
</div>
</body>
</html>
