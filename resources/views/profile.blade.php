<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-cyan-700 dark:text-cyan-400 leading-tight tracking-wide">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <!-- Informasi Akun -->
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 border border-cyan-200 dark:border-cyan-700 shadow-lg sm:rounded-2xl transition duration-300 hover:shadow-xl">
                <div class="max-w-xl">
                    <h3 class="text-xl font-semibold text-cyan-700 dark:text-cyan-300 mb-4 flex items-center gap-2">
                        <!-- Ganti dengan Heroicon User -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-500 dark:text-cyan-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a5 5 0 00-3.536 8.536A5 5 0 0010 18a5 5 0 003.536-7.464A5 5 0 0010 2zm0 2a3 3 0 110 6 3 3 0 010-6z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Informasi Akun') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Perbarui nama, email, dan informasi pribadi Anda di bawah ini.
                    </p>
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Ganti Password -->
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 border border-cyan-200 dark:border-cyan-700 shadow-lg sm:rounded-2xl transition duration-300 hover:shadow-xl">
                <div class="max-w-xl">
                    <h3 class="text-xl font-semibold text-cyan-700 dark:text-cyan-300 mb-4 flex items-center gap-2">
                        <!-- Heroicon Key -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-500 dark:text-cyan-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zm2.536-1.536a1 1 0 010 1.414l-5.657 5.657a1 1 0 01-1.414-1.414l5.657-5.657a1 1 0 011.414 0z" />
                        </svg>
                        {{ __('Ubah Kata Sandi') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Pastikan kata sandi Anda kuat dan aman. Gunakan kombinasi huruf, angka, dan simbol.
                    </p>
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <!-- Hapus Akun -->
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 shadow-lg sm:rounded-2xl transition duration-300 hover:shadow-xl">
                <div class="max-w-xl">
                    <h3 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
                        <!-- Heroicon Trash -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 9a1 1 0 011 1v4a1 1 0 11-2 0v-4a1 1 0 011-1zm4 0a1 1 0 011 1v4a1 1 0 11-2 0v-4a1 1 0 011-1zM4 6a1 1 0 011-1h10a1 1 0 011 1H4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Hapus Akun') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Menghapus akun akan menghapus semua data Anda secara permanen. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
