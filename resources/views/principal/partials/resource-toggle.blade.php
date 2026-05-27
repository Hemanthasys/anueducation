<div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between" style="border: 1px solid #f3f4f6;">
    <label class="text-xs font-medium text-gray-600">{{ $label }}</label>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1"
               class="sr-only peer"
               {{ old($name, $value) ? 'checked' : '' }}>
        <div class="w-9 h-5 rounded-full peer transition-all
                    bg-gray-200 peer-checked:bg-green-500
                    after:content-[''] after:absolute after:top-0.5 after:left-0.5
                    after:bg-white after:rounded-full after:h-4 after:w-4
                    after:transition-all peer-checked:after:translate-x-4">
        </div>
    </label>
</div>
