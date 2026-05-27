<div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
    <label class="block text-xs font-medium text-gray-600 mb-2">{{ $label }}</label>
    <input type="number" name="{{ $name }}" min="0"
           value="{{ old($name, $value) }}"
           class="w-full text-center rounded-lg text-sm px-3 py-2 font-semibold"
           style="border: 1px solid #e5e7eb; color: var(--color-primary);">
</div>
