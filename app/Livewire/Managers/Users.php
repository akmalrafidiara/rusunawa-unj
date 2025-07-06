<?php

namespace App\Livewire\Managers;

use App\Enums\RoleUser;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class Users extends Component
{
    // Traits
    use WithPagination;

    // Main data properties
    public $name, $email, $password, $phone, $role;

    // Options properties
    public $roleOptions;

    // Toolbar properties
    public $search = '';
    public $roleFilter = '';
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $userIdBeingEdited = null;

    // Query string properties
    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    /**
     * Initialize the component.
     */
    public function mount()
    {
        // Get all role options
        $allRoleOptions = RoleUser::options();

        // If the authenticated user is 'head_of_rusunawa', filter out the 'admin' role
        if (Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value)) {
            $this->roleOptions = array_filter($allRoleOptions, function ($option) {
                return $option['value'] !== RoleUser::ADMIN->value;
            });
            // Re-index the array if needed
            $this->roleOptions = array_values($this->roleOptions);
        } else {
            // For other roles (e.g., admin), show all options
            $this->roleOptions = $allRoleOptions;
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter)));

        // If the authenticated user is 'head_of_rusunawa', hide users with 'admin' role
        if (Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value)) {
            $users->whereDoesntHave('roles', fn($r) => $r->where('name', RoleUser::ADMIN->value));
        }

        $users = $users->orderBy($this->orderBy, $this->sort)
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

        return view('livewire.managers.oprations.users.index', compact('users'));
    }

    /**
     * Create a new user.
     */
    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Edit an existing user.
     *
     * @param User $user
     */
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

    /**
     * Validation rules for the form.
     *
     * @return array
     */
    public function rules()
    {
        // Get the rules as they are
        $rules = [
            'name' => 'required',
            'email' => "required|email|unique:users,email,{$this->userIdBeingEdited}",
            'password' => $this->userIdBeingEdited ? 'nullable|min:6' : 'required|min:6',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|exists:roles,name',
        ];

        // Add a custom rule to prevent 'head_of_rusunawa' from assigning 'admin' role
        if (Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value)) {
            $rules['role'] .= '|not_in:' . RoleUser::ADMIN->value;
        }

        return $rules;
    }

    /**
     * Validate the form data when a property is updated.
     *
     * @param string $propertyName
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    /**
     * Save the user data.
     */
    public function save()
    {
        $this->validate($this->rules());

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

        LivewireAlert::title($this->userIdBeingEdited ? 'Data berhasil diperbarui.' : 'Pengguna berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Confirm deletion of a user.
     *
     * @param array $data
     */
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

    /**
     * Delete a user.
     *
     * @param array $data
     */
    public function deleteUser($data)
    {
        $id = $data['id'];
        $user = User::find($id);

        // Prevent 'head_of_rusunawa' from deleting 'admin' users
        if (Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value) && $user->hasRole(RoleUser::ADMIN->value)) {
            LivewireAlert::error()
                ->title('Akses Ditolak!')
                ->text('Anda tidak memiliki izin untuk menghapus pengguna dengan peran Admin.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

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

    /**
     * Reset the form fields.
     */
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