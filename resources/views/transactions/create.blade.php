@extends('layouts.app')

@section('title', 'Create transaction')

@section('content')
    <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-accent"><svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>Back to transactions</a>

    <div class="mt-5">
        <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Create a transaction</h1>
        <p class="mt-3 text-sm font-medium leading-6 text-slate-600 sm:text-base">Start a membership application or renewal for an existing member.</p>
    </div>

    <form method="POST" action="{{ route('transactions.store') }}" class="mt-8 max-w-4xl rounded-2xl border border-brand-border bg-white p-5 [&_label]:font-bold [&_select]:font-semibold [&_textarea]:font-medium sm:p-8">
        @csrf

        <div class="mb-7 border-b border-brand-border/70 pb-5">
            <h2 class="text-lg font-bold text-slate-950">Transaction details</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">Fields marked * are required.</p>
        </div>

        @php($fieldClass = 'mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 placeholder:text-slate-400 focus:border-brand-primary focus:ring-3 focus:ring-brand-border')

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="member_id" class="text-sm font-medium text-slate-700">Member <span class="text-red-600">*</span></label>
                <select id="member_id" name="member_id" required class="{{ $fieldClass }}">
                    <option value="">Select member</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" data-membership-level-id="{{ $member->membership_level_id }}" @selected((string) old('member_id') === (string) $member->id)>
                            {{ $member->full_name }}{{ $member->membership_number ? ' · '.$member->membership_number : '' }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs font-medium text-slate-500">The member's registered plan is selected automatically. You can change it for an upgrade or plan change.</p>
            </div>

            <div>
                <label for="membership_level_id" class="text-sm font-medium text-slate-700">Membership level <span class="text-red-600">*</span></label>
                <select id="membership_level_id" name="membership_level_id" required class="{{ $fieldClass }}">
                    <option value="">Select level</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" data-level-name="{{ $level->name }}" data-fee="{{ $level->fee }}" data-validity-days="{{ $level->validity_days }}" @selected((string) old('membership_level_id') === (string) $level->id)>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
                @error('membership_level_id')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
                <div id="plan-summary" class="mt-3 hidden rounded-lg border border-brand-border bg-brand-accent/10 px-3.5 py-3" aria-live="polite">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-primary">Selected plan</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800"><span id="plan-fee"></span> · <span id="plan-validity"></span></p>
                </div>
            </div>

            <div>
                <label for="type" class="text-sm font-medium text-slate-700">Transaction type <span class="text-red-600">*</span></label>
                <select id="type" name="type" required class="{{ $fieldClass }}">
                    <option value="">Select type</option>
                    <option value="Application" @selected(old('type') === 'Application')>Application</option>
                    <option value="Renewal" @selected(old('type') === 'Renewal')>Renewal</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="notes" class="text-sm font-medium text-slate-700">Notes</label>
                <textarea id="notes" name="notes" rows="3" maxlength="1000" placeholder="Optional details about this transaction" class="{{ $fieldClass }}">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex flex-col-reverse gap-3 border-t border-brand-border/70 pt-6 sm:flex-row">
            <a href="{{ route('transactions.index') }}" class="rounded-xl border border-brand-border px-5 py-2.5 text-center text-sm font-semibold text-slate-800 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Cancel</a>
            <button type="submit" class="rounded-xl bg-brand-primary px-5 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar active:scale-[0.98]">Create transaction</button>
        </div>
    </form>

    <script>
        (() => {
            const memberSelect = document.getElementById('member_id');
            const levelSelect = document.getElementById('membership_level_id');
            const planSummary = document.getElementById('plan-summary');
            const planFee = document.getElementById('plan-fee');
            const planValidity = document.getElementById('plan-validity');
            const hasOldLevel = @json(old('membership_level_id') !== null);

            const updatePlanSummary = () => {
                const selectedLevel = levelSelect.options[levelSelect.selectedIndex];

                if (! selectedLevel?.value) {
                    planSummary.classList.add('hidden');

                    return;
                }

                const fee = Number(selectedLevel.dataset.fee);
                const validityDays = Number(selectedLevel.dataset.validityDays);

                planFee.textContent = new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                }).format(fee);
                planValidity.textContent = `${validityDays} ${validityDays === 1 ? 'day' : 'days'}`;
                planSummary.classList.remove('hidden');
            };

            const selectMemberLevel = () => {
                const selectedMember = memberSelect.options[memberSelect.selectedIndex];
                const registeredLevelId = selectedMember?.dataset.membershipLevelId;

                if (! registeredLevelId) {
                    levelSelect.value = '';
                    updatePlanSummary();

                    return;
                }

                levelSelect.value = Array.from(levelSelect.options).some((option) => option.value === registeredLevelId)
                    ? registeredLevelId
                    : '';
                updatePlanSummary();
            };

            memberSelect.addEventListener('change', selectMemberLevel);
            levelSelect.addEventListener('change', updatePlanSummary);

            if (hasOldLevel) {
                updatePlanSummary();
            } else if (memberSelect.value) {
                selectMemberLevel();
            }
        })();
    </script>
@endsection
