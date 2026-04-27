<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Exception;

class InstallController extends Controller
{
    public function index()
    {
        return view('install.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'admin_name' => 'required',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            // 1. Test Database Connection
            $this->testConnection($request);

            // 2. Update .env file
            $this->updateEnv($request);

            // 3. Clear Cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            // 4. Run Migrations & Seeds
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            
            // 5. Create Storage Symlink
            try {
                $storageLink = public_path('storage');
                
                // Use Laravel's File facade for safer deletion
                if (\Illuminate\Support\Facades\File::exists($storageLink)) {
                    if (is_link($storageLink)) {
                        unlink($storageLink);
                    } else {
                        \Illuminate\Support\Facades\File::deleteDirectory($storageLink);
                    }
                }
                
                Artisan::call('storage:link');
            } catch (Exception $e) {
                // Log the error if needed
            }

            // 6. Create Admin User
            $this->createAdmin($request);

            // 6. Create Installed Lock File
            File::put(storage_path('installed'), date('Y-m-d H:i:s'));

            return response()->json(['success' => true, 'message' => 'Instalasi berhasil! Silakan login.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    protected function testConnection($request)
    {
        // Set connection configuration dynamically
        config(['database.connections.mysql.host' => $request->db_host]);
        config(['database.connections.mysql.port' => $request->db_port]);
        config(['database.connections.mysql.username' => $request->db_user]);
        config(['database.connections.mysql.password' => $request->db_pass ?? '']);
        config(['database.connections.mysql.database' => null]); // Connect without DB first

        try {
            // Attempt to connect to MySQL server
            DB::reconnect('mysql');
            
            // Create database if not exists
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$request->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            
            // Now connect to the actual database
            config(['database.connections.mysql.database' => $request->db_name]);
            DB::reconnect('mysql');
            DB::connection('mysql')->getPdo();
            
        } catch (Exception $e) {
            throw new Exception("Gagal terhubung ke MySQL: " . $e->getMessage());
        }
    }

    protected function updateEnv($request)
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        if (!File::exists($envPath)) {
            File::copy($envExamplePath, $envPath);
        }

        $content = File::get($envPath);

        $replacements = [
            'DB_HOST' => $request->db_host,
            'DB_PORT' => $request->db_port,
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_user,
            'DB_PASSWORD' => $request->db_pass ?? '',
            'APP_URL' => url('/'),
            'SESSION_DRIVER' => 'file',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'database',
        ];

        foreach ($replacements as $key => $value) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        }

        // Generate Key if empty
        if (strpos($content, 'APP_KEY=') === false || strlen(explode('APP_KEY=', $content)[1]) < 10) {
             Artisan::call('key:generate', ['--show' => true]);
             $key = trim(str_replace('Application key set successfully.', '', Artisan::output()));
             $content = preg_replace("/^APP_KEY=.*/m", "APP_KEY={$key}", $content);
        }

        File::put($envPath, $content);
    }

    protected function createAdmin($request)
    {
        \App\Models\User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'nim' => 'ADMIN',
            'password' => \Illuminate\Support\Facades\Hash::make($request->admin_password),
            'role' => 'superadmin',
            'is_verified' => true,
        ]);
    }
}
