<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use App\Models\Student;

class FetchStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch student data from external API and save to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = env('API_BASE_URL');
        $username = env('API_USERNAME');
        $password = env('API_PASSWORD');
        $apiKey = env('API_KEY');

        if (!$baseUrl || !$username || !$password || !$apiKey) {
             $this->error("Missing API credentials in .env");
             return;
        }

        $this->info("Logging in to $baseUrl...");
        
        try {
            $loginResponse = Http::withHeaders([
                'apikey' => $apiKey
            ])->post("$baseUrl/auth/v2/login", [
                'username' => $username,
                'password' => $password
            ]);

            if ($loginResponse->failed()) {
                $this->error('Login failed: ' . $loginResponse->body());
                return;
            }

            $token = $loginResponse->json('data.token');
            if (!$token) {
                 $this->error('No token received in login response.');
                 return;
            }

            $this->info("Logged in successfully. Fetching data...");

            // Try fetching all data
            // Note: The Postman file used a body with filters for check/all, but here we try without first.
            // If the API requires filters, we might need to add them.
            // We'll also use the token.
            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Authorization' => "Bearer $token"
            ])->get("$baseUrl/beasiswa/v2/operator/check/all");

            if ($response->failed()) {
                $this->error('Fetch failed: ' . $response->body());
                return;
            }

            $data = $response->json('data');
            
            if (!is_array($data)) {
                 $this->error('Invalid data format received.');
                 return;
            }

            $count = count($data);
            $this->info("Fetched $count records. Saving to database...");

            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($data as $record) {
                Student::updateOrCreate(
                    ['nik' => $record['nik']],
                    [
                        'no_kk' => $record['no_kk'] ?? null,
                        'nim' => $record['nim'] ?? null,
                        'name' => $record['name'],
                        'is_nik_valid' => $record['is_nik_valid'] ?? null,
                        'is_valid_by_univ' => $record['is_valid_by_univ'] ?? null,
                        'status' => $record['status'] ?? null,
                        'status_desc' => $record['status_desc'] ?? null,
                        'status_id' => $record['status_id'] ?? null,
                        'pt_name' => $record['pt_name'] ?? null,
                        'prodi_name' => $record['prodi_name'] ?? null,
                        'jenjang' => $record['jenjang'] ?? null,
                        'jalur_masuk' => $record['jalur_masuk'] ?? null,
                        'max_spp' => $record['max_spp'] ?? null,
                        'spp_ditanggung' => $record['spp_ditanggung'] ?? null,
                        'finalized_at' => $record['finalized_at'] ? \Carbon\Carbon::parse($record['finalized_at']) : null,
                        'notes' => $record['notes'] ?? null,
                        'batch_accepted' => $record['batch_accepted'] ?? null,
                    ]
                );
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Data saved successfully.");

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
