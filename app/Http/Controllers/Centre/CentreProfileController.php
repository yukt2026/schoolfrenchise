<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\District;
use App\Models\State;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CentreProfileController extends Controller
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

    public function show()
    {
        $centre = $this->getAuthCentre();
        $centre->load(['state', 'district', 'wallet', 'user']);
        $states = State::orderBy('name')->get();
        $districts = District::where('state_id', $centre->state_id)->orderBy('name')->get();

        return view('centre.profile.show', compact('centre', 'states', 'districts'));
    }

    public function update(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'director_name' => 'required|string|max:191',
            'mobile' => 'required|string|size:10',
            'alternate_mobile' => 'nullable|string|max:20',
            'email' => 'required|email|max:191',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'lab_capacity' => 'required|integer|min:1',
            'centre_logo' => 'nullable|image|max:2048',
            'director_photo' => 'nullable|image|max:2048',
            'director_signature' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('centre_logo')) {
            $validated['centre_logo'] = FileUploadService::uploadImage($request->file('centre_logo'), 'uploads/centres/logos');
        }

        if ($request->hasFile('director_photo')) {
            $validated['director_photo'] = FileUploadService::uploadImage($request->file('director_photo'), 'uploads/centres/directors');
        }

        if ($request->hasFile('director_signature')) {
            $validated['director_signature'] = FileUploadService::uploadImage($request->file('director_signature'), 'uploads/centres/signatures');
        }

        $old = $centre->toArray();
        $centre->update($validated);

        AuditLog::record('centre_profile_updated', $centre, $old, $centre->toArray());

        return redirect()->route('centre.profile.show')
            ->with('success', 'Centre profile details updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::record('centre_password_changed', $user, null, ['user_id' => $user->id]);

        return redirect()->route('centre.profile.show')
            ->with('success', 'Account login password updated successfully.');
    }
}
