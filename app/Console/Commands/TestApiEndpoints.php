<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class TestApiEndpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test {--host=http://127.0.0.1:8000 : Base URL for API testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test semua API endpoints dengan laporan lengkap dan tampilan yang informatif';

    /**
     * Test results storage
     */
    private array $testResults = [];
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;
    private string $baseUrl;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->baseUrl = $this->option('host');
        
        $this->displayHeader();
        $this->checkServerConnection();
        
        if ($this->confirm('Lanjutkan dengan testing semua API endpoints?', true)) {
            $this->runAllTests();
            $this->displaySummary();
        } else {
            $this->info('Testing dibatalkan.');
        }
    }

    /**
     * Display header
     */
    private function displayHeader(): void
    {
        $this->newLine();
        $this->line('--------------------------------------------------------------------------------');
        $this->line('🚀 JHIC API ENDPOINT TESTER');
        $this->line('Comprehensive API Testing & Validation');
        $this->line('--------------------------------------------------------------------------------');
        $this->newLine();
        
        $this->info("🌐 Base URL: {$this->baseUrl}");
        $this->info("📅 Waktu Test: " . now()->format('Y-m-d H:i:s'));
        $this->newLine();
    }

    /**
     * Check server connection
     */
    private function checkServerConnection(): void
    {
        $this->info('🔍 Memeriksa koneksi server...');
        
        try {
            $response = Http::timeout(10)->get($this->baseUrl);
            
            if ($response->successful()) {
                $this->info('✅ Server berjalan dan dapat diakses');
            } else {
                $this->error("❌ Server merespons dengan status: {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Tidak dapat terhubung ke server: {$e->getMessage()}");
        }
    }

    /**
     * Run all API tests
     */
    private function runAllTests(): void
    {
        $this->line('🧪 Starting comprehensive API testing...');
        $this->newLine();

        // Test scenarios
        $tests = [
            ['method' => 'GET', 'endpoint' => '/api/products', 'description' => 'Mengambil semua produk'],
            ['method' => 'POST', 'endpoint' => '/api/products', 'description' => 'Membuat produk baru'],
            ['method' => 'GET', 'endpoint' => '/api/products/{id}', 'description' => 'Mengambil produk tertentu'],
            ['method' => 'PUT', 'endpoint' => '/api/products/{id}', 'description' => 'Mengupdate produk'],
            ['method' => 'DELETE', 'endpoint' => '/api/products/{id}', 'description' => 'Menghapus produk'],
            ['method' => 'GET', 'endpoint' => '/api/products/999999', 'description' => 'Mengambil produk yang tidak ada (test 404)'],
            ['method' => 'POST', 'endpoint' => '/api/products', 'description' => 'Membuat produk dengan data tidak valid (test validasi)'],
        ];

        $progressBar = $this->output->createProgressBar(count($tests));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Memulai test...');
        $progressBar->start();

        foreach ($tests as $index => $test) {
            $progressBar->setMessage("Test: {$test['description']}");
            $this->runSingleTest($test, $index + 1);
            $progressBar->advance();
            usleep(500000); // 0.5 second delay for better UX
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Run a single test
     */
    private function runSingleTest(array $test, int $testNumber): void
    {
        $this->totalTests++;
        $startTime = microtime(true);
        
        try {
            $result = match($test['method']) {
                'GET' => $this->testGetEndpoint($test),
                'POST' => $this->testPostEndpoint($test),
                'PUT' => $this->testPutEndpoint($test),
                'DELETE' => $this->testDeleteEndpoint($test),
                default => ['success' => false, 'message' => 'Unknown method']
            };
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            $result['test_number'] = $testNumber;
            $result['duration'] = $duration;
            $result['test_info'] = $test;
            
            $this->testResults[] = $result;
            
            if ($result['success']) {
                $this->passedTests++;
            } else {
                $this->failedTests++;
            }
            
        } catch (\Exception $e) {
            $this->failedTests++;
            $this->testResults[] = [
                'test_number' => $testNumber,
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'test_info' => $test,
                'duration' => 0
            ];
        }
    }

    /**
     * Test GET endpoints
     */
    private function testGetEndpoint(array $test): array
    {
        $url = $this->baseUrl . $test['endpoint'];
        
        $this->line("🔄 Menjalankan test: GET {$test['endpoint']} - {$test['description']}");
        $startTime = microtime(true);
        
        // Handle dynamic ID replacement
        if (str_contains($url, '{id}')) {
            $product = Product::first();
            if ($product) {
                $url = str_replace('{id}', $product->id, $url);
                $this->line("   📝 Menggunakan produk ID: {$product->id}");
            } else {
                $this->line("   ❌ Tidak ada produk untuk test ID");
                return ['success' => false, 'message' => 'Tidak ada produk untuk test ID'];
            }
        }
        
        $response = Http::get($url);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($response->successful()) {
            $data = $response->json();
            $count = is_array($data['data']) ? count($data['data']) : 1;
            $this->line("   ✅ Berhasil mengambil data ({$count} item)");
            $this->line("   ⏱️  Durasi: {$duration}ms");
            return [
                'success' => true,
                'message' => "✅ GET request berhasil",
                'status_code' => $response->status(),
                'response_data' => $data
            ];
        } else {
            // Check if this is a 404 test (expected behavior)
            if ($response->status() === 404 && (str_contains($test['description'], '404 test') || str_contains($test['description'], 'test 404'))) {
                $this->line("   ✅ Test 404 berhasil (tidak ditemukan sesuai harapan)");
                $this->line("   ⏱️  Durasi: {$duration}ms");
                return [
                    'success' => true,
                    'message' => "✅ Test 404 berhasil (tidak ditemukan sesuai harapan)",
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ];
            }
            
            $this->line("   ❌ Gagal mengambil data dengan status: {$response->status()}");
            $this->line("   ⏱️  Durasi: {$duration}ms");
            return [
                'success' => false,
                'message' => "❌ GET request gagal",
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ];
        }
    }



    /**
     * Test POST endpoints
     */
    private function testPostEndpoint(array $test): array
    {
        $url = $this->baseUrl . $test['endpoint'];
        
        $this->line("🔄 Menjalankan test: POST {$test['endpoint']} - {$test['description']}");
        $startTime = microtime(true);
        
        // Different test data based on test description
        if (str_contains($test['description'], 'invalid data') || str_contains($test['description'], 'tidak valid') || str_contains($test['description'], 'validasi')) {
            $testData = [
                'name' => '', // Empty name should trigger validation
                'price' => 'invalid_price', // Invalid price format
                'stock' => -5 // Negative stock should be invalid
            ];
        } else {
            $testData = [
                'name' => 'Test Product API ' . now()->timestamp,
                'description' => 'Product created by API test script',
                'price' => 99.99,
                'stock' => 50,
                'status' => 'active'
            ];
        }
        
        $response = Http::post($url, $testData);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if (str_contains($test['description'], 'invalid data') || str_contains($test['description'], 'tidak valid') || str_contains($test['description'], 'validasi')) {
            // For validation test, we expect 422 status
            if ($response->status() === 422) {
                $this->line("   ✅ Test validasi berhasil (status 422 sesuai harapan)");
                $this->line("   ⏱️  Durasi: {$duration}ms");
                return [
                    'success' => true,
                    'message' => "✅ Test validasi berhasil (status 422 sesuai harapan)",
                    'status_code' => $response->status(),
                    'response_data' => $response->json()
                ];
            } else {
                $this->line("   ❌ Test validasi gagal (diharapkan 422, mendapat {$response->status()})");
                $this->line("   ⏱️  Durasi: {$duration}ms");
                return [
                    'success' => false,
                    'message' => "❌ Test validasi gagal (diharapkan 422, mendapat {$response->status()})",
                    'status_code' => $response->status()
                ];
            }
        } else {
            if ($response->status() === 201) {
                $data = $response->json();
                $productId = $data['data']['id'] ?? 'unknown';
                $this->line("   ✅ Berhasil membuat produk dengan ID: {$productId}");
                $this->line("   ⏱️  Durasi: {$duration}ms");
                return [
                    'success' => true,
                    'message' => "✅ POST request berhasil",
                    'status_code' => $response->status(),
                    'response_data' => $response->json()
                ];
            } else {
                $this->line("   ❌ Gagal membuat produk dengan status: {$response->status()}");
                $this->line("   ⏱️  Durasi: {$duration}ms");
                return [
                    'success' => false,
                    'message' => "❌ POST request gagal",
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ];
            }
        }
    }

    /**
     * Test PUT endpoints
     */
    private function testPutEndpoint(array $test): array
    {
        $product = Product::first();
        if (!$product) {
            $this->line("   ❌ Tidak ada produk untuk test PUT");
            return ['success' => false, 'message' => 'Tidak ada produk untuk test PUT'];
        }
        
        $url = $this->baseUrl . str_replace('{id}', $product->id, $test['endpoint']);
        
        $this->line("🔄 Menjalankan test: PUT {$test['endpoint']} - {$test['description']}");
        $this->line("   📝 Mengupdate produk ID: {$product->id}");
        $startTime = microtime(true);
        
        $updateData = [
            'name' => 'Updated Product ' . now()->timestamp,
            'price' => 149.99,
            'stock' => 75,
            'status' => 'active'
        ];
        
        $response = Http::put($url, $updateData);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($response->successful()) {
            $this->line("   ✅ Berhasil mengupdate produk");
            $this->line("   ⏱️  Durasi: {$duration}ms");
            return [
                'success' => true,
                'message' => "✅ PUT request berhasil",
                'status_code' => $response->status(),
                'response_data' => $response->json()
            ];
        } else {
            $this->line("   ❌ Gagal mengupdate produk dengan status: {$response->status()}");
            $this->line("   ⏱️  Durasi: {$duration}ms");
            return [
                'success' => false,
                'message' => "❌ PUT request gagal",
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ];
        }
    }

    /**
     * Test DELETE endpoints
     */
    private function testDeleteEndpoint(array $test): array
    {
        $this->line("🔄 Menjalankan test: DELETE {$test['endpoint']} - {$test['description']}");
        $this->line("   📦 Membuat produk test untuk dihapus...");
        
        // Create a test product to delete
        $product = Product::create([
            'name' => 'Test Product for Deletion ' . now()->timestamp,
            'description' => 'This product will be deleted by API test',
            'price' => 1.00,
            'stock' => 1,
            'is_active' => true
        ]);
        
        $url = $this->baseUrl . str_replace('{id}', $product->id, $test['endpoint']);
        
        $this->line("   🗑️  Menghapus produk ID: {$product->id}");
        $startTime = microtime(true);
        
        $response = Http::delete($url);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($response->successful()) {
            $this->line("   ✅ Berhasil menghapus produk");
            $this->line("   ⏱️  Durasi: {$duration}ms");
            return [
                'success' => true,
                'message' => "✅ DELETE request berhasil",
                'status_code' => $response->status(),
                'response_data' => $response->json()
            ];
        } else {
            $this->line("   ❌ Gagal menghapus produk dengan status: {$response->status()}");
            $this->line("   ⏱️  Durasi: {$duration}ms");
            return [
                'success' => false,
                'message' => "❌ DELETE request gagal",
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ];
        }
    }

    /**
     * Display comprehensive test summary
     */
    private function displaySummary(): void
    {
        $this->newLine();
        $this->line('--------------------------------------------------------------------------------');
        $this->line('📊 RINGKASAN TEST');
        $this->line('--------------------------------------------------------------------------------');
        $this->newLine();

        // Overall statistics
        $successRate = $this->totalTests > 0 ? round(($this->passedTests / $this->totalTests) * 100, 1) : 0;
        $this->info("📈 Statistik Keseluruhan:");
        $this->line("   Total Test: {$this->totalTests}");
        $this->line("   ✅ Berhasil: {$this->passedTests}");
        $this->line("   ❌ Gagal: {$this->failedTests}");
        $this->line("   📊 Tingkat Keberhasilan: {$successRate}%");
        $this->newLine();

        // Detailed test results
        $this->info("📋 Hasil Test Detail:");
        $this->newLine();

        foreach ($this->testResults as $result) {
            $status = $result['success'] ? '✅ BERHASIL' : '❌ GAGAL';
            $method = $result['test_info']['method'];
            $endpoint = $result['test_info']['endpoint'];
            $description = $result['test_info']['description'];
            $duration = $result['duration'] ?? 0;
            
            $this->line("#{$result['test_number']} {$status} | {$method} {$endpoint}");
            $this->line("    📝 {$description}");
            $this->line("    ⏱️  Durasi: {$duration}ms");
            
            if (isset($result['status_code'])) {
                $this->line("    📡 Status Code: {$result['status_code']}");
            }
            
            $this->line("    💬 {$result['message']}");
            $this->newLine();
        }

        // Recommendations
        $this->displayRecommendations();
        
        // Footer
        $this->line('--------------------------------------------------------------------------------');
        $this->line('🎉 Testing API Selesai!');
        $this->line('Terima kasih telah menggunakan JHIC API Tester');
        $this->line('--------------------------------------------------------------------------------');
        $this->newLine();
    }

    /**
     * Display recommendations based on test results
     */
    private function displayRecommendations(): void
    {
        $this->info("💡 Rekomendasi & Analisis:");
        
        if ($this->failedTests === 0) {
            $this->line("   🎉 Sempurna! Semua endpoint API berfungsi dengan baik.");
            $this->line("   🚀 API Anda siap untuk digunakan di production.");
        } else {
            $this->line("   ⚠️  Beberapa test gagal. Silakan periksa hal berikut:");
            
            foreach ($this->testResults as $result) {
                if (!$result['success']) {
                    $this->line("   • Perbaiki {$result['test_info']['method']} {$result['test_info']['endpoint']}");
                }
            }
        }
        
        $this->newLine();
        $this->line("📚 Dokumentasi API:");
        $this->line("   • GET /api/products - Mengambil semua produk");
        $this->line("   • POST /api/products - Membuat produk baru");
        $this->line("   • GET /api/products/{id} - Mengambil produk tertentu");
        $this->line("   • PUT /api/products/{id} - Mengupdate produk");
        $this->line("   • DELETE /api/products/{id} - Menghapus produk");
        $this->newLine();
    }
}
