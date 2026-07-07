@extends('layouts.teacher')

@section('title', __('nav_mutual_transfers'))

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-lg font-bold" style="color: var(--color-primary);">
        {{ __('nav_mutual_transfers') }}
    </h1>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="rounded-xl px-4 py-3 mb-4 text-sm" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="rounded-xl px-4 py-3 mb-4 text-sm" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        {{ session('error') }}
    </div>
@endif

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MY REQUEST                                                    --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm mb-6" style="border: 1px solid #e5e7eb;">
    <div class="px-5 py-3 flex items-center justify-between" style="border-bottom: 1px solid #f3f4f6;">
        <h3 class="font-semibold text-sm" style="color: var(--color-primary);">
            {{ $myPost ? __('my_transfer_request') : __('post_transfer_request') }}
        </h3>
        @if($myPost)
        <form method="POST" action="{{ route('teacher.mutual-transfers.remove') }}"
              onsubmit="return confirm('{{ __('cancel_request') }}?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-semibold" style="color: #dc2626;">
                {{ __('cancel_request') }}
            </button>
        </form>
        @endif
    </div>

    <div class="p-5">
        @if($myPost)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('current_school') }}</p>
                    <p class="text-sm font-semibold" style="color: #111827;">
                        {{ $myPost->currentSchool?->name_en ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('preferred_division') }}</p>
                    <p class="text-sm font-semibold" style="color: #111827;">
                        {{ app()->getLocale() === 'si' && $myPost->preferredDivision?->name_si ? $myPost->preferredDivision->name_si : ($myPost->preferredDivision?->name_en ?? __('any_division')) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('preferred_subject') }}</p>
                    <p class="text-sm font-semibold" style="color: #111827;">{{ $myPost->preferred_subject ?: __('any_subject') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('contact_phone') }}</p>
                    <p class="text-sm font-semibold" style="color: #111827;">{{ $myPost->phone }}</p>
                </div>
                @if($myPost->notes_en || $myPost->notes_si)
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">
                        {{ app()->getLocale() === 'si' ? __('transfer_notes_si') : __('transfer_notes_en') }}
                    </p>
                    <p class="text-sm" style="color: #374151;">
                        {{ app()->getLocale() === 'si' && $myPost->notes_si ? $myPost->notes_si : $myPost->notes_en }}
                    </p>
                </div>
                @endif
                <div class="sm:col-span-2">
                    <p class="text-xs" style="color: #9ca3af;">{{ __('posted_on') }}: {{ $myPost->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <p class="text-xs font-semibold mb-3" style="color: var(--color-primary);">{{ __('edit_transfer_request') }}</p>
        @endif

        <form method="POST" action="{{ route('teacher.mutual-transfers.post') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #374151;">{{ __('preferred_division') }}</label>
                    <select name="preferred_division_id" class="w-full rounded-xl text-sm px-3 py-2.5" style="border: 1px solid #e5e7eb;">
                        <option value="">{{ __('any_division') }}</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('preferred_division_id', $myPost?->preferred_division_id) == $division->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'si' && $division->name_si ? $division->name_si : $division->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('preferred_division_id')<p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #374151;">{{ __('preferred_subject') }}</label>
                    <select name="preferred_subject" class="w-full rounded-xl text-sm px-3 py-2.5" style="border: 1px solid #e5e7eb;">
                        <option value="">{{ __('any_subject') }}</option>
                        @foreach($teachingSubjects as $level => $subjects)
                            <optgroup label="{{ strtoupper($level) }}">
                                @foreach($subjects as $name)
                                    <option value="{{ $name }}" {{ old('preferred_subject', $myPost?->preferred_subject) === $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('preferred_subject')<p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #374151;">{{ __('transfer_notes_en') }}</label>
                    <textarea name="notes_en" rows="3" class="w-full rounded-xl text-sm px-3 py-2.5" style="border: 1px solid #e5e7eb;">{{ old('notes_en', $myPost?->notes_en) }}</textarea>
                    @error('notes_en')<p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #374151;">{{ __('transfer_notes_si') }}</label>
                    <textarea name="notes_si" rows="3" class="w-full rounded-xl text-sm px-3 py-2.5" style="border: 1px solid #e5e7eb;">{{ old('notes_si', $myPost?->notes_si) }}</textarea>
                    @error('notes_si')<p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-4 sm:w-1/2">
                <label class="block text-xs font-medium mb-1.5" style="color: #374151;">{{ __('contact_phone') }}</label>
                <input type="text" name="phone" maxlength="10"
                       value="{{ old('phone', $myPost?->phone ?? $user->phone) }}"
                       placeholder="07XXXXXXXX"
                       class="w-full rounded-xl text-sm px-3 py-2.5" style="border: 1px solid #e5e7eb;">
                @error('phone')<p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-medium text-white" style="background: var(--color-primary);">
                {{ __('submit_request') }}
            </button>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- BROWSE OTHER REQUESTS                                         --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm" style="border: 1px solid #e5e7eb;">
    <div class="px-5 py-3" style="border-bottom: 1px solid #f3f4f6;">
        <h3 class="font-semibold text-sm" style="color: var(--color-primary);">{{ __('browse_transfer_requests') }}</h3>
    </div>

    <div class="p-5">
        {{-- Filter --}}
        <form method="GET" class="flex items-end gap-3 mb-5">
            <div class="flex-1 sm:max-w-xs">
                <label class="block text-xs font-medium mb-1.5" style="color: #374151;">{{ __('filter_by_division') }}</label>
                <select name="division_id" onchange="this.form.submit()" class="w-full rounded-xl text-sm px-3 py-2.5" style="border: 1px solid #e5e7eb;">
                    <option value="">{{ __('all_divisions') }}</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ $filterDivisionId == $division->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'si' && $division->name_si ? $division->name_si : $division->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- Match hint --}}
        @if(!$myPost)
        <div class="rounded-xl px-4 py-3 mb-5 text-xs" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;">
            {{ __('no_own_request_hint') }}
        </div>
        @endif

        {{-- List --}}
        @if($otherPosts->isEmpty())
        <p class="text-sm text-center py-8" style="color: #9ca3af;">{{ __('no_active_requests') }}</p>
        @else
        <div class="space-y-3">
            @foreach($otherPosts as $post)
            <div class="rounded-xl p-4"
                 style="{{ $post->is_match ? 'border: 2px solid #16a34a; background: #f0fdf4;' : 'border: 1px solid #e5e7eb;' }}">

                @if($post->is_match)
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold mb-2" style="background:#16a34a;color:#fff;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {{ __('potential_match') }}
                </div>
                @endif

                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm" style="color: #111827;">{{ $post->user?->name ?? '—' }}</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 mt-2">
                            <p class="text-xs" style="color: #6b7280;">
                                {{ __('current_school') }}:
                                <span style="color:#111827;font-weight:600;">{{ $post->currentSchool?->name_en ?? '—' }}</span>
                            </p>
                            <p class="text-xs" style="color: #6b7280;">
                                {{ __('preferred_division') }}:
                                <span style="color:#111827;font-weight:600;">
                                    {{ app()->getLocale() === 'si' && $post->preferredDivision?->name_si ? $post->preferredDivision->name_si : ($post->preferredDivision?->name_en ?? __('any_division')) }}
                                </span>
                            </p>
                            <p class="text-xs" style="color: #6b7280;">
                                {{ __('preferred_subject') }}:
                                <span style="color:#111827;font-weight:600;">{{ $post->preferred_subject ?: __('any_subject') }}</span>
                            </p>
                            <p class="text-xs" style="color: #6b7280;">
                                {{ __('posted_on') }}:
                                <span style="color:#111827;font-weight:600;">{{ $post->created_at->format('d M Y') }}</span>
                            </p>
                        </div>

                        @if($post->notes_en || $post->notes_si)
                        <p class="text-xs mt-2" style="color: #374151;">
                            {{ app()->getLocale() === 'si' && $post->notes_si ? $post->notes_si : $post->notes_en }}
                        </p>
                        @endif
                    </div>

                    <a href="tel:{{ $post->phone }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold flex-shrink-0"
                       style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" />
                        </svg>
                        {{ $post->phone }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection
