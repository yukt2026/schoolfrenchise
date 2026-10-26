<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Banner;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBannerController extends Controller
{
    /**
     * List all hero banners with filters and statistics.
     */
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('subtitle', 'like', "%{$s}%")
                    ->orWhere('link_url', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == '1');
        }

        $banners = $query->orderBy('sort_order', 'asc')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total'    => Banner::count(),
            'active'   => Banner::where('status', true)->count(),
            'inactive' => Banner::where('status', false)->count(),
        ];

        return view('admin.banners.index', compact('banners', 'stats'));
    }

    /**
     * Show form to upload a new banner.
     */
    public function create()
    {
        $nextOrder = (Banner::max('sort_order') ?? 0) + 1;
        return view('admin.banners.create', compact('nextOrder'));
    }

    /**
     * Store newly uploaded banner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'subtitle'          => 'nullable|string|max:255',
            'image'             => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'mobile_image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'link_url'          => 'nullable|string|max:255',
            'link_target'       => 'nullable|in:_self,_blank',
            'sort_order'        => 'nullable|integer|min:0',
            'status'            => 'nullable|boolean',
        ]);

        $imagePath = FileUploadService::uploadImage($request->file('image'), 'uploads/banners', 10240);

        $mobileImagePath = null;
        if ($request->hasFile('mobile_image')) {
            $mobileImagePath = FileUploadService::uploadImage($request->file('mobile_image'), 'uploads/banners', 10240);
        }

        $banner = Banner::create([
            'title'             => $validated['title'],
            'subtitle'          => $validated['subtitle'] ?? null,
            'image_path'        => $imagePath,
            'mobile_image_path' => $mobileImagePath,
            'link_url'          => $validated['link_url'] ?? null,
            'link_target'       => $validated['link_target'] ?? '_self',
            'sort_order'        => $validated['sort_order'] ?? 1,
            'status'            => $request->boolean('status', true),
        ]);

        AuditLog::record('banner_created', $banner, null, $banner->toArray());

        return redirect()->route('admin.banners.index')
            ->with('success', "Hero banner '{$banner->title}' uploaded and published successfully.");
    }

    /**
     * Show form to edit an existing banner.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update an existing banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'subtitle'          => 'nullable|string|max:255',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'mobile_image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'link_url'          => 'nullable|string|max:255',
            'link_target'       => 'nullable|in:_self,_blank',
            'sort_order'        => 'nullable|integer|min:0',
            'status'            => 'nullable|boolean',
        ]);

        $oldData = $banner->toArray();

        $updateData = [
            'title'       => $validated['title'],
            'subtitle'    => $validated['subtitle'] ?? null,
            'link_url'    => $validated['link_url'] ?? null,
            'link_target' => $validated['link_target'] ?? '_self',
            'sort_order'  => $validated['sort_order'] ?? $banner->sort_order,
            'status'      => $request->boolean('status'),
        ];

        if ($request->hasFile('image')) {
            // Delete old file if present
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $updateData['image_path'] = FileUploadService::uploadImage($request->file('image'), 'uploads/banners', 10240);
        }

        if ($request->hasFile('mobile_image')) {
            if ($banner->mobile_image_path && Storage::disk('public')->exists($banner->mobile_image_path)) {
                Storage::disk('public')->delete($banner->mobile_image_path);
            }
            $updateData['mobile_image_path'] = FileUploadService::uploadImage($request->file('mobile_image'), 'uploads/banners', 10240);
        }

        $banner->update($updateData);

        AuditLog::record('banner_updated', $banner, $oldData, $banner->toArray());

        return redirect()->route('admin.banners.index')
            ->with('success', "Hero banner '{$banner->title}' updated successfully.");
    }

    /**
     * Delete a banner.
     */
    public function destroy(Banner $banner)
    {
        $oldData = $banner->toArray();

        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        if ($banner->mobile_image_path && Storage::disk('public')->exists($banner->mobile_image_path)) {
            Storage::disk('public')->delete($banner->mobile_image_path);
        }

        $banner->delete();

        AuditLog::record('banner_deleted', $banner, $oldData, null);

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner '{$oldData['title']}' removed successfully.");
    }

    /**
     * Toggle active/inactive status of a banner.
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->update(['status' => !$banner->status]);

        AuditLog::record('banner_status_toggled', $banner, null, ['status' => $banner->status]);

        return back()->with('success', "Banner status changed to " . ($banner->status ? 'Active' : 'Inactive') . ".");
    }
}
