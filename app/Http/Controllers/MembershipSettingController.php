<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MembershipSettingController extends Controller
{
    public function index()
    {
        return redirect()->route('membership-settings.categories.index');
    }

    public function categories()
    {
        return view('membership-settings.index', [
            'categories' => MembershipCategory::orderBy('id')->get(),
            'page' => 'categories',
        ]);
    }

    public function levels()
    {
        return view('membership-settings.index', [
            'levels' => MembershipLevel::orderBy('id')->get(),
            'page' => 'levels',
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $this->categoryValidator($request)
            ->validateWithBag('category-create');

        $category = MembershipCategory::create($validated);

        return redirect()->route('membership-settings.categories.index')
            ->with('success', "{$category->name} category created.");
    }

    public function updateCategory(Request $request, MembershipCategory $membershipCategory)
    {
        $validator = $this->categoryValidator($request, $membershipCategory);
        $validator->after(function ($validator) use ($request, $membershipCategory): void {
            $newName = trim((string) $request->input('name'));

            if ($membershipCategory->isRequired() && $newName !== $membershipCategory->name) {
                $validator->errors()->add('name', 'Required categories cannot be renamed.');
            }

        });

        $validated = $validator->validateWithBag("category-{$membershipCategory->id}");

        DB::transaction(function () use ($membershipCategory, $validated): void {
            $membershipCategory->update($validated);
            $membershipCategory->members()->update(['category' => $membershipCategory->name]);
        });

        return redirect()->route('membership-settings.categories.index')
            ->with('success', "{$membershipCategory->name} category updated.");
    }

    public function destroyCategory(MembershipCategory $membershipCategory)
    {
        $deleted = DB::transaction(function () use ($membershipCategory): bool {
            $category = MembershipCategory::lockForUpdate()->findOrFail($membershipCategory->id);
            if (Member::withTrashed()->where('membership_category_id', $category->id)->orWhere('category', $category->name)->exists()
                || MembershipTransaction::where('membership_category_id', $category->id)->orWhere('category_name', $category->name)->exists()) {
                return false;
            }

            return $category->delete();
        });

        if (! $deleted) {
            return redirect()->route('membership-settings.categories.index')
                ->with('error', "{$membershipCategory->name} cannot be deleted because it is referenced by members or transactions.");
        }

        $name = $membershipCategory->name;

        return redirect()->route('membership-settings.categories.index')
            ->with('success', "{$name} category deleted.");
    }

    public function storeLevel(Request $request)
    {
        $validated = $this->levelValidator($request)
            ->validateWithBag('level-create');

        $level = MembershipLevel::create($validated);

        return redirect()->route('membership-settings.levels.index')
            ->with('success', "{$level->name} membership level created.");
    }

    public function updateLevel(Request $request, MembershipLevel $membershipLevel)
    {
        $validator = $this->levelValidator($request, $membershipLevel);
        $validator->after(function ($validator) use ($request, $membershipLevel): void {
            $newName = trim((string) $request->input('name'));
            if ($membershipLevel->isRequired() && $newName !== $membershipLevel->name) {
                $validator->errors()->add('name', 'Required membership levels cannot be renamed.');
            }

        });

        $validated = $validator->validateWithBag("level-{$membershipLevel->id}");

        DB::transaction(function () use ($membershipLevel, $validated): void {
            $membershipLevel->update($validated);
            $membershipLevel->members()->update(['membership_level' => $membershipLevel->name]);
        });

        return redirect()->route('membership-settings.levels.index')
            ->with('success', "{$membershipLevel->name} membership details updated.");
    }

    public function destroyLevel(MembershipLevel $membershipLevel)
    {
        $deleted = DB::transaction(function () use ($membershipLevel): bool {
            $level = MembershipLevel::lockForUpdate()->findOrFail($membershipLevel->id);
            if (Member::withTrashed()->where('membership_level_id', $level->id)->orWhere('membership_level', $level->name)->exists()
                || MembershipTransaction::where('membership_level_id', $level->id)->orWhere('level_name', $level->name)->exists()) {
                return false;
            }

            return $level->delete();
        });

        if (! $deleted) {
            return redirect()->route('membership-settings.levels.index')
                ->with('error', "{$membershipLevel->name} cannot be deleted because it is referenced by members or transactions.");
        }

        $name = $membershipLevel->name;

        return redirect()->route('membership-settings.levels.index')
            ->with('success', "{$name} membership level deleted.");
    }

    private function categoryValidator(Request $request, ?MembershipCategory $category = null)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'discount_percentage' => $request->input('discount_percentage', $category?->discount_percentage ?? 0),
        ]);

        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('membership_categories', 'name')->ignore($category)],
            'benefits' => ['nullable', 'string', 'max:2000'],
            'discount_percentage' => ['required', 'numeric', 'between:0,100', 'decimal:0,2'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function levelValidator(Request $request, ?MembershipLevel $level = null)
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('membership_levels', 'name')->ignore($level)],
            'fee' => ['nullable', 'required_if:is_active,1', 'numeric', 'min:0', 'max:99999999.99'],
            'validity_days' => ['nullable', 'required_if:is_active,1', 'integer', 'min:1', 'max:3650'],
            'benefits' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
