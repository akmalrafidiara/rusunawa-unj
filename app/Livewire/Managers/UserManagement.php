<?php

namespace App\Livewire\Managers;

use App\Enums\RoleUser;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $name, $email, $password, $phone;
    public $role;

    public $roleOptions;

    public $perPage = 5;
    public $showModal = false;
    public $userIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->roleOptions = RoleUser::options();
    }

    public function render()
    {
        $roles = Role::all();

        $users = User::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%$this->search%"))
        ->when($this->roleFilter, function ($q) {
            $q->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter));
        })
        ->paginate($this->perPage);

        foreach ($users as $user) {
            if ($user->phone) {
                $phone = preg_replace('/^(\+62|0)/', '', $user->phone);
                $user->wa_link = 'https://wa.me/62' . $phone;
            } else {
                $user->wa_link = null;
            }
        }

        return view('livewire.managers.user-management', compact('users', 'roles'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(User $user)
    {
        $this->search = '';
        $this->userIdBeingEdited = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->getRoleNames()->first() ?? '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,{$this->userIdBeingEdited}",
            'password' => $this->userIdBeingEdited ? 'nullable|min:6' : 'required|min:6',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        $user = User::updateOrCreate(
            ['id' => $this->userIdBeingEdited],
            $data
        );

        if (!$user->hasRole($this->role)) {
            $user->syncRoles([$this->role]);
        }

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', $this->userIdBeingEdited ? 'Data berhasil diperbarui.' : 'Pengguna berhasil ditambahkan.');
        $this->dispatch('swal:success', title: 'Berhasil!');
    }

    public function confirmDelete($id)
    {
        // Kirim ID ke frontend via dispatch
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            session()->flash('success', 'Pengguna berhasil dihapus');
            $this->dispatch('swal:success', title: 'Berhasil Dihapus');
        }
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->role = '';
        $this->userIdBeingEdited = null;
    }
}
