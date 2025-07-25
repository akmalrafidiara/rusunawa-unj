<?php

namespace App\Livewire\Managers;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementCategory; // Import AnnouncementCategory Enum
use App\Models\Announcement as AnnouncementModel;
use App\Models\Attachment;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use Illuminate\Validation\Rule;

class Announcement extends Component
{
    // Traits
    use WithPagination;
    use WithFileUploads;
    use WithFilePond;

    // Main data properties
    public
        $title,
        $description,
        $status,
        $category,
        $createdAt,
        $updatedAt;

    // Single image upload property for 'image' column
    public $image;
    public $existingImage;

    // File upload properties for attachments (morphMany)
    public $attachments = [];
    public $existingAttachments = [];
    public $attachmentsToDelete = [];

    // Options properties
    public $statusOptions;
    public $categoryOptions;

    // Filter properties
    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';

    // Pagination and sorting properties
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    // Modal properties
    public $showModal = false;
    public $modalType = '';
    public $announcementIdBeingEdited = null;

    // Query string properties
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    // listener untuk event dari Trix editor
    protected $listeners = ['contentChanged' => 'updateDescription'];

    /**
     * Rules for validation.
     *
     * @return array
     */
    public function rules()
{
    // Aturan validasi umum
    $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => ['required', Rule::in(AnnouncementStatus::values())],
        'category' => ['required', Rule::in(AnnouncementCategory::values())],
        'attachments.*' => 'nullable|file|max:5120',
    ];

    // Logika dinamis untuk validasi 'image'
    // Jika sedang membuat pengumuman baru (announcementIdBeingEdited null)
    if (is_null($this->announcementIdBeingEdited)) {
        $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
    } else {
        // Jika sedang mengedit pengumuman yang sudah ada
        // Dan pengumuman yang diedit belum punya gambar (existingImage null)
        if (is_null($this->existingImage)) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        } else {
            // Jika pengumuman yang diedit sudah punya gambar (existingImage tidak null)
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }
    }

    return $rules;
}

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'title.required' => 'Judul pengumuman wajib diisi.',
        'title.string' => 'Judul pengumuman harus berupa teks.',
        'title.max' => 'Judul pengumuman tidak boleh lebih dari :max karakter.',

        'description.string' => 'Deskripsi pengumuman harus berupa teks.',

        'status.required' => 'Status pengumuman wajib dipilih.',
        'status.in' => 'Status pengumuman tidak valid.',

        'category.required' => 'Kategori pengumuman wajib dipilih.',
        'category.in' => 'Kategori pengumuman tidak valid.',

        'image.required' => 'Gambar Banner pengumuman wajib diunggah.',
        'image.image' => 'File harus berupa gambar.',
        'image.mimes' => 'Format gambar yang diizinkan adalah JPEG, PNG, JPG, GIF, atau WEBP.',
        'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',

        'attachments.*.file' => 'File harus berupa dokumen.',
        'attachments.*.max' => 'Ukuran file tidak boleh lebih dari 5MB.',
    ];

    /**
     * Initialize the component.
     */
    public function mount()
    {
        $this->statusOptions = AnnouncementStatus::options();
        $this->categoryOptions = AnnouncementCategory::options();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $announcements = AnnouncementModel::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where("status", $this->statusFilter))
            ->when($this->categoryFilter, fn($q) => $q->where("category", $this->categoryFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage);

        return view('livewire.managers.contents.announcements.index', compact('announcements'));
    }

    /**
     * Open modal for creating a new announcement.
     */
    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Open modal for editing or viewing details of an announcement.
     *
     * @param AnnouncementModel $announcement
     */
    public function edit(AnnouncementModel $announcement)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->fillData($announcement);
        $this->modalType = 'form';
        $this->showModal = true;

        $this->dispatch('trix-load-content', $this->description);
    }

    public function updateDescription($content)
    {
        $this->description = $content;
    }

    /**
     * Open modal for viewing details of an announcement.
     *
     * @param AnnouncementModel $announcement
     */
    public function detail(AnnouncementModel $announcement)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->fillData(announcement: $announcement);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    /**
     * Fill data for edit or detail modal.
     *
     * @param AnnouncementModel $announcement
     */
    protected function fillData(AnnouncementModel $announcement)
    {
        // Filling edit and modal
        $this->announcementIdBeingEdited = $announcement->id;
        $this->title = $announcement->title;
        $this->description = $announcement->description;
        $this->status = $announcement->status->value;
        $this->category = $announcement->category->value;

        // Filling single image data
        $this->existingImage = $announcement->image; 

        // Filling attachments data
        $this->existingAttachments = $announcement->attachments()->get();

        // Reset image/file arrays
        $this->image = null;
        $this->attachments = [];
        $this->attachmentsToDelete = [];

        // Filling detail
        $this->createdAt = $announcement->created_at;
        $this->updatedAt = $announcement->updated_at;
    }

    /**
     * Lifecycle hook called when the component is updated (for image).
     * Validasi untuk properti `image` (gambar tunggal).
     */
    public function updatedImage()
    {
        $this->resetErrorBag('image');
        $this->validateOnly('image');
    }

    /**
     * Lifecycle hook called when the component is updated (for attachments).
     * Validasi untuk properti `attachments` (file berganda).
     */
    public function updatedAttachments()
    {
        $this->resetErrorBag('attachments.*');
        $this->validateOnly('attachments.*');
    }

    /**
     * Validate the form when a property is updated.
     * This method is called automatically (live) by Livewire when a property is updated.
     *
     * @param string $propertyName
     */
    public function updated($propertyName)
    {
        // Validate only the changed property if it exists in the rules
        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    /**
     * Handles deleting attachments marked for removal.
     * @param AnnouncementModel $announcement
     */
    private function handleAttachmentDeletions(AnnouncementModel $announcement)
    {
        if (!empty($this->attachmentsToDelete)) {
            $attachments = Attachment::whereIn('id', $this->attachmentsToDelete)->get();
            foreach ($attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }
        }
    }

    /**
     * Handles newly uploaded attachments.
     * @param AnnouncementModel $announcement
     */
    private function handleAttachmentUploads(AnnouncementModel $announcement)
    {
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                // Tentukan folder penyimpanan berdasarkan tipe file
                $path = '';
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $path = $file->store('attachments/images', 'public'); // Simpan gambar di folder images
                } else {
                    $path = $file->store('attachments/files', 'public'); // Simpan file lain di folder files
                }

                $announcement->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'file_name' => basename($path),
                    'mime_type' => $file->getMimeType(),
                    'path' => $path,
                ]);
            }
        }
    }

    /**
     * Remove an attachment from the attachments array for deletion.
     *
     * @param int $attachmentId
     */
    public function queueAttachmentForDeletion($attachmentId)
    {
        if (!in_array($attachmentId, $this->attachmentsToDelete)) {
            $this->attachmentsToDelete[] = $attachmentId;
        }

        $this->existingAttachments = collect($this->existingAttachments)->reject(function ($attachment) use ($attachmentId) {
            return $attachment['id'] == $attachmentId;
        })->values();
    }

    /**
     * Save the announcement data.
     * This method is called when the form is submitted.
     */
    public function save()
    {
        // Validate all properties
        $this->validate($this->rules());

        // Prepare data for saving
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'category' => $this->category, // Simpan category
        ];

        // Handle single image upload for 'image' column
        if ($this->image) {
            // Hapus gambar lama jika ada saat pengumuman sedang diedit
            if ($this->announcementIdBeingEdited && $this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $data['image'] = $this->image->store('images/announcements', 'public');
        } elseif ($this->announcementIdBeingEdited && $this->existingImage && !$this->image) {
            $data['image'] = $this->existingImage;
        } else {
            $data['image'] = null;
        }

        // Jika mengedit, pastikan ID pengumuman diatur
        $announcement = AnnouncementModel::updateOrCreate(
            ['id' => $this->announcementIdBeingEdited],
            $data
        );

        // Handle attachment deletions
        $this->handleAttachmentDeletions($announcement);
        // Handle new attachment uploads
        $this->handleAttachmentUploads($announcement);

        // Flash message
        LivewireAlert::title($this->announcementIdBeingEdited ? 'Data berhasil diperbarui.' : 'Pengumuman berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Reset form and close modal
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Confirm deletion of an announcement.
     *
     * @param array $data
     */
    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus pengumuman "' . $data['title'] . '"?')
            ->text('Apakah Anda yakin ingin menghapus pengumuman ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!') // Confirm button to delete method
            ->onConfirm('deleteAnnouncement', ['id' => $data['id']])
            ->show();
    }

    /**
     * Delete an announcement.
     *
     * @param array $data
     */
    public function deleteAnnouncement($data)
    {
        // Validate the ID
        $id = $data['id'];
        $announcement = AnnouncementModel::find($id);

        if ($announcement) {
            // Delete single image first
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }

            // Delete all associated attachments
            $attachments = $announcement->attachments()->get();
            foreach ($attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            // Delete the announcement
            $title = $announcement->title;
            $announcement->delete();

            // Flash success message
            LivewireAlert::title('Berhasil Dihapus')
                ->text('Pengumuman "' . $title . '" telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Confirm archiving of an announcement.
     *
     * @param array $data
     */
    public function confirmArchive($data)
    {
        LivewireAlert::title('Arsipkan pengumuman "' . $data['title'] . '"?')
            ->text('Apakah Anda yakin ingin mengarsipkan pengumuman ini? Pengumuman tidak akan terlihat di tampilan publik.')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Arsipkan!')
            ->onConfirm('archiveAnnouncement', ['id' => $data['id']])
            ->show();
    }

    /**
     * Archive an announcement.
     *
     * @param array $data
     */
    public function archiveAnnouncement($data)
    {
        $id = $data['id'];
        $announcement = AnnouncementModel::find($id);

        if ($announcement) {
            $announcement->update(['status' => AnnouncementStatus::Archived]);

            LivewireAlert::title('Berhasil Diarsipkan')
                ->text('Pengumuman "' . $announcement->title . '" telah diarsipkan.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Confirm publishing of an announcement.
     *
     * @param array $data
     */
    public function confirmPublish($data)
    {
        LivewireAlert::title('Terbitkan pengumuman "' . $data['title'] . '"?')
            ->text('Apakah Anda yakin ingin menerbitkan pengumuman ini? Pengumuman akan terlihat di tampilan publik.')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Terbitkan!')
            ->onConfirm('publishAnnouncement', ['id' => $data['id']])
            ->show();
    }

    /**
     * Publish an announcement.
     *
     * @param array $data
     */
    public function publishAnnouncement($data)
    {
        $id = $data['id'];
        $announcement = AnnouncementModel::find($id);

        if ($announcement) {
            $announcement->update(['status' => AnnouncementStatus::Published]);

            LivewireAlert::title('Berhasil Diterbitkan')
                ->text('Pengumuman "' . $announcement->title . '" telah diterbitkan.')
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
        $this->title = '';
        $this->description = '';
        $this->status = '';
        $this->category = '';
        $this->image = null;
        $this->existingImage = null;
        $this->attachments = [];
        $this->existingAttachments = [];
        $this->attachmentsToDelete = [];
        $this->announcementIdBeingEdited = null;

        $this->resetErrorBag();
        $this->resetValidation();

        $this->dispatch('trix-reset');
    }
}