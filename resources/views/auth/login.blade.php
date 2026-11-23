<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login · ClearWash</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">
    <main class="min-h-screen lg:grid lg:grid-cols-[minmax(340px,0.8fr)_minmax(520px,1.2fr)]">
        <section class="hidden bg-brand-sidebar p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-14" aria-label="ClearWash">
            <div class="flex items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-brand-accent text-xl font-bold">C</span>
                <span class="text-xl font-semibold tracking-tight">ClearWash</span>
            </div>
            <div class="max-w-md">
                <h2 class="text-3xl font-bold tracking-tight xl:text-4xl">Car wash subscription system</h2>
                <p class="mt-4 max-w-sm text-sm font-medium leading-6 text-slate-300">Member management, transactions, payments, and membership setup in one workspace.</p>
            </div>
            <p class="text-xs font-medium text-slate-500">ClearWash administration</p>
        </section>

        <section class="flex min-h-screen items-center justify-center bg-brand-canvas px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-sidebar text-lg font-bold text-white">C</span>
                    <span class="text-xl font-bold tracking-tight text-slate-950">ClearWash</span>
                </div>

                <div class="rounded-2xl border border-brand-border bg-white p-6 sm:p-8">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Sign in to ClearWash</h1>
                    <p class="mt-3 text-sm font-medium leading-6 text-slate-600">Use your administrator account to continue.</p>

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="text-sm font-bold text-slate-800">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" maxlength="255" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror class="mt-2 w-full rounded-xl border bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 placeholder:text-slate-400 focus:border-brand-primary focus:ring-3 focus:ring-brand-border {{ $errors->has('email') ? 'border-red-300' : 'border-brand-border' }}">
                        @error('email')<p id="email-error" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="text-sm font-bold text-slate-800">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror class="mt-2 w-full rounded-xl border bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 focus:border-brand-primary focus:ring-3 focus:ring-brand-border {{ $errors->has('password') ? 'border-red-300' : 'border-brand-border' }}">
                        @error('password')<p id="password-error" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-brand-primary px-5 py-3 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Sign in</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
