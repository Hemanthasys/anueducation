{{-- School Directory: client-side filtering with Alpine.js, cascading filters --}}
@extends('layouts.public')

@section('title', __('school_directory'))

@section('content')

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('school_directory') }}
        </h1>
        <p class="mt-1 text-sm text-white/70">
            {{ count(json_decode(json_encode($schools), true)) }} {{ __('schools') }}
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8"
     x-data="schoolDirectory({{ json_encode($schools) }}, '{{ app()->getLocale() }}')">

    {{-- Search + Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
        <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">

            {{-- Search input --}}
            <input type="text"
                   x-model="search"
                   @input="applyFilters()"
                   placeholder="{{ __('search_placeholder') }}"
                   class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-sm"
                   style="min-width: 200px;">

            {{-- Division filter --}}
            <select x-model="selectedDivision"
                    @change="onDivisionChange()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_divisions') }}</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ app()->getLocale() === 'si' ? $division->name_si : $division->name_en }}</option>
                @endforeach
            </select>

            {{-- Type filter --}}
            <select x-model="selectedType"
                    @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_types') }}</option>
                <template x-for="type in availableTypes" :key="type">
                    <option :value="type" x-text="type"></option>
                </template>
            </select>

            {{-- Ownership filter --}}
            <select x-model="selectedOwnership"
                    @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_ownership') }}</option>
                <template x-for="ownership in availableOwnerships" :key="ownership">
                    <option :value="ownership" x-text="ownership === 'national' ? '{{ __('national') }}' : '{{ __('provincial') }}'"></option>
                </template>
            </select>

            {{-- Medium filter --}}
            <select x-model="selectedMedium"
                    @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white">
                <option value="">{{ __('all_mediums') }}</option>
                <template x-for="medium in availableMediums" :key="medium">
                    <option :value="medium" x-text="medium"></option>
                </template>
            </select>

            {{-- Reset button --}}
            <button @click="reset()"
                    class="px-4 py-2 rounded-lg text-sm font-medium border transition"
                    style="border-color: var(--color-primary); color: var(--color-primary); background: white;">
                {{ __('reset') }}
            </button>

        </div>
    </div>

    {{-- Results count --}}
    <p class="text-sm text-gray-500 mb-4">
        {{ __('showing_results', [':count' => '', ':total' => '']) }}
        <span x-text="filtered.length"></span> {{ __('of') ?? 'of' }}
        <span>{{ count(json_decode(json_encode($schools), true)) }}</span>
        {{ __('schools') }}
    </p>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">

                {{-- Table header --}}
                <thead>
                    <tr style="background: var(--color-primary);">
                        <th class="table-th" style="width: 40px;">#</th>
                        <th class="table-th" @click="sort('name')" style="cursor: pointer; min-width: 200px;">
                            {{ __('schools') }}
                            <span x-text="sortField === 'name' ? (sortDir === 'asc' ? ' ↑' : ' ↓') : ''"></span>
                        </th>
                        <th class="table-th">{{ __('census_no') }}</th>
                        <th class="table-th">{{ __('division') }}</th>
                        <th class="table-th">{{ __('type') }}</th>
                        <th class="table-th">{{ __('ownership') }}</th>
                        <th class="table-th">{{ __('medium') }}</th>
                        <th class="table-th">{{ __('students') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>

                {{-- Table body --}}
                <tbody>
                    <template x-if="paginated.length === 0">
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #9ca3af;">
                                {{ __('no_schools_found') }}
                            </td>
                        </tr>
                    </template>

                    <template x-for="(school, index) in paginated" :key="school.id">
                        <tr style="border-bottom: 0.5px solid #f3f4f6;"
                            onmouseover="this.style.background='#fafafa'"
                            onmouseout="this.style.background='white'">

                            {{-- Row number --}}
                            <td class="table-td" style="color: #9ca3af; text-align: center;"
                                x-text="(currentPage - 1) * perPage + index + 1">
                            </td>

                            {{-- School name: EN or SI based on locale --}}
                            <td class="table-td">
                                <div class="font-semibold" style="color: var(--color-primary);"
                                     x-text="locale === 'si' ? school.name_si : school.name_en">
                                </div>
                                <div style="font-size: 0.72rem; color: #9ca3af;"
                                     x-text="locale === 'si' ? school.name_en : school.name_si">
                                </div>
                            </td>

                            {{-- Census number --}}
                            <td class="table-td" style="color: #6b7280;" x-text="school.census_no"></td>

                            {{-- Division --}}
                            <td class="table-td">
                                <span class="badge-division"
                                      x-text="locale === 'si' ? school.division_si : school.division_en">
                                </span>
                            </td>

                            {{-- Type --}}
                            <td class="table-td">
                                <span class="badge-type" x-text="school.type"></span>
                            </td>

                            {{-- Ownership --}}
                            <td class="table-td">
                                <span :class="school.ownership === 'national' ? 'badge-national' : 'badge-provincial'"
                                      x-text="school.ownership === 'national' ? '{{ __('national') }}' : '{{ __('provincial') }}'">
                                </span>
                            </td>

                            {{-- Medium --}}
                            <td class="table-td">
                                <span class="badge-medium" x-text="school.medium"></span>
                            </td>

                            {{-- Students placeholder --}}
                            <td class="table-td" style="color: #9ca3af; text-align: center;">
                                {{ __('not_available') }}
                            </td>

                            {{-- View profile button --}}
                            <td class="table-td">
                                <a :href="'/schools/' + school.census_no"
                                   class="btn-view">
                                    {{ __('view_profile') }}
                                </a>
                            </td>

                        </tr>
                    </template>
                </tbody>

            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-center gap-2 mt-6" x-show="totalPages > 1">

        {{-- Previous --}}
        <button @click="prevPage()"
                :disabled="currentPage === 1"
                class="px-3 py-1.5 rounded-lg border text-sm transition"
                :style="currentPage === 1 ? 'opacity: 0.4; cursor: not-allowed;' : 'cursor: pointer;'"
                style="border-color: #e5e7eb;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        {{-- Page numbers --}}
        <template x-for="page in totalPages" :key="page">
            <button @click="goToPage(page)"
                    class="px-3 py-1.5 rounded-lg border text-sm transition"
                    :style="currentPage === page
                        ? 'background: var(--color-primary); color: white; border-color: var(--color-primary);'
                        : 'border-color: #e5e7eb; cursor: pointer;'"
                    x-text="page">
            </button>
        </template>

        {{-- Next --}}
        <button @click="nextPage()"
                :disabled="currentPage === totalPages"
                class="px-3 py-1.5 rounded-lg border text-sm transition"
                :style="currentPage === totalPages ? 'opacity: 0.4; cursor: not-allowed;' : 'cursor: pointer;'"
                style="border-color: #e5e7eb;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

    </div>

</div>

{{-- Table styles --}}
<style>
    .table-th {
        padding: 12px 14px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255,255,255,0.85);
        white-space: nowrap;
    }
    .table-td {
        padding: 10px 14px;
        vertical-align: middle;
    }
    .badge-division {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        background: #e1f5ee;
        color: #085041;
    }
    .badge-type {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        background: #eeedfe;
        color: #3c3489;
    }
    .badge-medium {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        background: #faeeda;
        color: #633806;
    }
    .badge-national {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        background: #e6f1fb;
        color: #0c447c;
    }
    .badge-provincial {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        background: #faece7;
        color: #993c1d;
    }
    .btn-view {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 6px;
        border: 1.5px solid var(--color-primary);
        color: var(--color-primary);
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .btn-view:hover {
        background: var(--color-primary);
        color: white;
    }
</style>

@push('scripts')
<script>
function schoolDirectory(allSchools, locale) {
    return {
        locale: locale,
        allSchools: allSchools,
        filtered: allSchools,
        search: '',
        selectedDivision: '',
        selectedType: '',
        selectedOwnership: '',
        selectedMedium: '',
        sortField: 'name',
        sortDir: 'asc',
        currentPage: 1,
        perPage: 20,

        // Available filter options (cascade based on division)
        get availableTypes() {
            let pool = this.selectedDivision
                ? this.allSchools.filter(s => s.division_id == this.selectedDivision)
                : this.allSchools;
            return [...new Set(pool.map(s => s.type).filter(Boolean))].sort();
        },
        get availableOwnerships() {
            let pool = this.selectedDivision
                ? this.allSchools.filter(s => s.division_id == this.selectedDivision)
                : this.allSchools;
            return [...new Set(pool.map(s => s.ownership).filter(Boolean))].sort();
        },
        get availableMediums() {
            let pool = this.selectedDivision
                ? this.allSchools.filter(s => s.division_id == this.selectedDivision)
                : this.allSchools;
            return [...new Set(pool.map(s => s.medium).filter(Boolean))].sort();
        },

        // Total pages for pagination
        get totalPages() {
            return Math.ceil(this.filtered.length / this.perPage);
        },

        // Current page slice
        get paginated() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        // When division changes, reset dependent filters and reapply
        onDivisionChange() {
            this.selectedType = '';
            this.selectedOwnership = '';
            this.selectedMedium = '';
            this.applyFilters();
        },

        // Apply all filters + search + sort
        applyFilters() {
            this.currentPage = 1;
            let result = this.allSchools;

            // Search by name or census number
            if (this.search) {
                const q = this.search.toLowerCase();
                result = result.filter(s =>
                    s.name_en?.toLowerCase().includes(q) ||
                    s.name_si?.includes(this.search) ||
                    s.census_no?.toString().includes(q)
                );
            }

            // Division filter
            if (this.selectedDivision) {
                result = result.filter(s => s.division_id == this.selectedDivision);
            }

            // Type filter
            if (this.selectedType) {
                result = result.filter(s => s.type === this.selectedType);
            }

            // Ownership filter
            if (this.selectedOwnership) {
                result = result.filter(s => s.ownership === this.selectedOwnership);
            }

            // Medium filter
            if (this.selectedMedium) {
                result = result.filter(s => s.medium === this.selectedMedium);
            }

            // Sort
            result = result.sort((a, b) => {
                const nameA = this.locale === 'si' ? a.name_si : a.name_en;
                const nameB = this.locale === 'si' ? b.name_si : b.name_en;
                return this.sortDir === 'asc'
                    ? (nameA || '').localeCompare(nameB || '')
                    : (nameB || '').localeCompare(nameA || '');
            });

            this.filtered = result;
        },

        // Sort by column
        sort(field) {
            if (this.sortField === field) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDir = 'asc';
            }
            this.applyFilters();
        },

        // Pagination controls
        goToPage(page) { this.currentPage = page; },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

        // Reset all filters
        reset() {
            this.search = '';
            this.selectedDivision = '';
            this.selectedType = '';
            this.selectedOwnership = '';
            this.selectedMedium = '';
            this.applyFilters();
        },

        // Init
        init() { this.applyFilters(); }
    }
}
</script>
@endpush

@endsection