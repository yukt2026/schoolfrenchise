<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBannerModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'superadmin@drishticomputer.com'],
            [
                'name'      => 'Super Administrator',
                'password'  => bcrypt('SuperSecretAdmin@2026'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->admin->roles()->syncWithoutDetaching([$superAdminRole->id]);
    }

    public function test_guest_cannot_access_banners(): void
    {
        $response = $this->get(route('admin.banners.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_banner_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.banners.index'));
        $response->assertStatus(200);
        $response->assertSee('Hero Banners & Slider Manager', false);
        $response->assertSee('Add New Banner');
    }

    public function test_admin_can_view_create_banner_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.banners.create'));
        $response->assertStatus(200);
        $response->assertSee('Upload Hero Slider Banner');
    }

    public function test_admin_can_store_banner(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test_hero_banner.jpg', 1920, 800);

        $response = $this->actingAs($this->admin)->post(route('admin.banners.store'), [
            'title'      => 'Special New Batch Admission Open 2026',
            'subtitle'   => 'Get up to 50% scholarship on ADCA and DCA courses',
            'image'      => $file,
            'link_url'   => '/enquiry/admission',
            'sort_order' => 1,
            'status'     => 1,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'title'    => 'Special New Batch Admission Open 2026',
            'subtitle' => 'Get up to 50% scholarship on ADCA and DCA courses',
            'status'   => true,
        ]);
    }

    public function test_admin_can_edit_and_update_banner(): void
    {
        Storage::fake('public');

        $banner = Banner::create([
            'title'      => 'Initial Banner Title',
            'subtitle'   => 'Initial Subtitle',
            'image_path' => 'uploads/banners/initial.jpg',
            'sort_order' => 2,
            'status'     => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.banners.edit', $banner));
        $response->assertStatus(200);
        $response->assertSee('Initial Banner Title');

        $newImage = UploadedFile::fake()->image('updated_banner.jpg', 1920, 800);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.banners.update', $banner), [
            'title'      => 'Updated Banner Title 2026',
            'subtitle'   => 'Updated Subtitle',
            'image'      => $newImage,
            'sort_order' => 5,
            'status'     => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'id'    => $banner->id,
            'title' => 'Updated Banner Title 2026',
        ]);
    }

    public function test_admin_can_toggle_banner_status(): void
    {
        $banner = Banner::create([
            'title'      => 'Toggle Banner',
            'image_path' => 'uploads/banners/dummy.jpg',
            'sort_order' => 1,
            'status'     => true,
        ]);

        $this->actingAs($this->admin)->post(route('admin.banners.toggle-status', $banner));
        $this->assertDatabaseHas('banners', ['id' => $banner->id, 'status' => false]);

        $this->actingAs($this->admin)->post(route('admin.banners.toggle-status', $banner));
        $this->assertDatabaseHas('banners', ['id' => $banner->id, 'status' => true]);
    }

    public function test_admin_can_delete_banner(): void
    {
        $banner = Banner::create([
            'title'      => 'Delete Banner',
            'image_path' => 'uploads/banners/dummy.jpg',
            'sort_order' => 1,
            'status'     => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.banners.destroy', $banner));
        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }
}
