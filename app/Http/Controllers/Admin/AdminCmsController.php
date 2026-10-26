<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Banner;
use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\Notice;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCmsController extends Controller
{
    /**
     * CMS Central Hub Overview
     */
    public function index()
    {
        $stats = [
            'total_banners'   => Banner::count(),
            'active_banners'  => Banner::where('status', true)->count(),
            'total_notices'   => Notice::count(),
            'active_notices'  => Notice::where('status', true)->count(),
            'marquee_count'   => Notice::where('is_marquee', true)->where('status', true)->count(),
            'total_photos'    => Gallery::count(),
            'active_photos'   => Gallery::where('status', true)->count(),
            'total_enquiries' => Enquiry::count(),
            'new_enquiries'   => Enquiry::where('status', 'new')->count(),
        ];

        $recentNotices = Notice::latest()->take(5)->get();
        $recentEnquiries = Enquiry::latest()->take(6)->get();
        $recentGallery = Gallery::latest()->take(6)->get();

        return view('admin.cms.index', compact('stats', 'recentNotices', 'recentEnquiries', 'recentGallery'));
    }

    // ==========================================
    // NOTICES & MARQUEE ANNOUNCEMENTS
    // ==========================================

    public function notices(Request $request)
    {
        $query = Notice::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('content', 'like', "%{$s}%");
            });
        }

        if ($request->filled('audience')) {
            $query->where('target_audience', $request->audience);
        }

        if ($request->filled('is_marquee')) {
            $query->where('is_marquee', $request->is_marquee == '1');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == '1');
        }

        $notices = $query->latest('published_at')->latest('id')->paginate(15)->withQueryString();

        return view('admin.cms.notices', compact('notices'));
    }

    public function createNotice()
    {
        return view('admin.cms.create_notice');
    }

    public function storeNotice(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'is_marquee'      => 'nullable|boolean',
            'target_audience' => 'required|in:all,centres,students,public',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'status'          => 'nullable|boolean',
            'published_at'    => 'required|date',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = FileUploadService::uploadDocument($request->file('attachment'), 'uploads/notices');
        }

        $notice = Notice::create([
            'title'           => $validated['title'],
            'content'         => $validated['content'] ?? null,
            'is_marquee'      => $request->boolean('is_marquee'),
            'target_audience' => $validated['target_audience'],
            'attachment_path' => $attachmentPath,
            'status'          => $request->boolean('status', true),
            'published_at'    => $validated['published_at'],
        ]);

        AuditLog::record('notice_created', $notice, null, $notice->toArray());

        return redirect()->route('admin.cms.notices')
            ->with('success', "Notice '{$notice->title}' published successfully.");
    }

    public function editNotice(Notice $notice)
    {
        return view('admin.cms.edit_notice', compact('notice'));
    }

    public function updateNotice(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'is_marquee'      => 'nullable|boolean',
            'target_audience' => 'required|in:all,centres,students,public',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'status'          => 'nullable|boolean',
            'published_at'    => 'required|date',
        ]);

        $oldData = $notice->toArray();

        $updateData = [
            'title'           => $validated['title'],
            'content'         => $validated['content'] ?? null,
            'is_marquee'      => $request->boolean('is_marquee'),
            'target_audience' => $validated['target_audience'],
            'status'          => $request->boolean('status'),
            'published_at'    => $validated['published_at'],
        ];

        if ($request->hasFile('attachment')) {
            $updateData['attachment_path'] = FileUploadService::uploadDocument($request->file('attachment'), 'uploads/notices');
        }

        $notice->update($updateData);

        AuditLog::record('notice_updated', $notice, $oldData, $notice->toArray());

        return redirect()->route('admin.cms.notices')
            ->with('success', "Notice '{$notice->title}' updated successfully.");
    }

    public function destroyNotice(Notice $notice)
    {
        $oldData = $notice->toArray();
        $notice->delete();

        AuditLog::record('notice_deleted', $notice, $oldData, null);

        return redirect()->route('admin.cms.notices')
            ->with('success', "Notice removed.");
    }

    public function toggleNoticeStatus(Notice $notice)
    {
        $notice->update(['status' => !$notice->status]);

        AuditLog::record('notice_status_toggled', $notice, null, ['status' => $notice->status]);

        return back()->with('success', "Notice status updated to " . ($notice->status ? 'Active' : 'Inactive') . ".");
    }

    // ==========================================
    // GALLERY & CAMPUS VISUALS
    // ==========================================

    public function gallery(Request $request)
    {
        $query = Gallery::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('title', 'like', "%{$s}%");
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == '1');
        }

        $items = $query->orderBy('sort_order')->latest('id')->paginate(18)->withQueryString();
        $categories = Gallery::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.cms.gallery', compact('items', 'categories'));
    }

    public function createGallery()
    {
        $dbCategories = Gallery::distinct()->pluck('category')->filter()->values()->toArray();
        $defaultCategories = ['Certificates', 'Students', 'Computer Lab', 'Events', 'Campus', 'Celebration', 'Convocation', 'Classroom', 'Awards'];
        $categories = array_values(array_unique(array_merge($defaultCategories, $dbCategories)));

        return view('admin.cms.create_gallery', compact('categories'));
    }

    public function storeGallery(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:191',
            'category'   => 'required|string|max:100',
            'image'      => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'status'     => 'nullable|boolean',
        ]);

        $imagePath = FileUploadService::uploadImage($request->file('image'), 'uploads/gallery');

        $gallery = Gallery::create([
            'title'      => $validated['title'],
            'category'   => $validated['category'],
            'image_path' => $imagePath,
            'sort_order' => $validated['sort_order'] ?? 1,
            'status'     => $request->boolean('status', true),
        ]);

        AuditLog::record('gallery_item_created', $gallery, null, $gallery->toArray());

        return redirect()->route('admin.cms.gallery')
            ->with('success', "Gallery image '{$gallery->title}' added successfully.");
    }

    public function editGallery(Gallery $gallery)
    {
        $dbCategories = Gallery::distinct()->pluck('category')->filter()->values()->toArray();
        $defaultCategories = ['Certificates', 'Students', 'Computer Lab', 'Events', 'Campus', 'Celebration', 'Convocation', 'Classroom', 'Awards'];
        $categories = array_values(array_unique(array_merge($defaultCategories, $dbCategories)));

        return view('admin.cms.edit_gallery', compact('gallery', 'categories'));
    }

    public function updateGallery(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:191',
            'category'   => 'required|string|max:100',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'status'     => 'nullable|boolean',
        ]);

        $oldData = $gallery->toArray();

        $updateData = [
            'title'      => $validated['title'],
            'category'   => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? $gallery->sort_order,
            'status'     => $request->boolean('status'),
        ];

        if ($request->hasFile('image')) {
            $updateData['image_path'] = FileUploadService::uploadImage($request->file('image'), 'uploads/gallery');
        }

        $gallery->update($updateData);

        AuditLog::record('gallery_item_updated', $gallery, $oldData, $gallery->toArray());

        return redirect()->route('admin.cms.gallery')
            ->with('success', "Gallery image updated successfully.");
    }

    public function destroyGallery(Gallery $gallery)
    {
        $oldData = $gallery->toArray();
        $gallery->delete();

        AuditLog::record('gallery_item_deleted', $gallery, $oldData, null);

        return redirect()->route('admin.cms.gallery')
            ->with('success', "Gallery item removed.");
    }

    public function toggleGalleryStatus(Gallery $gallery)
    {
        $gallery->update(['status' => !$gallery->status]);

        AuditLog::record('gallery_status_toggled', $gallery, null, ['status' => $gallery->status]);

        return back()->with('success', "Gallery item status toggled.");
    }

    // ==========================================
    // PUBLIC ENQUIRIES & LEAD MANAGEMENT
    // ==========================================

    public function enquiries(Request $request)
    {
        $query = Enquiry::with(['state', 'district']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enquiries = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.cms.enquiries', compact('enquiries'));
    }

    public function showEnquiry(Enquiry $enquiry)
    {
        $enquiry->load(['state', 'district']);
        return view('admin.cms.show_enquiry', compact('enquiry'));
    }

    public function updateEnquiryStatus(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,converted,closed',
        ]);

        $oldStatus = $enquiry->status;
        $enquiry->update(['status' => $validated['status']]);

        AuditLog::record('enquiry_status_updated', $enquiry, ['status' => $oldStatus], ['status' => $enquiry->status]);

        return back()->with('success', "Enquiry status updated to " . ucfirst($enquiry->status) . ".");
    }

    public function destroyEnquiry(Enquiry $enquiry)
    {
        $oldData = $enquiry->toArray();
        $enquiry->delete();

        AuditLog::record('enquiry_deleted', $enquiry, $oldData, null);

        return redirect()->route('admin.cms.enquiries')
            ->with('success', "Enquiry record removed.");
    }

    // ==========================================
    // SITE PAGES & GENERAL CMS CONTENT SETTINGS
    // ==========================================

    public function content()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.cms.content', compact('settings'));
    }

    public function updateContent(Request $request)
    {
        $validated = $request->validate([
            'site_helpline'           => 'nullable|string|max:50',
            'site_whatsapp'           => 'nullable|string|max:50',
            'site_email'              => 'nullable|email|max:100',
            'site_address'            => 'nullable|string',
            'site_office_hours'       => 'nullable|string|max:100',
            'total_franchises_count'  => 'nullable|string|max:20',
            'total_states_count'      => 'nullable|string|max:20',
            'total_cities_count'      => 'nullable|string|max:20',
            'total_courses_count'     => 'nullable|string|max:20',
            'hero_title'              => 'nullable|string|max:191',
            'hero_subtitle'           => 'nullable|string',
            'chairman_message_quote'  => 'nullable|string',
            'about_mission_summary'   => 'nullable|string',
            'social_facebook'         => 'nullable|url|max:191',
            'social_instagram'        => 'nullable|url|max:191',
            'social_youtube'          => 'nullable|url|max:191',
            'social_twitter'          => 'nullable|url|max:191',
        ]);

        foreach ($validated as $key => $val) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'group' => 'cms']
            );
        }

        AuditLog::record('cms_content_updated', null, null, ['updated_keys' => array_keys($validated)]);

        return back()->with('success', 'Website CMS content settings saved successfully.');
    }
}
