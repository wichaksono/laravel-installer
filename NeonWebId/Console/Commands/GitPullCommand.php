<?php

namespace Modules\NeonWebId\Console\Commands;

use Illuminate\Console\Command;
use Modules\NeonWebId\Services\GitService;

class GitPullCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:pull
                            {repository? : Repository path or name from config}
                            {--branch= : Branch to pull}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull changes from git repository';

    /**
     * Execute the console command.
     */
    public function handle(GitService $git)
    {
        $repository = $this->argument('repository');
        $branch = $this->option('branch');

        // If no repository specified, pull all from config
        if (!$repository) {
            return $this->pullAllFromConfig($git);
        }

        // Check if repository is configured
        $config = config("neonwebid.git.repositories.$repository");
        if ($config) {
            $result = $git->pull(
                $config['path'],
                $branch ?? $config['branch'] ?? null,
                $config['credentials'] ?? []
            );
        } else {
            // Treat input as direct path
            $result = $git->pull($repository, $branch);
        }

        if ($result['success']) {
            $this->info($result['message']);
            $this->line($result['output']);
        } else {
            $this->error($result['message']);
            if ($result['output']) {
                $this->error($result['output']);
            }
        }

        return $result['success'] ? 0 : 1;
    }

    /**
     * Pull all repositories from config
     */
    private function pullAllFromConfig(GitService $git): int
    {
        $success = true;
        $repositories = config('neonwebid.git.repositories', []);

        foreach ($repositories as $name => $config) {
            $this->info("Pulling $name...");

            $result = $git->pull(
                $config['path'],
                $config['branch'] ?? null,
                $config['credentials'] ?? []
            );

            if ($result['success']) {
                $this->info("✓ $name: " . $result['message']);
            } else {
                $this->error("✗ $name: " . $result['message']);
                $success = false;
            }
        }

        return $success ? 0 : 1;
    }
}
