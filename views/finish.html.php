<?php
// FILE: public/installer/views/finish.html.php
// View for the installation finish step.
defined('INSTALLER_PATH') or die('Direct access to this file is not allowed.');

$success = $data['success'] ?? false;
$error = $data['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete - Neon Installer</title>
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
<div class="bg-white p-10 rounded-xl shadow-2xl w-full max-w-lg mx-auto text-center transform transition-all duration-300 hover:scale-105">
    <?php if ($success): ?>
        <h1 class="text-3xl font-bold mb-4 text-green-600 tracking-tight">🎉 Installation Complete!</h1>
        <p class="text-gray-500 mb-6 text-sm">Congratulations! Your system has been successfully installed.</p>
        <a href="/" class="inline-block w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
            Go to Dashboard
        </a>
    <?php else: ?>
        <h1 class="text-3xl font-bold mb-4 text-red-600 tracking-tight">❌ Installation Failed!</h1>
        <p class="text-gray-500 mb-6 text-sm">An error occurred: <?php echo htmlspecialchars($error); ?></p>
        <a href="index.php?step=database" class="inline-block w-full bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105">
            Try Again
        </a>
    <?php endif; ?>
</div>
</body>
</html>
