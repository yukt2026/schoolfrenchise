<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Models\District;
use App\Models\FranchiseApplication;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FranchisePublicController extends Controller
{
    public function create()
    {
        $states = State::where('status', true)->orderBy('name')->get();
        return view('pages.franchise-apply', compact('states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:191',
            'centre_name' => 'required|string|max:191',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:191',
            'state_id' => 'required|exists:states,id',
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'qualification' => 'required|string|max:100',
            'computers_count' => 'required|integer|min:3',
            'carpet_area_sqft' => 'required|integer|min:100',
            'remarks' => 'nullable|string',
        ]);

        // Generate application reference number: FR-YYYY-RANDOM
        $appNo = 'FR-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $validated['application_no'] = $appNo;
        $validated['status'] = 'pending';

        $app = FranchiseApplication::create($validated);

        return redirect()->route('franchise.status')->with('success', "Your Franchise Application has been registered successfully! Your Application Tracking ID is: <strong>{$appNo}</strong>. Save this ID to track your application status.");
    }

    public function searchIndex(Request $request)
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $districts = [];

        if ($request->filled('state_id')) {
            $districts = District::where('state_id', $request->state_id)->where('status', true)->orderBy('name')->get();
        }

        $query = Centre::with(['state', 'district'])->where('status', 'active');

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('centre_code', 'like', "%{$kw}%")
                  ->orWhere('centre_name', 'like', "%{$kw}%")
                  ->orWhere('director_name', 'like', "%{$kw}%")
                  ->orWhere('city', 'like', "%{$kw}%")
                  ->orWhere('pincode', 'like', "%{$kw}%");
            });
        }

        $centres = $query->paginate(9)->withQueryString();

        return view('pages.search-centre', compact('states', 'districts', 'centres'));
    }

    public function statusIndex()
    {
        return view('pages.franchise-status');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'search_query' => 'required|string|max:60',
        ]);

        $queryStr = trim($request->search_query);

        $application = FranchiseApplication::with(['state', 'district'])
            ->where('application_no', $queryStr)
            ->orWhere('mobile', $queryStr)
            ->first();

        return view('pages.franchise-status', [
            'searched' => true,
            'application' => $application,
            'searchQuery' => $queryStr,
        ]);
    }
}
