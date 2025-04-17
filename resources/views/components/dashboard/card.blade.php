@props([
    'title' => '',
    'icon' => '',
    'color' => 'blue',
    'value' => '',
])

<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
    <div class="w-12 h-12 flex items-center justify-center bg-{{ $color }}-500 text-white rounded-full text-2xl">
        <i class='bx {{ $icon }}'></i>
    </div>
    <div class="ml-4">
        <p class="text-gray-600 dark:text-gray-400">{{ $title }}</p>
        <h2 class="text-xl font-semibold">{{ $value }}</h2>
    </div>
</div>
