<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - NeonWebId Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">Welcome to Installation</h1>
        <p class="mb-4">This wizard will help you install and configure your application.</p>

        <div class="mb-4">
            <p class="text-gray-600">Before proceeding, please make sure:</p>
            <ul class="list-disc ml-6">
                <li>Your server meets the requirements</li>
                <li>You have database credentials ready</li>
                <li>You have necessary permissions to create files and directories</li>
            </ul>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('installer.requirements') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Continue
            </a>
        </div>

        <div class="mt-4 text-sm text-gray-500">
            © {{ date('Y') }} NeonWebId - Created by {{ '@wichaksono' }}
        </div>
    </div>
</div>
</body>
</html>
