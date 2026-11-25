<dialog id="{{ $modalId }}" aria-labelledby="{{ $modalId }}-title" aria-describedby="{{ $modalId }}-description" class="fixed inset-0 m-auto max-h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] max-w-lg overflow-y-auto rounded-2xl border-0 bg-white p-0 text-left text-slate-900 shadow-2xl backdrop:bg-slate-950/60">
    <div class="p-6 sm:p-8">
        <div class="flex size-12 items-center justify-center rounded-2xl bg-red-50 text-red-700" aria-hidden="true">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6l1 3h4M4 6h16M6 6l1 14h10l1-14M10 10v6m4-6v6"/></svg>
        </div>
        <h3 id="{{ $modalId }}-title" class="mt-5 break-words text-xl font-bold leading-snug text-slate-950">Delete {{ $recordName }}?</h3>
        <p id="{{ $modalId }}-description" class="mt-3 text-sm leading-6 text-slate-600">{{ $warning }}</p>
        <div class="mt-5 flex items-start gap-2.5 rounded-xl bg-red-50 px-4 py-3 text-red-700">
            <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m10.3 4.9-8 14A1.4 1.4 0 0 0 3.5 21h17a1.4 1.4 0 0 0 1.2-2.1l-8-14a1.9 1.9 0 0 0-3.4 0ZM12 9v4m0 4h.01"/></svg>
            <p class="text-sm font-medium leading-5">This permanently removes the record and cannot be undone.</p>
        </div>
        <div class="mt-7 grid grid-cols-2 gap-3 border-t border-brand-border pt-5">
            <form method="dialog" class="w-full sm:w-auto">
                <button type="submit" autofocus class="min-h-11 w-full rounded-xl border border-brand-border bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Keep record</button>
            </form>
            <form method="POST" action="{{ $action }}" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="min-h-11 w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition-[background-color,transform] duration-150 hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 active:scale-[0.98]">Delete</button>
            </form>
        </div>
    </div>
</dialog>
