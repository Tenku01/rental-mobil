<a {{ $attributes->merge([
    'class' => '
       block w-full px-4 py-2 text-start text-sm leading-5 
        text-gray-700 dark:text-gray-600 
        hover:bg-cyan-500 hover:text-white 
        dark:hover:bg-cyan-600 dark:hover:text-white
        focus:outline-none focus:bg-cyan-500 focus:text-white
        transition duration-150 ease-in-out
    '
]) }}>
    {{ $slot }}
</a>
