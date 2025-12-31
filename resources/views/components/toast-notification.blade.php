<div x-data="{
        get bgColor() {
            switch ($store.toast.type) {
                case 'success': return 'bg-cyan-500';
                case 'error': return 'bg-red-500';
                case 'warning': return 'bg-yellow-500';
                default: return 'bg-gray-700';
            }
        },
        get icon() {
            switch ($store.toast.type) {
                case 'success': return 'fas fa-check-circle';
                case 'error': return 'fas fa-exclamation-circle';
                case 'warning': return 'fas fa-exclamation-triangle';
                default: return 'fas fa-info-circle';
            }
        }
    }"
    x-show="$store.toast.visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full"
    @click.away="$store.toast.visible = false"
    class="fixed bottom-6 right-6 z-50 p-4 rounded-xl shadow-2xl text-white max-w-sm cursor-pointer"
    :class="bgColor"
    style="display: none;"
>
    <div class="flex items-start space-x-3">
        <i :class="icon" class="text-2xl mt-0.5"></i>
        <p x-text="$store.toast.message" class="text-sm font-medium"></p>
        <button @click="$store.toast.visible = false" class="ml-auto -mt-1 opacity-70 hover:opacity-100 focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>