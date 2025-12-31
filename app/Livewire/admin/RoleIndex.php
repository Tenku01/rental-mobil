<?php

namespace App\Livewire\admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Role;

class RoleIndex extends Component
{
    use WithPagination;

    public $role_name;
    public $roleId;
    public $showModal = false; // Hanya untuk Edit

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'role_name' => 'required|string|max:50|unique:roles,role_name',
    ];

    #[Layout('layouts.admin')]
    public function render()
    {
        // Menampilkan semua role
        $roles = Role::orderBy('id', 'asc')->get();

        return view('livewire.admin.role-index', [
            'roles' => $roles
        ]);
    }

    // --- HANYA ADA FITUR EDIT ---

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->role_name = $role->role_name;
        
        $this->showModal = true;
    }

    public function update()
    {
        // Validasi unik kecuali diri sendiri
        $this->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $this->roleId
        ]);

        $role = Role::findOrFail($this->roleId);
        
        // Proteksi: Role 'admin' (ID 1) tidak boleh diganti namanya sembarangan untuk keamanan sistem
        if ($role->id === 1) {
            $this->showModal = false;
            session()->flash('error', 'Role Admin Utama tidak boleh diubah namanya demi keamanan sistem.');
            return;
        }

        $role->update([
            'role_name' => $this->role_name
        ]);

        $this->showModal = false;
        session()->flash('message', 'Nama role berhasil diubah.');
    }

    // Tidak ada fungsi create() atau delete() sesuai permintaan.
}