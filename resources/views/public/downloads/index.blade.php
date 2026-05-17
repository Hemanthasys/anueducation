{{-- Downloads page: sidebar category filter + client-side filtering --}}
@extends('layouts.public')

@section('title', __('downloads_page'))

@section('content')

{{-- Page header --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('downloads_page') }}
        </h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8"
     x-data="downloadsPage({{ json_encode($downloads) }}, '{{ app()->getLocale() }}')">

    <div style="display: flex; gap: 24px; align-items: flex-start;">

        {{-- Left sidebar: category tree --}}
        <div class="flex-shrink-0 hidden md:block"
             style="width: 220px; position: sticky; top: 20px;">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-wide" style="color: var(--color-primary);">
                        {{ __('categories') ?? 'Categories' }}
                    </p>
                </div>

                {{-- All documents --}}
                <button @click="setCategory('')"
                        class="w-full text-left px-4 py-3 flex items-center gap-2 text-sm font-semibold transition border-b border-gray-50"
                        :style="selectedCategory === '' ? 'background: var(--color-primary); color: white;' : 'color: #374151;'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                    </svg>
                    {{ __('all_documents') }}
                    <span class="ml-auto text-xs px-1.5 py-0.5 rounded-full"
                          :style="selectedCategory === '' ? 'background: rgba(255,255,255,0.2); color: white;' : 'background: #f3f4f6; color: #6b7280;'"
                          x-text="allDownloads.length">
                    </span>
                </button>

                {{-- Category items --}}
                @php
                    $categories = [
                        'circulars'     => ['icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                        'forms'         => ['icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z'],
                        'templates'     => ['icon' => 'M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0118 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75-.125V6.75m0-2.25A1.125 1.125 0 0121.75 5.625v1.5m-18.375-3H3.375'],
                        'reports'       => ['icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                        'guidelines'    => ['icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
                        'exam_papers'   => ['icon' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10'],
                        'answer_sheets' => ['icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'general_forms' => ['icon' => 'M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z'],
                        'other'         => ['icon' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z'],
                    ];
                @endphp

                @foreach($categories as $key => $cat)
                <button @click="setCategory('{{ $key }}')"
                        class="w-full text-left px-4 py-2.5 flex items-center gap-2 text-sm transition border-b border-gray-50"
                        :style="selectedCategory === '{{ $key }}' ? 'background: var(--color-primary); color: white;' : 'color: #374151;'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cat['icon'] }}" />
                    </svg>
                    {{ __($key) }}
                    <span class="ml-auto text-xs px-1.5 py-0.5 rounded-full"
                          :style="selectedCategory === '{{ $key }}' ? 'background: rgba(255,255,255,0.2); color: white;' : 'background: #f3f4f6; color: #6b7280;'"
                          x-text="allDownloads.filter(d => d.category === '{{ $key }}').length">
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Right content area --}}
        <div style="flex: 1; min-width: 0;">

            {{-- Top filter bar --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
                <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">

                    {{-- Search --}}
                    <input type="text"
                           x-model="search"
                           @input="applyFilters()"
                           placeholder="{{ __('search_documents') }}"
                           class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-sm"
                           style="min-width: 180px;">

                    {{-- Year filter --}}
                    <select x-model="selectedYear"
                            @change="applyFilters()"
                            class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                        <option value="">{{ __('all_years') }}</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>

                    {{-- Reset --}}
                    <button @click="reset()"
                            class="px-4 py-2 rounded-lg text-sm font-medium border transition"
                            style="border-color: var(--color-primary); color: var(--color-primary);">
                        {{ __('reset') }}
                    </button>

                </div>
            </div>

            {{-- Results count --}}
            <p class="text-sm text-gray-500 mb-4">
                <span x-text="filtered.length"></span> {{ __('of') }} {{ count(json_decode(json_encode($downloads), true)) }} {{ __('downloads_page') }}
            </p>

            {{-- File list --}}
            <div style="display: flex; flex-direction: column; gap: 10px;">

                <template x-if="filtered.length === 0">
                    <div class="text-center py-16 text-gray-400 bg-white rounded-xl border border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p>{{ __('no_downloads_found') }}</p>
                    </div>
                </template>

                <template x-for="file in filtered" :key="file.id">
                    {{-- Single file row --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4"
                         style="display: flex; align-items: center; gap: 16px;">

                        {{-- File type icon --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                             :style="file.file_ext === 'pdf' ? 'background: #fee2e2;' : file.file_ext === 'xlsx' || file.file_ext === 'xls' ? 'background: #dcfce7;' : 'background: #dbeafe;'">
                            {{-- PDF icon --}}
                            <template x-if="file.file_ext === 'pdf'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: #dc2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </template>
                            {{-- Excel icon --}}
                            <template x-if="file.file_ext === 'xlsx' || file.file_ext === 'xls'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: #16a34a;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0118 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75-.125V6.75m0-2.25A1.125 1.125 0 0121.75 5.625v1.5m-18.375-3H3.375" />
                                </svg>
                            </template>
                            {{-- Word/default icon --}}
                            <template x-if="file.file_ext !== 'pdf' && file.file_ext !== 'xlsx' && file.file_ext !== 'xls'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: #2563eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </template>
                        </div>

                        {{-- File info --}}
                        <div style="flex: 1; min-width: 0;">
                            <p class="font-semibold text-sm truncate" style="color: var(--color-primary);"
                               x-text="locale === 'si' && file.title_si ? file.title_si : file.title_en">
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                                {{-- Category badge --}}
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                      style="background: var(--color-accent); color: var(--color-primary);"
                                      x-text="file.category">
                                </span>
                                {{-- Year --}}
                                <span class="text-xs text-gray-400" x-text="file.year"></span>
                                {{-- Download count --}}
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    <span x-text="file.download_count"></span>
                                </span>
                            </div>
                        </div>

                        {{-- Download button --}}
                        <button @click="preview(file)"
                                class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                                style="background: var(--color-primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            {{ __('download') }}
                        </button>

                    </div>
                </template>

            </div>
        </div>
    </div>

    {{-- Preview modal --}}
    <div x-show="isOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75); display: none;"
         @click.self="closePreview()">

        <div class="bg-white rounded-2xl w-full shadow-2xl flex flex-col"
             style="max-width: 800px; max-height: 90vh;"
             @click.stop>

            {{-- Modal header --}}
            <div class="flex items-center justify-between p-5 border-b border-gray-100 flex-shrink-0">
                <div>
                    <p class="font-semibold text-sm" style="color: var(--color-primary);"
                       x-text="selectedFile.title_en">
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5"
                       x-text="selectedFile.category + ' · ' + selectedFile.year">
                    </p>
                </div>
                <button @click="closePreview()"
                        class="p-2 rounded-full transition"
                        style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="overflow-y-auto flex-1 p-5">

                {{-- Google Drive preview --}}
                <template x-if="selectedFile.is_drive && selectedFile.drive_id">
                    <iframe :src="'https://drive.google.com/file/d/' + selectedFile.drive_id + '/preview'"
                            class="w-full rounded-lg border border-gray-200"
                            style="height: 500px;"
                            allow="autoplay">
                    </iframe>
                </template>

                {{-- PDF preview --}}
                <template x-if="!selectedFile.is_drive && selectedFile.file_ext === 'pdf'">
                    <iframe :src="selectedFile.file_url"
                            class="w-full rounded-lg border border-gray-200"
                            style="height: 500px;">
                    </iframe>
                </template>

                {{-- Non-PDF, non-Drive: show message --}}
                <template x-if="!selectedFile.is_drive && selectedFile.file_ext !== 'pdf'">
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-sm text-gray-500 mb-6">{{ __('preview_not_available') }} {{ __('click_to_download') }}</p>
                    </div>
                </template>

            </div>

            {{-- Modal footer: download button --}}
<div class="p-5 border-t border-gray-100 flex-shrink-0">

    {{-- Google Drive: open in new tab --}}
    <template x-if="selectedFile.is_drive">
        <a :href="selectedFile.drive_url"
           target="_blank"
           @click="incrementDownload(selectedFile.id)"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white no-underline transition"
           style="background: var(--color-primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
            {{ __('download') }}
        </a>
    </template>

    {{-- Local file: direct download --}}
            <template x-if="!selectedFile.is_drive">
                <a :href="selectedFile.file_url"
                :download="selectedFile.title_en"
                @click="incrementDownload(selectedFile.id)"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white no-underline transition"
                style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    {{ __('download') }}
                </a>
            </template>

        </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
function downloadsPage(allDownloads, locale) {
    return {
        locale: locale,
        allDownloads: allDownloads,
        filtered: allDownloads,
        search: '',
        selectedCategory: '',
        selectedYear: '',
        isOpen: false,
        selectedFile: {},

        // Set category from sidebar
        setCategory(category) {
            this.selectedCategory = category;
            this.applyFilters();
        },

        // Apply all filters
        applyFilters() {
            let result = this.allDownloads;

            // Category filter
            if (this.selectedCategory) {
                result = result.filter(d => d.category === this.selectedCategory);
            }

            // Year filter
            if (this.selectedYear) {
                result = result.filter(d => d.year == this.selectedYear);
            }

            // Search filter
            if (this.search) {
                const q = this.search.toLowerCase();
                result = result.filter(d =>
                    d.title_en?.toLowerCase().includes(q) ||
                    d.title_si?.includes(this.search)
                );
            }

            this.filtered = result;
        },

        // Open preview modal
        preview(file) {
            this.selectedFile = file;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        // Close preview modal
        closePreview() {
            this.isOpen = false;
            this.selectedFile = {};
            document.body.style.overflow = '';
        },

        // Increment download count via API
        incrementDownload(id) {
            fetch('/downloads/' + id + '/increment', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json',
                }
            });
},

        // Reset all filters
        reset() {
            this.search = '';
            this.selectedCategory = '';
            this.selectedYear = '';
            this.applyFilters();
        },

        init() { this.applyFilters(); }
    }
}

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelector('[x-data]')?._x_dataStack?.[0]?.closePreview();
    }
});
</script>
@endpush

@endsection