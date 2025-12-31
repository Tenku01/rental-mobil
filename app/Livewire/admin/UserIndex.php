<?php

namespace App\Livewire\admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = '';
    
    // Modal state
    public $showModal = false;
    public $modalTitle = '';
    public $editingUserId = null;
    
    // Form fields - HANYA 3 FIELD
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role_id = '';
    public $status = 'aktif';

    protected $paginationTheme = 'tailwind';

    // Validation rules sederhana
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:aktif,nonaktif',
        ];

        // Validasi password
        if (!$this->editingUserId) {
            // Create: password wajib
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        } else {
            // Edit: password optional
            $rules['password'] = 'nullable|confirmed|min:8';
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->editingUserId;
        }

        return $rules;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $users = User::with('role')
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterRole, function($q) {
                $q->where('role_id', $this->filterRole);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.admin.user-index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    // Buka modal create
    public function create()
    {
        $this->resetForm();
        $this->modalTitle = 'Tambah User Baru';
        $this->showModal = true;
    }

    // Buka modal edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->status = $user->status;
        
        $this->modalTitle = 'Edit User';
        $this->showModal = true;
    }

    // Simpan data
    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'status' => $this->status,
        ];

        // Tambah password jika diisi
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            // Update
            $user = User::findOrFail($this->editingUserId);
            
            // Cegah edit admin sendiri
            if ($user->id === Auth::id()) {
                session()->flash('error', 'Anda tidak bisa mengedit akun sendiri!');
                return;
            }
            
            $user->update($data);
            $message = 'User berhasil diperbarui!';
        } else {
            // Create
            User::create($data);
            $message = 'User baru berhasil ditambahkan!';
        }

        $this->closeModal();
        session()->flash('success', $message);
    }

    // Hapus user
    public function delete($id)
    {
        $user = User::findOrFail($id);

        // Cegah hapus diri sendiri
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri!');
            return;
        }

        $user->delete();
        session()->flash('success', 'User berhasil dihapus!');
    }

    // Toggle status
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menonaktifkan akun sendiri!');
            return;
        }

        $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status' => $newStatus]);

        $pesan = $newStatus === 'aktif' ? 'diaktifkan kembali.' : 'dinonaktifkan.';
        session()->flash('message', 'User ' . $user->name . ' berhasil ' . $pesan);
    }

    // Reset form
    private function resetForm()
    {
        $this->reset([
            'editingUserId',
            'name',
            'email',
            'password',
            'password_confirmation',
            'role_id',
            'status'
        ]);
        
        $this->resetErrorBag();
        $this->status = 'aktif';
    }

    // Tutup modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
}