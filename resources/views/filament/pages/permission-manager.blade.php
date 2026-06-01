<x-filament-panels::page>

    {{-- Read-only banner for zonal_director --}}
    @if($this->isReadOnly)
        <div style="margin-bottom: 1.5rem; padding: 0.75rem 1rem; background: #fefce8; border: 1px solid #fde047; border-radius: 0.5rem; color: #854d0e; font-size: 0.875rem;">
            <strong>View Only.</strong>
            You can see current permission assignments but cannot make changes.
            Contact Super Admin to update permissions.
        </div>
    @endif

    @php
        $modules    = $this->modules;
        $roles      = $this->getConfigurableRoles();
        $isReadOnly = $this->isReadOnly;
        $grouped    = collect($modules)->groupBy('group');
    @endphp

    @foreach($grouped as $groupName => $groupModules)

        {{-- Group heading --}}
        <div style="margin-top: 2rem; margin-bottom: 0.5rem;">
            <span style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #9ca3af;">
                {{ $groupName }}
            </span>
        </div>

        @foreach($groupModules as $moduleKey => $module)
            @php
                $moduleKey = array_search($module, $modules);
                $isPhase2  = $module['phase'] === 2;
            @endphp

            <x-filament::section>

                {{-- Module header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-weight: 600; font-size: 0.95rem;">
                            {{ $module['label'] }}
                        </span>
                        @if($isPhase2)
                            <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #dbeafe; color: #1d4ed8; font-size: 0.7rem; font-weight: 600;">
                                Coming Soon
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #dcfce7; color: #15803d; font-size: 0.7rem; font-weight: 600;">
                                Live
                            </span>
                        @endif
                    </div>

                    {{-- Save button — hidden for read-only users --}}
                    @if(!$isReadOnly)
                        <x-filament::button
                            wire:click="saveModule('{{ $moduleKey }}')"
                            wire:loading.attr="disabled"
                            size="sm"
                        >
                            Save {{ $module['label'] }}
                        </x-filament::button>
                    @endif
                </div>

                {{-- Permission grid table --}}
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">

                        {{-- Role column headers --}}
                        <thead>
                            <tr style="border-bottom: 2px solid #e5e7eb;">
                                <th style="text-align: left; padding: 0.5rem 0.75rem; font-size: 0.75rem; color: #6b7280; font-weight: 600; min-width: 220px;">
                                    Permission
                                </th>
                                @foreach($roles as $roleSlug => $roleLabel)
                                    <th style="text-align: center; padding: 0.5rem 0.5rem; font-size: 0.72rem; font-weight: 600; min-width: 90px;
                                        color: {{ $roleSlug === 'zonal_director' ? '#d97706' : '#6b7280' }};">
                                        {{ $roleLabel }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($module['permissions'] as $permission)
                                <tr style="border-bottom: 1px solid #f3f4f6;">

                                    {{-- Permission name --}}
                                    <td style="padding: 0.6rem 0.75rem;">
                                        <code style="font-size: 0.75rem; color: #374151; background: #f9fafb; padding: 0.15rem 0.4rem; border-radius: 0.25rem; border: 1px solid #e5e7eb;">
                                            {{ $permission }}
                                        </code>
                                    </td>

                                    {{-- Toggle per role --}}
                                    @foreach($roles as $roleSlug => $roleLabel)
                                        @php
                                            $checked  = $permissionState[$permission][$roleSlug] ?? false;
                                            $isLocked = $isReadOnly;
                                        @endphp
                                        <td style="text-align: center; padding: 0.6rem 0.5rem;">
                                            <button
                                                @if(!$isLocked)
                                                    wire:click="togglePermission('{{ $permission }}', '{{ $roleSlug }}')"
                                                @endif
                                                type="button"
                                                @if($isLocked) disabled @endif
                                                style="
                                                    position: relative;
                                                    display: inline-flex;
                                                    height: 1.25rem;
                                                    width: 2.25rem;
                                                    flex-shrink: 0;
                                                    border-radius: 9999px;
                                                    border: 2px solid transparent;
                                                    transition: background-color 0.2s;
                                                    background-color: {{ $checked ? ($roleSlug === 'zonal_director' ? '#d97706' : '#f59e0b') : '#d1d5db' }};
                                                    cursor: {{ $isLocked ? 'not-allowed' : 'pointer' }};
                                                    opacity: {{ $isLocked ? '0.6' : '1' }};
                                                    outline: none;
                                                "
                                                title="{{ $isLocked ? 'View only' : ($checked ? 'Click to revoke' : 'Click to grant') }}"
                                            >
                                                <span style="
                                                    display: inline-block;
                                                    height: 0.95rem;
                                                    width: 0.95rem;
                                                    border-radius: 9999px;
                                                    background: white;
                                                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                                                    transition: transform 0.2s;
                                                    transform: {{ $checked ? 'translateX(1rem)' : 'translateX(0)' }};
                                                    margin-top: 1px;
                                                "></span>
                                            </button>
                                        </td>
                                    @endforeach

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            </x-filament::section>

        @endforeach

    @endforeach

    {{-- Footer note --}}
    <x-filament::section>
        <p style="font-size: 0.75rem; color: #9ca3af;">
            <strong>Super Admin</strong> always has full access and is not shown in this grid.
            Changes take effect immediately after saving each module.
            <strong>Zonal Director</strong> permissions are configurable here by Super Admin.
        </p>
    </x-filament::section>

</x-filament-panels::page>