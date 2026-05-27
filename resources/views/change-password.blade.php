<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('change_password') }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root { --color-primary: {{ $theme['primary'] ?? '#1a3a6b' }}; }
        body { font-family: 'Noto Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">

<div class="w-full max-w-md px-4">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <h1 class="text-xl font-bold mb-1" style="color: var(--color-primary);">
            {{ __('change_password') }}
        </h1>
        <p class="text-sm text-gray-500 mb-6">{{ __('change_password_desc') }}</p>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('new_password') }}</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="--tw-ring-color: var(--color-primary);">
                <p class="text-xs text-gray-400 mt-1">{{ __('password_min_length') }}</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('confirm_password') }}</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="--tw-ring-color: var(--color-primary);">
            </div>

            <button type="submit"
                    class="w-full text-white font-semibold py-2.5 rounded-lg transition hover:opacity-90"
                    style="background: var(--color-primary);">
                {{ __('save_password') }}
            </button>

        </form>
    </div>
</div>

</body>
</html>
