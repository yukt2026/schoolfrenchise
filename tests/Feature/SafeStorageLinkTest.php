<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SafeStorageLinkTest extends TestCase
{
    use DatabaseTransactions;

    public function test_artisan_storage_link_runs_without_exec_errors(): void
    {
        $exitCode = $this->artisan('storage:link', ['--force' => true])->run();
        $this->assertEquals(0, $exitCode);
    }

    public function test_admin_fix_storage_link_route_syncs_directories_safely(): void
    {
        $admin = User::where('role', 'super_admin')->first() ?? User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Place a test file in storage/app/public/test_safe.txt
        $testStorageDir = storage_path('app/public');
        File::ensureDirectoryExists($testStorageDir);
        File::put($testStorageDir . '/test_safe.txt', 'Safe storage sync content');

        $response = $this->actingAs($admin)->get(route('admin.fix-storage-link'));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verify test file was synced into public/storage
        $this->assertFileExists(public_path('storage/test_safe.txt'));
        $this->assertEquals('Safe storage sync content', File::get(public_path('storage/test_safe.txt')));

        // Cleanup test file
        File::delete($testStorageDir . '/test_safe.txt');
        File::delete(public_path('storage/test_safe.txt'));
    }

    public function test_public_storage_fallback_route_serves_files(): void
    {
        $testStorageDir = storage_path('app/public/test_unit');
        File::ensureDirectoryExists($testStorageDir);
        File::put($testStorageDir . '/unit.txt', 'Sample text file');

        $response = $this->get('/storage/test_unit/unit.txt');

        $response->assertStatus(200);

        // Cleanup
        File::deleteDirectory($testStorageDir);
    }
}
