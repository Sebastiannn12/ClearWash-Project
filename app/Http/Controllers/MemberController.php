<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', Rule::exists('membership_categories', 'name')->where('is_active', true)],
            'membership_level' => ['nullable', Rule::exists('membership_levels', 'name')->where('is_active', true)],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $members = Member::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('membership_number', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('membership_level', 'like', "%{$search}%");

                    $nameParts = preg_split('/\s+/', trim($search), 2);
                    if (count($nameParts) === 2) {
                        $query->orWhere(function ($query) use ($nameParts) {
                            $query->where('first_name', 'like', "%{$nameParts[0]}%")
                                ->where('last_name', 'like', "%{$nameParts[1]}%");
                        });
                    }
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
            ->when($filters['membership_level'] ?? null, fn ($query, $value) => $query->where('membership_level', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('is_active', $value === 'active'))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', [
            'members' => $members,
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('is_active', true)->count(),
            'inactiveMembers' => Member::where('is_active', false)->count(),
            ...$this->membershipOptions(),
        ]);
    }

    public function create()
    {
        return view('members.create', $this->membershipOptions());
    }

    public function store(Request $request)
    {
        $member = DB::transaction(fn () => Member::create($this->validatedData($request)));

        return redirect()->route('members.show', $member)->with('success', 'Member registered successfully.');
    }

    public function show(Member $member)
    {
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', [
            'member' => $member,
            ...$this->membershipOptions(),
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $member->update($this->validatedData($request));

        return redirect()->route('members.show', $member)->with('success', 'Member information updated.');
    }

    public function destroy(Member $member)
    {
        $member->update(['is_active' => false]);

        return redirect()->route('members.show', $member)->with('success', 'Member deactivated.');
    }

    public function reactivate(Member $member)
    {
        $member->update(['is_active' => true]);

        return redirect()->route('members.show', $member)->with('success', 'Member reactivated.');
    }

    public function forceDelete(Member $member)
    {
        $memberName = $member->full_name;
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', "{$memberName} was permanently deleted.");
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'category' => ['required', Rule::exists('membership_categories', 'name')->where('is_active', true)],
            'membership_level' => ['required', Rule::exists('membership_levels', 'name')->where('is_active', true)],
            'address' => ['nullable', 'string', 'max:1000'],
            'joined_on' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function membershipOptions(): array
    {
        return [
            'categories' => MembershipCategory::where('is_active', true)->orderBy('id')->pluck('name'),
            'levels' => MembershipLevel::where('is_active', true)->orderBy('id')->pluck('name'),
        ];
    }
}
