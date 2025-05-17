<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Requirements - NeonWebId Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">System Requirements</h1>

        <div class="mb-6">
            <h2 class="text-xl mb-2">PHP Version</h2>
            <div class="flex items-center">
                <span class="mr-2">PHP >= 8.1.0:</span>
                @if($requirements['php'])
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @else
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @endif
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-xl mb-2">Required Extensions</h2>
            @foreach($requirements['extensions'] as $extension => $installed)
                <div class="flex items-center mb-2">
                    <span class="mr-2">{{ $extension }}:</span>
                    @if($installed)
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex justify-between">
            <a href="{{ route('installer.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back
            </a>
            @if(!in_array(false, $requirements['extensions']) && $requirements['php'])
                <a href="{{ route('installer.configuration') }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Continue
                </a>
            @endif
        </div>

        <div class="mt-4 text-sm text-gray-500">
            © {{ date('Y') }} NeonWebId - Created by {{ '@wichaksono' }}
        </div>
    </div>
</div>
</body>
</html>
