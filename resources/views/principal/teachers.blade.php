@extends('layouts.principal')
@section('title', __('nav_teachers'))
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_teachers') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('teachers_page_desc') }}</p>
    </div>
</div>

{{-- Staff list --}}
@if(isset($teachers) && $teachers->count())
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
        <div class="overflow-x-auto">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="background: var(--color-primary);">
                        <th style="text-align: left; padding: 12px 16px; color: #fff; font-size: 12px; font-weight: 600;">{{ __('name') }}</th>
                        <th style="text-align: left; padding: 12px 16px; color: #fff; font-size: 12px; font-weight: 600;">{{ __('designation') }}</th>
                        <th style="text-align: left; padding: 12px 16px; color: #fff; font-size: 12px; font-weight: 600;">{{ __('service_grade') }}</th>
                        <th style="text-align: left; padding: 12px 16px; color: #fff; font-size: 12px; font-weight: 600;">{{ __('appointment_type') }}</th>
                        <th style="text-align: center; padding: 12px 16px; color: #fff; font-size: 12px; font-weight: 600;">{{ __('status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr style="border-top: 1px solid #f3f4f6; {{ $loop->even ? 'background: #f9fafb;' : '' }}">
                        <td style="padding: 12px 16px;">
                            <p style="font-weight: 600; color: #111827; margin: 0;">{{ $teacher->name }}</p>
                            @if($teacher->salary_slip_no)
                                <p style="font-size: 11px; color: #9ca3af; margin: 2px 0 0;">{{ $teacher->salary_slip_no }}</p>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; color: #6b7280; font-size: 13px;">
                            {{ $teacher->designation ?? '—' }}
                        </td>
                        <td style="padding: 12px 16px;">
                            @if($teacher->service_grade)
                                <span style="font-size: 11px; padding: 2px 8px; border-radius: 20px; background: #eff6ff; color: #1d4ed8;">
                                    {{ str_replace('_', ' ', $teacher->service_grade) }}
                                </span>
                            @else
                                <span style="color: #d1d5db;">—</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #6b7280; text-transform: capitalize;">
                            {{ $teacher->appointment_type ? __('apt_' . $teacher->appointment_type) : '—' }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            @if($teacher->is_active)
                                <span style="font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #d1fae5; color: #065f46; font-weight: 600;">{{ __('active') }}</span>
                            @else
                                <span style="font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #fee2e2; color: #991b1b; font-weight: 600;">{{ __('inactive') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="bg-white rounded-2xl p-12 text-center" style="border: 2px dashed #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
        </svg>
        <h3 class="text-base font-semibold mb-2" style="color: #9ca3af;">{{ __('no_teachers_registered') }}</h3>
        <p class="text-sm max-w-md mx-auto" style="color: #d1d5db;">{{ __('teachers_registered_by_admin') }}</p>
    </div>
@endif

@endsection