<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
    {{-- Form: add / edit an IP rule --}}
    <x-gp247::card :title="$editingId
        ? gp247_language_render('action.edit')
        : gp247_language_render('Plugins/CheckIP::lang.admin.add_new_title')">
        <x-slot:actions>
            <x-gp247::form-new-button :editing="$editingId" action="resetForm" data-testid="admin-check-ip-form-new" />
        </x-slot:actions>
        <form wire:submit="save" class="space-y-4">
            <x-gp247::input
                :label="gp247_language_render('Plugins/CheckIP::lang.description')"
                name="description"
                wire:model="description"
                :error="$errors->first('description')"
                required />

            <x-gp247::input
                :label="gp247_language_render('Plugins/CheckIP::lang.ip')"
                name="ip"
                wire:model="ip"
                :help="gp247_language_render('Plugins/CheckIP::lang.ip_help')"
                :error="$errors->first('ip')"
                required />

            {{-- type: allow / deny --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ gp247_language_render('Plugins/CheckIP::lang.action') }}
                </label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="radio" value="allow" wire:model="type"
                            class="text-blue-600 focus:ring-blue-500">
                        {{ gp247_language_render('Plugins/CheckIP::lang.allow') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="radio" value="deny" wire:model="type"
                            class="text-blue-600 focus:ring-blue-500">
                        {{ gp247_language_render('Plugins/CheckIP::lang.deny') }}
                    </label>
                </div>
                @error('type')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- status: ON / OFF --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ gp247_language_render('Plugins/CheckIP::lang.status') }}
                </label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="radio" value="1" wire:model="status"
                            class="text-blue-600 focus:ring-blue-500">
                        ON
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="radio" value="0" wire:model="status"
                            class="text-blue-600 focus:ring-blue-500">
                        OFF
                    </label>
                </div>
                @error('status')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end pt-2">
                <x-gp247::button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ gp247_language_render('action.submit') }}
                </x-gp247::button>
            </div>
        </form>
    </x-gp247::card>

    {{-- List: allow + deny --}}
    <x-gp247::card :title="gp247_language_render('Plugins/CheckIP::lang.admin.list')">
        <div class="space-y-6">
            {{-- Allow --}}
            <div>
                <h4 class="mb-2 text-sm font-semibold text-green-700 dark:text-green-300">
                    IP {{ gp247_language_render('Plugins/CheckIP::lang.allow') }}
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                <th class="py-2 pr-3 font-medium">#</th>
                                <th class="py-2 pr-3 font-medium">{{ gp247_language_render('Plugins/CheckIP::lang.ip') }}</th>
                                <th class="py-2 pr-3 font-medium">{{ gp247_language_render('Plugins/CheckIP::lang.description') }}</th>
                                <th class="py-2 pr-3 font-medium">{{ gp247_language_render('Plugins/CheckIP::lang.status') }}</th>
                                <th class="py-2 pr-3 text-right font-medium">{{ gp247_language_render('action.title') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allowList as $row)
                                <tr class="border-b border-gray-100 dark:border-gray-800 {{ $editingId === (int) $row->id ? 'bg-blue-50 dark:bg-gray-700/40' : '' }}">
                                    <td class="py-2 pr-3 text-gray-700 dark:text-gray-200">{{ $row->id }}</td>
                                    <td class="py-2 pr-3 font-medium text-gray-800 dark:text-gray-100">{{ $row->ip }}</td>
                                    <td class="py-2 pr-3 text-gray-600 dark:text-gray-300">{{ $row->description }}</td>
                                    <td class="py-2 pr-3">
                                        <x-gp247::badge :color="$row->status ? 'green' : 'gray'">{{ $row->status ? 'ON' : 'OFF' }}</x-gp247::badge>
                                    </td>
                                    <td class="py-2 pr-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <x-gp247::button size="sm" variant="ghost" wire:click="edit({{ $row->id }})"
                                                title="{{ gp247_language_render('action.edit') }}">
                                                <i class="fas fa-edit text-blue-600"></i>
                                            </x-gp247::button>
                                            <x-gp247::button size="sm" variant="ghost" wire:click="delete({{ $row->id }})"
                                                wire:confirm="{{ gp247_language_render('action.delete_confirm') }}"
                                                title="{{ gp247_language_render('action.delete') }}">
                                                <i class="fas fa-trash-alt text-red-600"></i>
                                            </x-gp247::button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-3 text-center text-gray-400 dark:text-gray-500">—</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Deny --}}
            <div>
                <h4 class="mb-2 text-sm font-semibold text-red-700 dark:text-red-300">
                    IP {{ gp247_language_render('Plugins/CheckIP::lang.deny') }}
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                <th class="py-2 pr-3 font-medium">#</th>
                                <th class="py-2 pr-3 font-medium">{{ gp247_language_render('Plugins/CheckIP::lang.ip') }}</th>
                                <th class="py-2 pr-3 font-medium">{{ gp247_language_render('Plugins/CheckIP::lang.description') }}</th>
                                <th class="py-2 pr-3 font-medium">{{ gp247_language_render('Plugins/CheckIP::lang.status') }}</th>
                                <th class="py-2 pr-3 text-right font-medium">{{ gp247_language_render('action.title') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($denyList as $row)
                                <tr class="border-b border-gray-100 dark:border-gray-800 {{ $editingId === (int) $row->id ? 'bg-blue-50 dark:bg-gray-700/40' : '' }}">
                                    <td class="py-2 pr-3 text-gray-700 dark:text-gray-200">{{ $row->id }}</td>
                                    <td class="py-2 pr-3 font-medium text-gray-800 dark:text-gray-100">{{ $row->ip }}</td>
                                    <td class="py-2 pr-3 text-gray-600 dark:text-gray-300">{{ $row->description }}</td>
                                    <td class="py-2 pr-3">
                                        <x-gp247::badge :color="$row->status ? 'green' : 'gray'">{{ $row->status ? 'ON' : 'OFF' }}</x-gp247::badge>
                                    </td>
                                    <td class="py-2 pr-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <x-gp247::button size="sm" variant="ghost" wire:click="edit({{ $row->id }})"
                                                title="{{ gp247_language_render('action.edit') }}">
                                                <i class="fas fa-edit text-blue-600"></i>
                                            </x-gp247::button>
                                            <x-gp247::button size="sm" variant="ghost" wire:click="delete({{ $row->id }})"
                                                wire:confirm="{{ gp247_language_render('action.delete_confirm') }}"
                                                title="{{ gp247_language_render('action.delete') }}">
                                                <i class="fas fa-trash-alt text-red-600"></i>
                                            </x-gp247::button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-3 text-center text-gray-400 dark:text-gray-500">—</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-gp247::card>
</div>
