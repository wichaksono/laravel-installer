<?php
// FILE: public/installer/views/welcome.html.php
// Welcome view for the installer.
defined('INSTALLER_PATH') or die('Direct access to this file is not allowed.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neon Installer</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="p-10 rounded-xl shadow-2xl w-full max-w-lg mx-auto transform transition-all duration-300 hover:scale-105 bg-white bg-opacity-90 backdrop-filter backdrop-blur-md">
    <h1 class="text-3xl font-bold mb-3 text-gray-800 tracking-tight text-center">Welcome to Neon</h1>
    <p class="text-gray-500 mb-6 text-sm text-center">Let's start the installation process.</p>
    <div class="text-center">
        <a href="installer/index.php?step=requirements" class="inline-block w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
            Start Installation
        </a>
    </div>
</div>
</body>
</html>
