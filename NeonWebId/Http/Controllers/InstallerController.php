<?php

namespace Modules\NeonWebId\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use PDO;

/**
 * InstallerController
 * @created 2025-05-17 07:19:27
 * @author wichaksono
 * @package Modules\NeonWebId\Http\Controllers
 */
class InstallerController extends Controller
{
    /**
     * System requirements
     * @var array
     */
    protected $requirements = [
        'php'        => '8.1.0',
        'extensions' => [
            'MySQLi',
            'BCMath',
            'Ctype',
            'Fileinfo',
            'JSON',
            'Mbstring',
            'OpenSSL',
            'PDO',
            'Tokenizer',
            'XML',
        ]
    ];

    /**
     * Show welcome page
     *
     * @return View|RedirectResponse
     */
    public function index()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return view('neonwebid::welcome');
    }

    /**
     * Check system requirements
     *
     * @return View
     */
    public function requirements()
    {
        $requirements = $this->checkRequirements();
        return view('neonwebid::requirements', compact('requirements'));
    }

    /**
     * Show configuration form
     *
     * @return View|RedirectResponse
     */
    public function configuration()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return view('neonwebid::configuration');
    }

    /**
     * Handle installation setup
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setup(Request $request)
    {
        $request->validate([
            'app_name' => 'required',
            'db_host' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_password' => 'required',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            // Test database connection
            $connection = $this->testDatabaseConnection($request);
            if (!$connection['success']) {
                return back()->withErrors(['database' => $connection['message']]);
            }

            // Create .env file
            $this->createEnvFile($request);

            // Run Laravel migrations
            Artisan::call('migrate:fresh', ['--force' => true]);

            // Run module migrations
            $this->runModuleMigrations();

            // Create admin user
            $this->createAdminUser($request);

            // Mark as installed
            $this->markAsInstalled();

            return redirect('/')->with('success', 'Application installed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Run module migrations
     *
     * @return void
     */
    protected function runModuleMigrations()
    {
        $migrationPath = __DIR__ . '/../../Database/Migrations';

        // Get all migration files
        $migrations = File::glob($migrationPath . '/*.php');

        foreach ($migrations as $migration) {
            // Include migration file
            require_once $migration;

            // Get migration class name
            $className = 'Modules\\NeonWebId\\Database\\Migrations\\' . pathinfo($migration, PATHINFO_FILENAME);

            // Run migration
            $migration = new $className();
            $migration->up();
        }
    }

    /**
     * Check if application is installed
     *
     * @return bool
     */
    protected function isInstalled()
    {
        return file_exists(storage_path('installed'));
    }

    /**
     * Check system requirements
     *
     * @return array
     */
    protected function checkRequirements()
    {
        $results        = [];
        $results['php'] = version_compare(PHP_VERSION, $this->requirements['php'], '>=');

        foreach ($this->requirements['extensions'] as $extension) {
            $results['extensions'][$extension] = extension_loaded($extension);
        }

        return $results;
    }

    /**
     * Create .env file from example
     *
     * @param Request $request
     *
     * @return void
     */
    protected function createEnvFile(Request $request)
    {
        $envExample = base_path('.env.example');
        $env        = base_path('.env');

        if (file_exists($envExample)) {
            copy($envExample, $env);
        }

        $this->updateEnvFile([
            'APP_NAME'    => '"' . $request->app_name . '"',
            'DB_HOST'     => $request->db_host,
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_user,
            'DB_PASSWORD' => $request->db_password,
        ]);
    }

    /**
     * Update environment file
     *
     * @param array $data
     *
     * @return void
     */
    protected function updateEnvFile($data)
    {
        $envFile     = base_path('.env');
        $envContents = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $envContents = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContents
            );
        }

        file_put_contents($envFile, $envContents);
    }

    /**
     * Test database connection
     *
     * @param Request $request
     *
     * @return array
     */
    protected function testDatabaseConnection($request)
    {
        try {
            $config = [
                'host'     => $request->db_host,
                'username' => $request->db_user,
                'password' => $request->db_password,
                'database' => $request->db_name,
            ];

            $connection = DB::connection()->setPdo(
                new PDO(
                    "mysql:host={$config['host']};dbname={$config['database']}",
                    $config['username'],
                    $config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                )
            );

            $connection->getPdo();

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create admin user
     *
     * @param Request $request
     *
     * @return void
     */
    protected function createAdminUser($request)
    {
        $user = config('auth.providers.users.model');
        $user::create([
            'name'     => 'Administrator',
            'email'    => $request->admin_email,
            'password' => bcrypt($request->admin_password),
        ]);
    }

    /**
     * Mark application as installed
     *
     * @return void
     */
    protected function markAsInstalled()
    {
        file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));
    }
}
