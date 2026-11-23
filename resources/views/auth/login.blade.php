<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login · ClearWash</title>
    <script>try{document.documentElement.classList.toggle('dark',localStorage.getItem('clearwash-theme')==='dark')}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cw-login min-h-screen bg-brand-canvas font-sans text-slate-900 antialiased">
    <main class="min-h-screen lg:grid lg:grid-cols-[minmax(340px,0.8fr)_minmax(520px,1.2fr)]">
        <section class="cw-login-panel hidden p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-14" aria-label="ClearWash">
            <div class="flex items-center gap-3">
                <span class="cw-logo size-11 rounded-xl"><img src="{{ asset('clearwash-logo.jpg') }}" alt="ClearWash logo"></span>
                <span class="text-xl font-semibold tracking-tight">ClearWash</span>
            </div>
            <div class="max-w-md">
                <h2 class="text-4xl font-bold leading-tight tracking-tight xl:text-5xl">Car wash subscription management.</h2>
                <p class="mt-4 max-w-sm text-sm font-medium leading-6 text-slate-300">Member management, transactions, payments, and membership setup in one workspace.</p>
            </div>
            <p class="text-xs font-medium text-slate-300">ClearWash administration</p>
        </section>

        <section class="flex min-h-screen items-center justify-center bg-brand-canvas px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-4 flex justify-end"><button type="button" class="cw-theme-toggle" data-theme-toggle aria-pressed="false"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z"/></svg><span data-theme-label>Dark mode</span></button></div>
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="cw-logo size-10 rounded-xl"><img src="{{ asset('clearwash-logo.jpg') }}" alt="ClearWash logo"></span>
                    <span class="text-xl font-bold tracking-tight text-slate-950">ClearWash</span>
                </div>

                <div class="cw-card rounded-2xl border border-brand-border bg-white p-6 sm:p-10">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Login to ClearWash</h1>
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
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-brand-border text-brand-primary focus:ring-brand-accent">Remember me</label>
                    <button type="submit" class="w-full rounded-xl bg-brand-primary px-5 py-3 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Login</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
