<?php

namespace App\Http\Controllers;

use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Illuminate\Http\Request;

class MembershipSettingController extends Controller
{
    public function index()
    {
        return view('membership-settings.index', [
            'categories' => MembershipCategory::where('is_active', true)->orderBy('id')->get(),
            'levels' => MembershipLevel::where('is_active', true)->orderBy('id')->get(),
        ]);
    }

    public function updateLevel(Request $request, MembershipLevel $membershipLevel)
    {
        $validated = $request->validate([
            'fee' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'validity_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'benefits' => ['nullable', 'string', 'max:2000'],
        ]);

        $membershipLevel->update($validated);

        return redirect()->route('membership-settings.index')
            ->with('success', "{$membershipLevel->name} membership details updated.");
    }
}
