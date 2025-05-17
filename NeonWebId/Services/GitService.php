<?php

namespace Modules\NeonWebId\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * GitService Class
 * Handles Git operations including auto pull for private repositories
 *
 * @created 2025-05-17 07:44:54
 * @author wichaksono
 * @package Modules\NeonWebId\Services
 */
class GitService
{
    /**
     * Execute git pull command
     *
     * @param string $repository Repository path
     * @param string|null $branch Branch name (default: current branch)
     * @param array $credentials Git credentials ['username' => '', 'token' => '']
     * @return array ['success' => bool, 'message' => string, 'output' => string]
     */
    public function pull(string $repository, ?string $branch = null, array $credentials = []): array
    {
        try {
            // Validate repository path
            if (!file_exists($repository . '/.git')) {
                return [
                    'success' => false,
                    'message' => 'Not a git repository',
                    'output' => ''
                ];
            }

            // Build command array
            $commands = ['git'];

            // Add credentials if provided
            if (!empty($credentials)) {
                $gitUrl = $this->getRemoteUrl($repository);
                $secureUrl = $this->addCredentialsToUrl($gitUrl, $credentials);
                $commands[] = '-c';
                $commands[] = "url.$secureUrl.insteadOf=$gitUrl";
            }

            $commands = array_merge($commands, ['pull', 'origin']);

            // Add branch if specified
            if ($branch) {
                $commands[] = $branch;
            }

            // Create and configure process
            $process = new Process($commands);
            $process->setWorkingDirectory($repository);
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            // Handle process result
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            return [
                'success' => true,
                'message' => 'Successfully pulled changes',
                'output' => $process->getOutput()
            ];

        } catch (\Exception $e) {
            Log::error('Git pull failed', [
                'repository' => $repository,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'output' => $e instanceof ProcessFailedException ? $e->getProcess()->getErrorOutput() : ''
            ];
        }
    }

    /**
     * Get remote URL for repository
     *
     * @param string $repository Repository path
     * @return string
     */
    private function getRemoteUrl(string $repository): string
    {
        $process = new Process(['git', 'config', '--get', 'remote.origin.url']);
        $process->setWorkingDirectory($repository);
        $process->run();

        return trim($process->getOutput());
    }

    /**
     * Add credentials to Git URL
     *
     * @param string $url Original Git URL
     * @param array $credentials ['username' => '', 'token' => '']
     * @return string
     */
    private function addCredentialsToUrl(string $url, array $credentials): string
    {
        $pattern = '/^(https?:\/\/)(.+)$/';
        return preg_replace(
            $pattern,
            sprintf(
                '$1%s:%s@$2',
                $credentials['username'],
                $credentials['token']
            ),
            $url
        );
    }
}
