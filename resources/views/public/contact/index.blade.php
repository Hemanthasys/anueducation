{{-- Contact page: map + details left, form right --}}
@extends('layouts.public')

@section('title', __('contact_page'))

@section('content')

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('contact_page') }}
        </h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">

        {{-- Left: contact details + map --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">

            {{-- Contact details card --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('contact_details') }}</p>

                @if(($siteSettings['address_en'] ?? null) || ($siteSettings['address_si'] ?? null))
                        <div class="profile-info-row">
                            <span class="profile-info-label">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        {{ __('address') ?? 'Address' }}
                    </span>
                    <span class="profile-info-value">
                    {{ app()->getLocale() === 'si' ? ($siteSettings['address_si'] ?? '') : ($siteSettings['address_en'] ?? '') }}
                </span>
                </div>
                @endif

                @if($siteSettings['phone'] ?? null)
                <div class="profile-info-row">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        {{ __('contact') }}
                    </span>
                    <a href="tel:{{ $siteSettings['phone'] }}"
                       class="profile-info-value no-underline"
                       style="color: var(--color-primary);">
                        {{ $siteSettings['phone'] }}
                    </a>
                </div>
                @endif

                @if($siteSettings['email'] ?? null)
                <div class="profile-info-row">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        {{ __('email') ?? 'Email' }}
                    </span>
                    <a href="mailto:{{ $siteSettings['email'] }}"
                       class="profile-info-value no-underline"
                       style="color: var(--color-primary);">
                        {{ $siteSettings['email'] }}
                    </a>
                </div>
                @endif

                {{-- Office hours --}}
                <div class="profile-info-row">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('office_hours') }}
                    </span>
                    <span class="profile-info-value">{{ __('office_hours_value') }}</span>
                </div>
            </div>

            {{-- Map --}}
                @if(($siteSettings['lat'] ?? null) && ($siteSettings['lng'] ?? null))
                <div class="profile-card">
                    <div class="w-full rounded-xl overflow-hidden" style="height: 250px;">
                        <iframe
                            width="100%"
                            height="100%"
                            frameborder="0"
                            style="border: 0;"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps?q={{ $siteSettings['lat'] }},{{ $siteSettings['lng'] }}&z=16&output=embed"
                            allowfullscreen>
                        </iframe>
                    </div>
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $siteSettings['lat'] }},{{ $siteSettings['lng'] }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-lg text-sm font-semibold no-underline text-white transition"
                   style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c-.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                    {{ __('get_directions') }}
                </a>
            </div>
            @endif

        </div>

        {{-- Right: contact form --}}
        <div class="profile-card">
            <p class="profile-section-title">{{ __('contact_form') }}</p>

            {{-- Success message --}}
            @if(session('success'))
            <div class="mb-4 p-4 rounded-lg text-sm font-medium"
                 style="background: #dcfce7; color: #166534;">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            {{-- Error messages --}}
            @if($errors->any())
            <div class="mb-4 p-4 rounded-lg text-sm font-medium"
                 style="background: #fee2e2; color: #991b1b;">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- Contact form --}}
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-primary);">
                        {{ __('your_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-lg border text-sm transition"
                           style="border-color: #e5e7eb;"
                           onfocus="this.style.borderColor='var(--color-primary)'"
                           onblur="this.style.borderColor='#e5e7eb'">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-primary);">
                        {{ __('your_email') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-lg border text-sm transition"
                           style="border-color: #e5e7eb;"
                           onfocus="this.style.borderColor='var(--color-primary)'"
                           onblur="this.style.borderColor='#e5e7eb'">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Subject --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-primary);">
                        {{ __('your_subject') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="subject"
                           value="{{ old('subject') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-lg border text-sm transition"
                           style="border-color: #e5e7eb;"
                           onfocus="this.style.borderColor='var(--color-primary)'"
                           onblur="this.style.borderColor='#e5e7eb'">
                    @error('subject')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-primary);">
                        {{ __('your_message') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message"
                              rows="5"
                              required
                              class="w-full px-4 py-2.5 rounded-lg border text-sm transition resize-none"
                              style="border-color: #e5e7eb;"
                              onfocus="this.style.borderColor='var(--color-primary)'"
                              onblur="this.style.borderColor='#e5e7eb'">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- reCAPTCHA --}}
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                    @error('g-recaptcha-response')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit button --}}
                <button type="submit"
                        class="w-full py-3 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                        style="background: var(--color-primary);">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        {{ __('send_message') }}
                    </span>
                </button>

            </form>
        </div>

    </div>
</div>

{{-- Page styles --}}
<style>
    .profile-card {
        background: white;
        border: 0.5px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.25rem;
    }
    .profile-section-title {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--color-accent);
        display: inline-block;
        margin-bottom: 1rem;
    }
    .profile-info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 0.5px solid #f3f4f6;
        font-size: 0.83rem;
    }
    .profile-info-row:last-of-type { border-bottom: none; }
    .profile-info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        flex-shrink: 0;
    }
    .profile-info-value {
        color: #1f2937;
        font-weight: 500;
        text-align: right;
    }
</style>

{{-- reCAPTCHA script --}}
@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@endsection