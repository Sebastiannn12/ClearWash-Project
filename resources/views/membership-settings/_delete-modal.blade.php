<dialog id="{{ $modalId }}" aria-labelledby="{{ $modalId }}-title" class="fixed inset-0 m-auto max-h-[calc(100vh-2rem)] w-[calc(100vw-2rem)] max-w-lg overflow-y-auto rounded-2xl border-0 bg-white p-0 text-left text-slate-900 shadow-2xl backdrop:bg-slate-950/60">
    <div class="p-5 sm:p-7">
        <div class="flex size-11 items-center justify-center rounded-full bg-red-100 text-xl font-bold text-red-700" aria-hidden="true">!</div>
        <h3 id="{{ $modalId }}-title" class="mt-4 break-words text-xl font-bold text-slate-950">Delete {{ $recordName }}?</h3>
        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $warning }}</p>
        <p class="mt-2 text-sm font-semibold text-red-700">This action cannot be undone.</p>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <form method="dialog" class="w-full sm:w-auto">
                <button type="submit" class="w-full rounded-xl border border-brand-border bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-[background-color,transform] duration-150 hover:bg-brand-canvas active:scale-[0.98]">Cancel</button>
            </form>
            <form method="POST" action="{{ $action }}" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-red-700 active:scale-[0.98]">Delete</button>
            </form>
        </div>
    </div>
</dialog>
