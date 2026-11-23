<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'membership_category_id' => ['nullable', Rule::exists('membership_categories', 'id')->where('is_active', true)],
            'membership_level_id' => ['nullable', Rule::exists('membership_levels', 'id')->where('is_active', true)],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $members = Member::query()
            ->with(['membershipCategory', 'membershipLevel'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('membership_number', 'like', "%{$search}%")
                        ->orWhereHas('membershipCategory', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('membershipLevel', fn ($query) => $query->where('name', 'like', "%{$search}%"));

                    $nameParts = preg_split('/\s+/', trim($search), 2);
                    if (count($nameParts) === 2) {
                        $query->orWhere(function ($query) use ($nameParts) {
                            $query->where('first_name', 'like', "%{$nameParts[0]}%")
                                ->where('last_name', 'like', "%{$nameParts[1]}%");
                        });
                    }
                });
            })
            ->when($filters['membership_category_id'] ?? null, fn ($query, $value) => $query->where('membership_category_id', $value))
            ->when($filters['membership_level_id'] ?? null, fn ($query, $value) => $query->where('membership_level_id', $value))
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
        $validated = $this->validatedData($request);

        $member = DB::transaction(function () use ($validated) {
            $level = MembershipLevel::findOrFail($validated['membership_level_id']);
            $category = MembershipCategory::findOrFail($validated['membership_category_id']);
            $pricing = MembershipTransaction::pricingFor($category, $level);

            $member = Member::create($validated);

            $member->membershipTransactions()->firstOrCreate(
                ['type' => TransactionType::Application->value],
                [
                    'membership_level_id' => $level->id,
                    'status' => (float) $pricing['amount_due'] === 0.0 ? TransactionStatus::Paid->value : TransactionStatus::Pending->value,
                    ...$pricing,
                    'category_name' => $category->name,
                    'level_name' => $level->name,
                    'validity_days' => $level->validity_days,
                ],
            );

            return $member;
        });

        return redirect()->route('members.show', $member)->with('success', 'Member registered successfully.');
    }

    public function show(Member $member)
    {
        $member->load([
            'membershipCategory',
            'membershipLevel',
            'membershipTransactions' => fn ($query) => $query
                ->with(['membershipLevel', 'payments'])
                ->latest(),
        ]);

        return view('members.show', compact('member'));
    }

    public function history(Member $member)
    {
        $member->load(['membershipCategory', 'membershipLevel', 'membershipTransactions' => fn ($query) => $query->with(['membershipLevel', 'payments'])->latest()]);

        return view('members.history', compact('member'));
    }

    public function print(Member $member)
    {
        $member->load(['membershipCategory', 'membershipLevel', 'membershipTransactions' => fn ($query) => $query->with('payments')->latest()->limit(1)]);

        return view('members.print', compact('member'));
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
        $member->update($this->validatedData($request, $member));

        return redirect()->route('members.show', $member)->with('success', 'Member information updated.');
    }

    public function deactivate(Member $member)
    {
        $member->update(['is_active' => false]);

        return redirect()->route('members.show', $member)->with('success', 'Member deactivated.');
    }

    public function reactivate(Member $member)
    {
        $member->update(['is_active' => true]);

        return redirect()->route('members.show', $member)->with('success', 'Member reactivated.');
    }

    private function validatedData(Request $request, ?Member $member = null): array
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                function (string $attribute, mixed $value, $fail) use ($member): void {
                    $duplicates = Member::query()
                        ->whereRaw('LOWER(email) = ?', [Str::lower((string) $value)])
                        ->when($member, fn ($query) => $query->where('id', '!=', $member->id))
                        ->get();
                    if ($duplicates->isNotEmpty()) {
                        $fail('This email is already registered to another member.');
                    }
                },
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
                'regex:/^\+?[0-9][0-9\s().-]*[0-9]$/',
                function (string $attribute, mixed $value, $fail) use ($member): void {
                    $normalizedPhone = $this->normalizePhone((string) $value);

                    if (strlen($normalizedPhone) < 7 || strlen($normalizedPhone) > 15) {
                        $fail('The phone number must contain between 7 and 15 digits.');

                        return;
                    }

                    $duplicates = Member::query()
                        ->when($member, fn ($query) => $query->where('id', '!=', $member->id))
                        ->get(['id', 'phone'])
                        ->filter(fn (Member $existingMember) => $this->normalizePhone($existingMember->phone) === $normalizedPhone);
                    if ($duplicates->isNotEmpty()) {
                        $fail('This phone number is already registered to another member.');
                    }
                },
            ],
            'membership_category_id' => ['required', Rule::exists('membership_categories', 'id')->where('is_active', true)],
            'membership_level_id' => ['required', Rule::exists('membership_levels', 'id')->where('is_active', true)],
            'address' => ['nullable', 'string', 'max:1000'],
            'joined_on' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $category = MembershipCategory::findOrFail($validated['membership_category_id']);
        $level = MembershipLevel::findOrFail($validated['membership_level_id']);

        $validated['phone'] = $this->normalizePhone($validated['phone']);
        $validated['category'] = $category->name;
        $validated['membership_level'] = $level->name;

        return $validated;
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    private function membershipOptions(): array
    {
        return [
            'categories' => MembershipCategory::where('is_active', true)->orderBy('id')->get(['id', 'name', 'discount_percentage']),
            'levels' => MembershipLevel::where('is_active', true)->orderBy('id')->get(['id', 'name', 'fee']),
        ];
    }
}
