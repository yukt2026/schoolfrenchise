<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CentreDownloadController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Download::where('status', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('title', 'like', "%{$s}%");
        }

        $downloads = $query->latest('id')->paginate(12)->withQueryString();
        $categories = Download::select('category')->distinct()->pluck('category');

        return view('centre.downloads.index', compact('centre', 'downloads', 'categories'));
    }

    public function certificate()
    {
        $centre = $this->getAuthCentre();
        $centre->load(['state', 'district']);

        return view('centre.downloads.certificate', compact('centre'));
    }

    public function studentCertificates(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Certificate::where('centre_id', $centre->id)
            ->with(['student', 'course', 'examResult']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('certificate_no', 'like', "%{$s}%")
                  ->orWhereHas('student', function ($sq) use ($s) {
                      $sq->where('full_name', 'like', "%{$s}%")
                        ->orWhere('roll_no', 'like', "%{$s}%")
                        ->orWhere('registration_no', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('id')->paginate(15)->withQueryString();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        $stats = [
            'total' => Certificate::where('centre_id', $centre->id)->count(),
            'active' => Certificate::where('centre_id', $centre->id)->where('status', 'issued')->count(),
            'revoked' => Certificate::where('centre_id', $centre->id)->where('status', 'revoked')->count(),
        ];

        return view('centre.downloads.student_certificates', compact('centre', 'certificates', 'courses', 'stats'));
    }

    public function showStudentCertificate(Certificate $certificate)
    {
        $centre = $this->getAuthCentre();
        if ($certificate->centre_id !== $centre->id) {
            abort(403, 'Unauthorized access to student certificate.');
        }

        $certificate->load(['student.user', 'course', 'centre', 'examResult.marksheet']);
        return view('centre.downloads.student_certificate_show', compact('centre', 'certificate'));
    }

    public function downloadFile(Download $download)
    {
        if (!$download->status) {
            abort(404, 'File is currently unavailable.');
        }

        $download->increment('downloads_count');

        if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
            return Storage::disk('public')->download($download->file_path, $download->title . '.' . pathinfo($download->file_path, PATHINFO_EXTENSION));
        }

        return back()->with('info', "Download for '{$download->title}' registered. Physical document package available at Central Directorate.");
    }
}
