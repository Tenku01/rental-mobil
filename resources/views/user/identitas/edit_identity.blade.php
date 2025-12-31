<!-- resources/views/user/edit_identity.blade.php -->

<x-navbar />

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-24 border border-cyan-600">
    <h1 class="text-2xl font-semibold text-left text-gray-700 mb-6">Edit Identitas Saya</h1>

    <form action="{{ route('update.identity', $userIdentification->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <table class="min-w-full mt-6 table-auto border border-cyan-600 rounded-lg">
            <thead class="bg-cyan-600 text-white">
                <tr>
                    <th class="py-3 px-6 text-left text-sm font-semibold border-r border-cyan-600">Jenis Dokumen</th>
                    <th class="py-3 px-6 text-left text-sm font-semibold border-r border-cyan-600">Upload File</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-100 border-b border-cyan-600">
                    <td class="py-3 px-6 text-sm text-gray-700 border-r border-cyan-600">KTP</td>
                    <td class="py-3 px-6 border-r border-cyan-600">
                        <input type="file" name="ktp" id="ktp" class="block text-sm text-gray-900 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                    </td>
                </tr>
                <tr class="hover:bg-gray-100 border-b border-cyan-600">
                    <td class="py-3 px-6 text-sm text-gray-700 border-r border-cyan-600">SIM</td>
                    <td class="py-3 px-6 border-r border-cyan-600">
                        <input type="file" name="sim" id="sim" class="block text-sm text-gray-900 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-6 flex justify-center">
            <button type="submit" class="bg-cyan-600 text-white px-6 py-2 rounded-lg hover:bg-cyan-700 transition duration-300 ease-in-out transform hover:scale-105">Update</button>
        </div>
    </form>
</div>
