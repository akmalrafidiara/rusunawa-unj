<?php

namespace App\Livewire\Managers;

use App\Enums\RoleUser;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
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

    public $perPage = 10;

    public $orderBy = 'created_at';
    public $sort = 'asc';

    public $showModal = false;
    public $userIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->roleOptions = RoleUser::options();
    }

    public function render()
    {
        $roles = Role::all();

        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter)))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage);

        // Use map to efficiently add wa_link to each user
        $users->getCollection()->transform(function ($user) {
            if ($user->phone) {
                $phone = preg_replace('/^(\+62|0)/', '', $user->phone);
                $user->wa_link = 'https://wa.me/62' . $phone;
            } else {
                $user->wa_link = null;
            }
            return $user;
        });

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

        LivewireAlert::title($this->userIdBeingEdited ? 'Data berhasil diperbarui.' : 'Pengguna berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();
    }

    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus data '. $data['name'] . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteUser', ['id' => $data['id']])
            ->show();
    }

    public function deleteUser($data)
    {
        $id = $data['id'];
        $user = User::find($id);

        if ($user) {
            $user->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text($user->name . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
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
