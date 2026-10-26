<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\Notice;
use App\Models\Setting;
use App\Models\State;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $featuredCourses = Course::where('status', true)->where('is_popular', true)->take(12)->get();
        if ($featuredCourses->count() < 12) {
            $featuredCourses = Course::where('status', true)->take(12)->get();
        }

        $allCourses = Course::where('status', true)->get();
        $states = State::where('status', true)->orderBy('name')->get();
        $galleries = Gallery::where('status', true)->orderBy('sort_order', 'asc')->latest('id')->take(12)->get();
        $notices = Notice::where('status', true)->latest()->take(5)->get();
        $banners = Banner::where('status', true)->orderBy('sort_order', 'asc')->latest('id')->get();

        $stats = [
            'franchises' => Setting::get('total_franchises_count', '12042'),
            'states' => Setting::get('total_states_count', '28'),
            'cities' => Setting::get('total_cities_count', '763'),
            'courses' => Setting::get('total_courses_count', '364'),
        ];

        return view('pages.home', compact('featuredCourses', 'allCourses', 'states', 'galleries', 'notices', 'stats', 'banners'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function company()
    {
        return view('pages.company');
    }

    public function chairman()
    {
        return view('pages.chairman');
    }

    public function mission()
    {
        return view('pages.mission');
    }

    public function members()
    {
        return view('pages.members');
    }

    public function facilities()
    {
        return view('pages.facilities');
    }

    public function gallery()
    {
        $categories = Gallery::where('status', true)->pluck('category')->unique();
        $items = Gallery::where('status', true)->orderBy('sort_order')->get();
        return view('pages.gallery', compact('categories', 'items'));
    }

    public function contact()
    {
        $states = State::where('status', true)->orderBy('name')->get();
        return view('pages.contact', compact('states'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:191',
            'subject' => 'required|string|max:191',
            'message' => 'required|string',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
        ]);

        $validated['type'] = 'general_contact';
        $validated['status'] = 'new';

        Enquiry::create($validated);

        return back()->with('success', 'Thank you! Your message has been received. Our helpdesk team will contact you shortly.');
    }

    public function privacy()
    {
        return view('pages.privacy-policy');
    }

    public function terms()
    {
        return view('pages.terms-conditions');
    }

    public function refund()
    {
        return view('pages.refund-policy');
    }
}
