<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center px-4 py-2 
                bg-white dark:bg-cyan-800 
                border border-cyan-500 dark:border-cyan-600 
                rounded-md font-semibold text-xs 
                text-cyan-700 dark:text-cyan-200 
                uppercase tracking-widest shadow-sm 
                hover:bg-cyan-500 hover:text-white 
                dark:hover:bg-cyan-600 
                focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-200
                disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
