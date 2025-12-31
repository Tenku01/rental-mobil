@props(['disabled' => false])

<input 
    @disabled($disabled) 
    {{ $attributes->merge(['class' => 'border border-gray-100 dark:border-gray-200 dark:bg-gray-0 dark:text-gray-700 focus:border-cyan-500 dark:focus:border-cyan-600 focus:ring-cyan-500 dark:focus:ring-cyan-600 rounded-md shadow-md']) }}
>
