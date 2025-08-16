<?php
// FILE: public/installer/views/requirements.html.php
// View for the system requirements step.
defined('INSTALLER_PATH') or die('Direct access to this file is not allowed.');

$requirements = $data['requirements'] ?? [];
$isAllPassed = $data['isAllPassed'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Requirements - Neon Installer</title>
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
<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">System Requirements Check</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($requirements as $extension => $status): ?>
            <div class="flex items-center justify-between p-4 rounded-lg <?php echo $status ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
                <span class="font-medium"><?php echo $extension; ?></span>
                <?php if ($status): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-8 text-center">
        <?php if ($isAllPassed): ?>
            <a href="index.php?step=database" class="inline-block w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                Continue
            </a>
        <?php else: ?>
            <button disabled class="inline-block w-full bg-gray-400 text-white font-bold py-3 px-6 rounded-lg cursor-not-allowed">
                Continue
            </button>
            <p class="text-red-500 text-sm mt-4">Some requirements are not met. Please fix them before continuing.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
