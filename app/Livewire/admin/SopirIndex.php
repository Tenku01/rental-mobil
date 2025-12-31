<?php

namespace App\Livewire\admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout; // Import Layout Attribute
use App\Models\Sopir;

class SopirIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = ''; // tersedia, tidak tersedia, bekerja

    // Modal Edit Status
    public $sopirId;
    public $statusSopir;
    public $showEditModal = false;

    protected $paginationTheme = 'tailwind';

    #[Layout('layouts.admin')] // Menggunakan Attribute
    public function render()
    {
        $sopirs = Sopir::with('user')
            ->when($this->search, function($q) {
                $q->where('nama', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('nama', 'asc')
            ->paginate(10);

        return view('livewire.admin.sopir-index', [
            'sopirs' => $sopirs
        ]);
    }

    public function editStatus($id)
    {
        $sopir = Sopir::findOrFail($id);
        $this->sopirId = $id;
        $this->statusSopir = $sopir->status;
        $this->showEditModal = true;
    }

    public function updateStatus()
    {
        $this->validate([
            'statusSopir' => 'required|in:tersedia,tidak tersedia,bekerja'
        ]);

        $sopir = Sopir::findOrFail($this->sopirId);
        $sopir->update([
            'status' => $this->statusSopir
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Status sopir berhasil diperbarui.');
    }

    public function closeModal()
    {
        $this->showEditModal = false;
    }
}