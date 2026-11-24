<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login · ClearWash</title>
    <script>
        try {
            document.documentElement.classList.toggle('dark', localStorage.getItem('clearwash-theme') === 'dark')
        } catch (e) {}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="cw-login min-h-screen bg-brand-canvas font-sans text-slate-900 antialiased">
    <main class="min-h-screen">
        <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="cw-login-shell w-full max-w-md lg:flex lg:flex-wrap lg:items-center">
                <div class="cw-login-theme mb-4 flex justify-end lg:order-4 lg:mb-0 lg:mt-5"><button type="button" class="cw-theme-toggle" data-theme-toggle aria-pressed="false"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z" />
                        </svg><span data-theme-label>Dark mode</span></button></div>
                <div class="cw-login-brand mb-8 flex items-center gap-3 lg:hidden">
                    <span class="cw-logo size-10 rounded-xl"><img src="{{ asset('clearwash-logo.jpg') }}" alt="ClearWash logo"></span>
                    <span class="text-xl font-bold tracking-tight text-slate-950">ClearWash</span>
                </div>

                <div class="mb-8 hidden w-full text-center lg:order-1 lg:block">
                    <div class="flex items-center justify-center gap-4">
                        <span class="cw-logo size-16 rounded-2xl"><img src="{{ asset('clearwash-logo.jpg') }}" alt="ClearWash logo"></span>
                        <span class="text-4xl font-bold tracking-tight text-slate-950">ClearWash</span>
                    </div>
                </div>

                <div class="cw-card w-full rounded-2xl border border-brand-border bg-white p-6 sm:p-10 lg:order-2">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Login to ClearWash</h1>
                    <p class="mt-3 text-sm font-medium leading-6 text-slate-600">Use your administrator account to continue.</p>

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="text-sm font-bold text-slate-800">Email address</label>
                            <div class="cw-login-field-wrap relative">
                                <svg class="cw-login-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus autocomplete="username" maxlength="255" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror class="cw-login-field mt-2 w-full rounded-xl border bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 placeholder:text-slate-400 focus:border-brand-primary focus:ring-3 focus:ring-brand-border {{ $errors->has('email') ? 'border-red-300' : 'border-brand-border' }}">
                            </div>
                            @error('email')<p id="email-error" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password" class="text-sm font-bold text-slate-800">Password</label>
                            <div class="cw-login-field-wrap relative">
                                <svg class="cw-login-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="4" y="10" width="16" height="11" rx="2.5" />
                                    <path stroke-linecap="round" d="M8 10V7a4 4 0 0 1 8 0v3" />
                                </svg>
                                <input id="password" name="password" type="password" placeholder="Enter your password" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror class="cw-login-field mt-2 w-full rounded-xl border bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition-[border-color,box-shadow] duration-150 focus:border-brand-primary focus:ring-3 focus:ring-brand-border {{ $errors->has('password') ? 'border-red-300' : 'border-brand-border' }}">
                                <button type="button" class="cw-password-toggle" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false">
                                    <svg data-password-show-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                        <circle cx="12" cy="12" r="2.5" />
                                    </svg>
                                    <svg data-password-hide-icon hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 6.1A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a14.8 14.8 0 0 1-3.2 3.5M6.2 8.3C3.8 10 2.5 12 2.5 12s3.5 6 9.5 6c1.2 0 2.3-.2 3.3-.6M10 10a2.5 2.5 0 0 0 3.4 3.4" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')<p id="password-error" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-brand-border text-brand-primary focus:ring-brand-accent">Remember me</label>
                        <div class="border-t border-brand-border pt-5">
                            <button type="submit" class="w-full rounded-xl bg-brand-primary px-5 py-3 text-sm font-bold text-white transition-[background-color,transform] duration-150 hover:bg-brand-sidebar focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent active:scale-[0.98]">Login</button>
                        </div>
                    </form>
                </div>
                <p class="hidden text-sm font-medium leading-6 text-slate-600 lg:order-3 lg:mt-5 lg:block lg:flex-1">Car wash subscription management</p>
            </div>
        </section>
    </main>
</body>

</html>
