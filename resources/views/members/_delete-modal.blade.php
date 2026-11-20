@php
    $permanent = $permanent ?? false;
    $modalId = ($permanent ? 'permanent-delete-' : 'soft-delete-').$member->id;
@endphp
<dialog id="{{ $modalId }}" aria-labelledby="{{ $modalId }}-title" class="fixed inset-0 m-auto max-h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-left text-slate-900 shadow-2xl backdrop:bg-slate-950/60">
    <div class="min-w-0 p-5 sm:p-7">
        <div class="flex size-11 items-center justify-center rounded-full bg-red-100 text-xl font-bold text-red-700" aria-hidden="true">!</div>
        <h3 id="{{ $modalId }}-title" class="mt-4 break-words text-xl font-bold text-slate-950">{{ $permanent ? 'Permanently delete' : 'Delete' }} {{ $member->full_name }}?</h3>
        @if ($permanent)
            <p class="mt-2 text-sm leading-6 text-slate-600">Payment-free Pending or Cancelled transactions will also be deleted. Members with payments or other transaction statuses remain protected.</p>
            <p class="mt-2 text-sm font-semibold text-red-700">This action cannot be undone.</p>
        @else
            <p class="mt-2 text-sm leading-6 text-slate-600">This member will move to Deleted Members. Their transaction and payment history will be preserved, and you can restore them later.</p>
        @endif
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <form method="dialog" class="w-full sm:w-auto">
                <button type="submit" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
            </form>
            <form method="POST" action="{{ $permanent ? route('members.force-delete', $member) : route('members.delete', $member) }}" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-lg bg-red-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-800">{{ $permanent ? 'Delete Permanently' : 'Confirm Delete' }}</button>
            </form>
        </div>
    </div>
</dialog>
