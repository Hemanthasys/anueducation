{{-- Individual division profile page — fully responsive --}}
@extends('layouts.public')

@section('title', ($division->{'name_' . app()->getLocale()}) . ' — ' . __('division_profile'))

@section('content')

{{-- Back link --}}
<div class="w-full py-3" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <a href="{{ route('divisions.index') }}"
           class="inline-flex items-center gap-1 text-xs text-white/70 no-underline hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('back_to_divisions') }}
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-6 md:py-8">

    {{-- Header card --}}
    <div class="rounded-2xl p-4 md:p-6 mb-6" style="background: var(--color-primary);">
        <div class="flex items-start gap-3 md:gap-4 flex-wrap">
            {{-- Icon --}}
            <div class="flex-shrink-0 rounded-xl flex items-center justify-center"
                 style="width: 56px; height: 56px; background: rgba(255,255,255,0.12);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 md:w-9 md:h-9" style="color: var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            {{-- Name --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-lg md:text-xl font-bold leading-snug" style="color: var(--color-accent);">
                    {{ $division->{'name_' . app()->getLocale()} }}
                </h1>
                <p class="text-xs md:text-sm mt-1" style="color: rgba(255,255,255,0.6);">
                    {{ app()->getLocale() === 'si' ? $division->name_en : $division->name_si }}
                </p>
            </div>
        </div>

        {{-- Director info --}}
        @if($division->director)
        <div class="mt-4 pt-4 flex flex-wrap items-center gap-3"
             style="border-top: 0.5px solid rgba(255,255,255,0.15);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background: rgba(255,255,255,0.12);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ __('principal') }}</p>
                <p class="text-sm font-semibold text-white">{{ $division->director->name }}</p>
            </div>
            <div class="flex flex-wrap gap-2 ml-auto">
                @if($division->phone)
                <a href="tel:{{ $division->phone }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg no-underline text-white"
                   style="background: rgba(255,255,255,0.12);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    {{ $division->phone }}
                </a>
                @endif
                @if($division->email)
                <a href="mailto:{{ $division->email }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg no-underline text-white"
                   style="background: rgba(255,255,255,0.12);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    {{ $division->email }}
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Two column layout: stacks on mobile --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">

        {{-- Left column: stats + charts --}}
        <div class="flex flex-col gap-4">

            {{-- Student stats --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('student_statistics') }}</p>
                {{-- Male / Female / Total --}}
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('male') }}</p>
                        <p class="stat-pill-value">—</p>
                    </div>
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('female') }}</p>
                        <p class="stat-pill-value">—</p>
                    </div>
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('total') }}</p>
                        <p class="stat-pill-value">—</p>
                    </div>
                </div>
                {{-- Schools / ISAs --}}
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('schools') }}</p>
                        <p class="stat-pill-value" style="color: var(--color-primary);">{{ $totalSchools }}</p>
                    </div>
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('isa_count') }}</p>
                        <p class="stat-pill-value" style="color: var(--color-primary);">{{ $division->isas->count() }}</p>
                    </div>
                </div>
                <p class="text-xs text-center" style="color: #9ca3af;">{{ __('data_updated_annually') }}</p>
            </div>

            {{-- Type breakdown --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('type_breakdown') }}</p>
                <div class="flex items-center gap-4">
                    {{-- Fixed size donut --}}
                    <div style="width: 90px; height: 90px; min-width: 90px;">
                        <canvas id="typeChart" width="90" height="90"></canvas>
                    </div>
                    {{-- Legend --}}
                    <div class="flex-1 min-w-0">
                        @php $typeColors = ['1AB' => '#3d1a78', '1C' => '#e8a020', '2' => '#0d9e8a', '3' => '#e05a4e']; @endphp
                        @foreach($typeBreakdown as $type => $count)
                        <div class="flex items-center justify-between py-1 text-sm">
                            <span class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                      style="background: {{ $typeColors[$type] ?? '#888' }};"></span>
                                {{ __('type') }} {{ $type }}
                            </span>
                            <span class="font-semibold">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Medium breakdown --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('medium_breakdown') }}</p>
                <div class="flex items-center gap-4">
                    <div style="width: 90px; height: 90px; min-width: 90px;">
                        <canvas id="mediumChart" width="90" height="90"></canvas>
                    </div>
                    <div class="flex-1 min-w-0">
                        @php $mediumColors = ['sinhala' => '#3d1a78', 'tamil' => '#e8a020', 'english' => '#0d9e8a', 'mixed' => '#e05a4e']; @endphp
                        @foreach($mediumBreakdown as $medium => $count)
                        <div class="flex items-center justify-between py-1 text-sm">
                            <span class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                      style="background: {{ $mediumColors[$medium] ?? '#888' }};"></span>
                                {{ ucfirst($medium) }}
                            </span>
                            <span class="font-semibold">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- Right column: contact + map + staff --}}
        <div class="flex flex-col gap-4">

            {{-- Contact details --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('contact_location') }}</p>

                @if($division->address)
                <div class="profile-info-row">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        {{ __('address') ?? 'Address' }}
                    </span>
                    <span class="profile-info-value">{{ $division->address }}</span>
                </div>
                @endif

                @if($division->phone)
                <div class="profile-info-row">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        {{ __('contact') }}
                    </span>
                    <a href="tel:{{ $division->phone }}" class="profile-info-value no-underline" style="color: var(--color-primary);">
                        {{ $division->phone }}
                    </a>
                </div>
                @endif

                @if($division->email)
                <div class="profile-info-row">
                    <span class="profile-info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        {{ __('email') ?? 'Email' }}
                    </span>
                    <a href="mailto:{{ $division->email }}" class="profile-info-value no-underline" style="color: var(--color-primary);">
                        {{ $division->email }}
                    </a>
                </div>
                @endif

                @if(!$division->address && !$division->phone && !$division->email)
                <p class="text-sm text-center py-4" style="color: #9ca3af;">{{ __('no_division_data') }}</p>
                @endif

                {{-- Google Maps embed --}}
                @if($division->google_map_url)
                <div class="w-full rounded-xl overflow-hidden mt-4" style="height: 200px;">
                    <iframe width="100%" height="100%" frameborder="0" style="border: 0;"
                            src="{{ str_replace('/view', '/embed', $division->google_map_url) }}"
                            allowfullscreen>
                    </iframe>
                </div>
                <a href="{{ $division->google_map_url }}" target="_blank"
                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-lg text-sm font-semibold no-underline text-white transition"
                   style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c-.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                    {{ __('get_directions') }}
                </a>
                @endif
            </div>

            {{-- Staff list --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('division_staff') }}</p>
                @if($division->staff->count())
                <div class="flex flex-col gap-3">
                    @foreach($division->staff as $staff)
                    <div class="flex items-center gap-3 p-3 rounded-xl" style="background: #f9fafb;">
                        @if($staff->photo)
                            <img src="{{ Storage::url($staff->photo) }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-semibold"
                                 style="background: #eeedfe; color: #3c3489;">
                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate" style="color: var(--color-primary);">{{ $staff->name }}</p>
                            <p class="text-xs truncate" style="color: #6b7280;">{{ $staff->designation }}</p>
                        </div>
                        @if($staff->phone)
                        <a href="tel:{{ $staff->phone }}"
                           class="flex-shrink-0 text-xs no-underline px-2 py-1 rounded-lg hidden sm:block"
                           style="background: var(--color-primary); color: white;">
                            {{ $staff->phone }}
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-center py-4" style="color: #9ca3af;">{{ __('no_staff') }}</p>
                @endif
            </div>

        </div>
    </div>

            {{-- Results analyzer placeholder --}}
            <div class="profile-card" style="border: 0.5px dashed #e5e7eb; background: #f9fafb;">
                <p class="profile-section-title">{{ __('academic_results') }}</p>
                <div class="text-center py-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <p class="text-sm font-semibold mb-1" style="color: var(--color-primary);">{{ __('academic_results') }}</p>
                    <p class="text-xs mb-4" style="color: #9ca3af;">{{ __('phase2_note') }}</p>
                    {{-- 4 exam type cards --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            ['key' => 'grade5_results', 'exam' => 'grade5'],
                            ['key' => 'ol_results',     'exam' => 'ol'],
                            ['key' => 'al_results',     'exam' => 'al'],
                            ['key' => 'term_results',   'exam' => 'term'],
                        ] as $result)
                        <div class="p-3 rounded-xl border border-gray-200 text-center bg-white opacity-60">
                            <p class="text-xs font-semibold" style="color: var(--color-primary);">{{ __($result['key']) }}</p>
                            <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('phase2_note') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

    {{-- Schools table: full width --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-6"
         x-data="schoolDirectory({{ json_encode($schools) }}, '{{ app()->getLocale() }}')">

        {{-- Section heading --}}
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <p class="profile-section-title" style="margin-bottom: 0;">
                {{ app()->getLocale() === 'si' ? $division->name_si : $division->name_en }} — {{ __('schools') }}
            </p>
            <span class="text-sm font-semibold px-3 py-1 rounded-full"
                  style="background: var(--color-accent); color: var(--color-primary);">
                {{ $totalSchools }} {{ __('schools') }}
            </span>
        </div>

        {{-- Filters: wrap on mobile --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <input type="text"
                   x-model="search"
                   @input="applyFilters()"
                   placeholder="{{ __('search_placeholder') }}"
                   class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm"
                   style="min-width: 150px;">

            <select x-model="selectedType" @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_types') }}</option>
                <template x-for="type in availableTypes" :key="type">
                    <option :value="type" x-text="type"></option>
                </template>
            </select>

            <select x-model="selectedOwnership" @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_ownership') }}</option>
                <template x-for="ownership in availableOwnerships" :key="ownership">
                    <option :value="ownership" x-text="ownership === 'national' ? '{{ __('national') }}' : '{{ __('provincial') }}'"></option>
                </template>
            </select>

            <select x-model="selectedMedium" @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_mediums') }}</option>
                <template x-for="medium in availableMediums" :key="medium">
                    <option :value="medium" x-text="medium"></option>
                </template>
            </select>

            <button @click="reset()"
                    class="px-3 py-2 rounded-lg text-sm font-medium border transition"
                    style="border-color: var(--color-primary); color: var(--color-primary);">
                {{ __('reset') }}
            </button>
        </div>

        {{-- Results count --}}
        <p class="text-xs mb-3" style="color: #9ca3af;">
            <span x-text="filtered.length"></span> {{ __('of') }} {{ $totalSchools }} {{ __('schools') }}
        </p>

        {{-- Table with horizontal scroll on mobile --}}
        <div class="overflow-x-auto">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; min-width: 500px;">
                <thead>
                    <tr style="background: var(--color-primary);">
                        <th class="table-th" style="width: 40px;">#</th>
                        <th class="table-th">{{ __('schools') }}</th>
                        <th class="table-th">{{ __('census_no') }}</th>
                        <th class="table-th">{{ __('type') }}</th>
                        <th class="table-th">{{ __('medium') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="paginated.length === 0">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">
                                {{ __('no_schools_found') }}
                            </td>
                        </tr>
                    </template>
                    <template x-for="(school, index) in paginated" :key="school.id">
                        <tr style="border-bottom: 0.5px solid #f3f4f6;"
                            onmouseover="this.style.background='#fafafa'"
                            onmouseout="this.style.background='white'">
                            <td class="table-td" style="color: #9ca3af; text-align: center;"
                                x-text="(currentPage - 1) * perPage + index + 1"></td>
                            <td class="table-td">
                                <div class="font-semibold" style="color: var(--color-primary);"
                                     x-text="locale === 'si' ? school.name_si : school.name_en"></div>
                                <div style="font-size: 0.72rem; color: #9ca3af;"
                                     x-text="locale === 'si' ? school.name_en : school.name_si"></div>
                            </td>
                            <td class="table-td" style="color: #6b7280;" x-text="school.census_no"></td>
                            <td class="table-td"><span class="badge-type" x-text="school.type"></span></td>
                            <td class="table-td"><span class="badge-medium" x-text="school.medium"></span></td>
                            <td class="table-td">
                                <a :href="'/schools/' + school.census_no" class="btn-view">
                                    {{ __('view_profile') }}
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-center gap-2 mt-6" x-show="totalPages > 1">
            <button @click="prevPage()" :disabled="currentPage === 1"
                    class="px-3 py-1.5 rounded-lg border text-sm"
                    :style="currentPage === 1 ? 'opacity: 0.4;' : 'cursor: pointer;'"
                    style="border-color: #e5e7eb;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <template x-for="page in totalPages" :key="page">
                <button @click="goToPage(page)"
                        class="px-3 py-1.5 rounded-lg border text-sm"
                        :style="currentPage === page
                            ? 'background: var(--color-primary); color: white; border-color: var(--color-primary);'
                            : 'border-color: #e5e7eb; cursor: pointer;'"
                        x-text="page">
                </button>
            </template>
            <button @click="nextPage()" :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 rounded-lg border text-sm"
                    :style="currentPage === totalPages ? 'opacity: 0.4;' : 'cursor: pointer;'"
                    style="border-color: #e5e7eb;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

    </div>

</div>

{{-- Styles --}}
<style>
    .profile-card { background: white; border: 0.5px solid #e5e7eb; border-radius: 16px; padding: 1.25rem; }
    .profile-section-title { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; padding-bottom: 8px; border-bottom: 2px solid var(--color-accent); display: inline-block; margin-bottom: 1rem; }
    .profile-info-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 8px 0; border-bottom: 0.5px solid #f3f4f6; font-size: 0.83rem; }
    .profile-info-row:last-of-type { border-bottom: none; }
    .profile-info-label { display: flex; align-items: center; gap: 6px; color: #6b7280; flex-shrink: 0; }
    .profile-info-value { color: #1f2937; font-weight: 500; text-align: right; }
    .stat-pill { background: #f9fafb; border-radius: 10px; padding: 10px 6px; text-align: center; }
    .stat-pill-label { font-size: 0.7rem; color: #9ca3af; margin-bottom: 3px; }
    .stat-pill-value { font-size: 1.3rem; font-weight: 700; color: var(--color-primary); }
    .table-th { padding: 10px 12px; text-align: left; font-size: 0.72rem; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; }
    .table-td { padding: 10px 12px; vertical-align: middle; }
    .badge-type { display: inline-block; font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; background: #eeedfe; color: #3c3489; }
    .badge-medium { display: inline-block; font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; background: #faeeda; color: #633806; }
    .btn-view { display: inline-block; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; border: 1.5px solid var(--color-primary); color: var(--color-primary); text-decoration: none; white-space: nowrap; transition: all 0.2s; }
    .btn-view:hover { background: var(--color-primary); color: white; }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    // Type breakdown donut chart
    const typeData = @json($typeBreakdown);
    const typeColors = { '1AB': '#3d1a78', '1C': '#e8a020', '2': '#0d9e8a', '3': '#e05a4e' };

    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(typeData),
            datasets: [{
                data: Object.values(typeData),
                backgroundColor: Object.keys(typeData).map(t => typeColors[t] || '#888'),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } },
        }
    });

    // Medium breakdown donut chart
    const mediumData = @json($mediumBreakdown);
    const mediumColors = { 'sinhala': '#3d1a78', 'tamil': '#e8a020', 'english': '#0d9e8a', 'mixed': '#e05a4e' };

    new Chart(document.getElementById('mediumChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(mediumData),
            datasets: [{
                data: Object.values(mediumData),
                backgroundColor: Object.keys(mediumData).map(m => mediumColors[m] || '#888'),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } },
        }
    });

    // School directory Alpine.js
    function schoolDirectory(allSchools, locale) {
        return {
            locale: locale,
            allSchools: allSchools,
            filtered: allSchools,
            search: '',
            selectedType: '',
            selectedOwnership: '',
            selectedMedium: '',
            currentPage: 1,
            perPage: 15,
            get availableTypes() {
                return [...new Set(this.allSchools.map(s => s.type).filter(Boolean))].sort();
            },
            get availableOwnerships() {
                return [...new Set(this.allSchools.map(s => s.ownership).filter(Boolean))].sort();
            },
            get availableMediums() {
                return [...new Set(this.allSchools.map(s => s.medium).filter(Boolean))].sort();
            },
            get totalPages() { return Math.ceil(this.filtered.length / this.perPage); },
            get paginated() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filtered.slice(start, start + this.perPage);
            },
            applyFilters() {
                this.currentPage = 1;
                let result = this.allSchools;
                if (this.search) {
                    const q = this.search.toLowerCase();
                    result = result.filter(s =>
                        s.name_en?.toLowerCase().includes(q) ||
                        s.name_si?.includes(this.search) ||
                        s.census_no?.toString().includes(q)
                    );
                }
                if (this.selectedType) result = result.filter(s => s.type === this.selectedType);
                if (this.selectedOwnership) result = result.filter(s => s.ownership === this.selectedOwnership);
                if (this.selectedMedium) result = result.filter(s => s.medium === this.selectedMedium);
                this.filtered = result;
            },
            goToPage(page) { this.currentPage = page; },
            prevPage() { if (this.currentPage > 1) this.currentPage--; },
            nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
            reset() {
                this.search = '';
                this.selectedType = '';
                this.selectedOwnership = '';
                this.selectedMedium = '';
                this.applyFilters();
            },
            init() { this.applyFilters(); }
        }
    }
</script>
@endpush

@endsection