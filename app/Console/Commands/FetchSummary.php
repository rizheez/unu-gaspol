<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use App\Models\Summary;

class FetchSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch summary data from external API and save to database';

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

            $this->info("Logged in successfully. Fetching summary data...");

            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Authorization' => "Bearer $token"
            ])->get("$baseUrl/beasiswa/v2/operator/summary");

            if ($response->failed()) {
                $this->error('Fetch failed: ' . $response->body());
                return;
            }

            $responseData = $response->json();
            
            $records = [];
            
            // Helper function to find records recursively
            $findRecords = function($data) use (&$findRecords, &$records) {
                if (!is_array($data)) return;
                
                // If this item has prodi_code, it's a record
                if (isset($data['prodi_code'])) {
                    $records[] = $data;
                    return; 
                }
                
                // Otherwise iterate children
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $findRecords($value);
                    }
                }
            };
            
            $findRecords($responseData);

            $count = count($records);
            $this->info("Found $count summary records via recursive search. Saving to database...");

            if ($count > 0) {
                $bar = $this->output->createProgressBar($count);
                $bar->start();

                foreach ($records as $record) {
                    Summary::updateOrCreate(
                        ['prodi_code' => $record['prodi_code']],
                        [
                            'prodi_name' => $record['prodi_name'],
                            'prodi_accred' => $record['prodi_accred'] ?? null,
                            'pt_code' => $record['pt_code'] ?? null,
                            'pt_name' => $record['pt_name'] ?? null,
                            'jenjang' => $record['jenjang'] ?? null,
                            'total_prodi' => $record['total_prodi'] ?? 0,
                            'diumumkan_count' => $record['diumumkan_count'] ?? 0,
                            'sk_tahap_count' => $record['sk_tahap_count'] ?? 0,
                            'is_valid_by_univ_count' => $record['is_valid_by_univ_count'] ?? 0,
                            'diumumkan_by_tahap' => $record['diumumkan_by_tahap'] ?? null,
                            'sk_tahap_by_tahap' => $record['sk_tahap_by_tahap'] ?? null,
                            'status_stats' => $record['status_stats'] ?? null,
                        ]
                    );
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();
                $this->info("Summary data saved successfully.");
            } else {
                $this->warn("No summary records found to save.");
                $this->info("Response keys: " . implode(', ', array_keys($responseData)));
            }

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
