{{-- resources/views/filament/pages/teacher-bulk-upload.blade.php --}}
{{-- Inline styles only — Tailwind not compiled in custom Filament blade pages --}}

<x-filament-panels::page>

    @if(! $this->uploaded)

        {{-- Instructions card --}}
        <div style="background:#fff;border-radius:0.75rem;border:1px solid #e5e7eb;padding:1.5rem;margin-bottom:1.5rem;">
            <h3 style="font-size:1rem;font-weight:600;color:#111827;margin:0 0 1rem;">
                {{ __('How to Bulk Upload Teachers') }}
            </h3>

            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                    <span style="flex-shrink:0;width:1.5rem;height:1.5rem;border-radius:9999px;background:#1a3a6b;color:#fff;font-size:0.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;">1</span>
                    <p style="font-size:0.875rem;color:#374151;margin:0;">
                        <strong>{{ __('Download the template') }}</strong> — {{ __('Click "Download Template" above to get the official Excel template with all required columns and dropdown validations.') }}
                    </p>
                </div>
                <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                    <span style="flex-shrink:0;width:1.5rem;height:1.5rem;border-radius:9999px;background:#1a3a6b;color:#fff;font-size:0.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;">2</span>
                    <p style="font-size:0.875rem;color:#374151;margin:0;">
                        <strong>{{ __('Fill in teacher data') }}</strong> — {{ __('Enter teacher information row by row. Use the dropdown selections for Staff Type, School, Subject, etc. Date format: DD/MM/YYYY.') }}
                    </p>
                </div>
                <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                    <span style="flex-shrink:0;width:1.5rem;height:1.5rem;border-radius:9999px;background:#1a3a6b;color:#fff;font-size:0.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;">3</span>
                    <p style="font-size:0.875rem;color:#374151;margin:0;">
                        <strong>{{ __('Upload the file') }}</strong> — {{ __('Click "Upload File" below. The system will validate each row. Rows with errors will be skipped and reported.') }}
                    </p>
                </div>
                <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                    <span style="flex-shrink:0;width:1.5rem;height:1.5rem;border-radius:9999px;background:#1a3a6b;color:#fff;font-size:0.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;">4</span>
                    <p style="font-size:0.875rem;color:#374151;margin:0;">
                        <strong>{{ __('Review results') }}</strong> — {{ __('After upload, review the summary and error report. Fix any errors in the Excel file and re-upload only the failed rows.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Required fields info --}}
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:0.75rem;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="font-size:0.875rem;font-weight:600;color:#1d4ed8;margin:0 0 0.5rem;">{{ __('Required Fields') }}</p>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                @foreach(['Full Name', 'NIC Number', 'Gender (M/F)', 'Birthday', 'School Census No', 'Staff Type', 'Appointed Date', 'Joined School Date'] as $field)
                    <span style="background:#dbeafe;color:#1e40af;font-size:0.75rem;font-weight:500;padding:0.2rem 0.6rem;border-radius:9999px;">{{ $field }}</span>
                @endforeach
            </div>
        </div>

        {{-- Upload button --}}
        <div style="display:flex;justify-content:center;padding:2rem 0;">
            {{ $this->uploadAction }}
        </div>

    @else

        {{-- Results summary --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">

            {{-- Created --}}
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:0.75rem;padding:1.25rem;text-align:center;">
                <p style="font-size:2rem;font-weight:700;color:#16a34a;margin:0;">{{ $results['created'] }}</p>
                <p style="font-size:0.875rem;color:#15803d;margin:0.25rem 0 0;font-weight:500;">{{ __('Teachers Created') }}</p>
            </div>

            {{-- Skipped --}}
            <div style="background:#fef9c3;border:1px solid #fde047;border-radius:0.75rem;padding:1.25rem;text-align:center;">
                <p style="font-size:2rem;font-weight:700;color:#ca8a04;margin:0;">{{ $results['skipped'] }}</p>
                <p style="font-size:0.875rem;color:#a16207;margin:0.25rem 0 0;font-weight:500;">{{ __('Rows Skipped') }}</p>
            </div>

            {{-- Total --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.75rem;padding:1.25rem;text-align:center;">
                <p style="font-size:2rem;font-weight:700;color:#1e293b;margin:0;">{{ $results['created'] + $results['skipped'] }}</p>
                <p style="font-size:0.875rem;color:#475569;margin:0.25rem 0 0;font-weight:500;">{{ __('Total Rows Processed') }}</p>
            </div>

        </div>

        {{-- Error report --}}
        @if(count($results['errors']) > 0)
            <div style="background:#fff;border-radius:0.75rem;border:1px solid #fca5a5;margin-bottom:1.5rem;overflow:hidden;">
                <div style="background:#fef2f2;padding:1rem 1.25rem;border-bottom:1px solid #fca5a5;display:flex;align-items:center;gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#dc2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <p style="font-size:0.875rem;font-weight:600;color:#dc2626;margin:0;">
                        {{ __('Skipped Rows — Error Report') }} ({{ count($results['errors']) }} {{ __('rows') }})
                    </p>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
                        <thead>
                            <tr style="background:#fef2f2;">
                                <th style="padding:0.625rem 1rem;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #fca5a5;white-space:nowrap;">{{ __('Row') }}</th>
                                <th style="padding:0.625rem 1rem;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #fca5a5;white-space:nowrap;">{{ __('Name') }}</th>
                                <th style="padding:0.625rem 1rem;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #fca5a5;white-space:nowrap;">{{ __('NIC') }}</th>
                                <th style="padding:0.625rem 1rem;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #fca5a5;">{{ __('Reason Skipped') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['errors'] as $error)
                                <tr style="border-bottom:1px solid #fee2e2;">
                                    <td style="padding:0.625rem 1rem;color:#374151;font-weight:600;">{{ $error['row'] }}</td>
                                    <td style="padding:0.625rem 1rem;color:#374151;">{{ $error['name'] }}</td>
                                    <td style="padding:0.625rem 1rem;color:#374151;font-family:monospace;">{{ $error['nic'] }}</td>
                                    <td style="padding:0.625rem 1rem;color:#dc2626;">{{ $error['reason'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:0.75rem;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p style="font-size:0.875rem;color:#15803d;font-weight:500;margin:0;">{{ __('All rows imported successfully with no errors.') }}</p>
            </div>
        @endif

        {{-- Upload another --}}
        <div style="display:flex;justify-content:center;">
            {{ $this->resetAction }}
        </div>

    @endif

    <x-filament-actions::modals />

</x-filament-panels::page>