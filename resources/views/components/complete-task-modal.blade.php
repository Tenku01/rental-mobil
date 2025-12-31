<div id="completeTaskModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 hidden items-center justify-center p-4 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-white rounded-lg overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border-t-8 border-green-500">
        <form id="completeTaskForm" method="POST" action="">
            @csrf
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                    Selesaikan Penugasan #<span id="taskIdDisplay"></span>
                </h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-4">Harap berikan detail kondisi mobil (kebersihan, kerusakan minor/major, kekurangan bensin, dll.) setelah penugasan selesai.</p>
                    
                    <label for="kondisi_mobil" class="block text-sm font-medium text-gray-700">Catatan Kondisi Mobil</label>
                    <textarea name="kondisi_mobil" id="kondisi_mobil" rows="5" required maxlength="500"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm p-3 border"
                        placeholder="Contoh: Mobil kembali dalam kondisi baik. Bensin tersisa 1/4 tank. Ada goresan kecil di bagian pintu belakang kiri."></textarea>
                    <p class="text-xs text-gray-400 mt-1">Maksimal 500 karakter.</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" 
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Konfirmasi & Selesaikan Tugas
                </button>
                <button type="button" onclick="closeModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>