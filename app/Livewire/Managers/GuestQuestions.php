<?php

namespace App\Livewire\Managers;

use App\Models\GuestQuestion as GuestQuestionModel;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class GuestQuestions extends Component
{
    use WithPagination;

    public $search = '';
    public $readFilter = '';

    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'readFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'markAsReadConfirmed',
        'deleteQuestionConfirmed',
    ];

    public function updating($name, $value)
    {
        if (in_array($name, ['search', 'readFilter', 'perPage', 'orderBy', 'sort'])) {
            $this->resetPage();
        }
    }


    public function getWhatsappLink($phoneNumberRaw, $questionMessage, $questionDate)
    {
        if (!$phoneNumberRaw) {
            return null;
        }

        $cleanPhoneNumber = preg_replace('/[^0-9]/', '', $phoneNumberRaw);
        if (!$cleanPhoneNumber) {
            return null;
        }

        if (substr($cleanPhoneNumber, 0, 1) === '0') {
            $whatsappNumber = '62' . substr($cleanPhoneNumber, 1);
        } elseif (substr($cleanPhoneNumber, 0, 2) === '62') {
            $whatsappNumber = $cleanPhoneNumber;
        } else {
            $whatsappNumber = '62' . $cleanPhoneNumber;
        }

        // Format tanggal dan tambahkan bold
        $formattedDate = Carbon::parse($questionDate)->locale('id')->isoFormat('DD MMMM YYYY');

        $templateMessage = "Halo, kami dari *Pengelola Rusunawa UNJ* ingin merespons pertanyaan Anda.\n\n" .
            "*Pertanyaan Anda pada tanggal* *{$formattedDate}*:\n\"{$questionMessage}\"\n\n" .
            "*Jawaban kami*:\n[Silakan ketik jawaban di sini...]";

        $encodedMessage = urlencode($templateMessage);

        return "https://wa.me/{$whatsappNumber}?text={$encodedMessage}";
    }

    public function getEmailLink($email, $questionMessage, $questionDate)
    {
        if (!$email) {
            return null;
        }

        // Format tanggal
        $formattedDate = Carbon::parse($questionDate)->locale('id')->isoFormat('DD MMMM YYYY');

        $subject = urlencode("Balasan Pertanyaan Anda - Tanggal {$formattedDate}");

        // Buat isi email dengan bold.
        $body = urlencode("Halo, kami dari Pengelola Rusunawa UNJ ingin merespons pertanyaan Anda.\n\n" .
            "Pertanyaan Anda pada tanggal {$formattedDate}:\n\"{$questionMessage}\"\n\n" .
            "Jawaban kami:\n[Silakan ketik jawaban di sini...]");

        // Menggunakan asterisk * untuk bold, meskipun mungkin tidak selalu dirender bold di semua klien email.
        $body = str_replace(urlencode("Pengelola Rusunawa UNJ"), urlencode("*Pengelola Rusunawa UNJ*"), $body);
        $body = str_replace(urlencode("Pertanyaan Anda pada tanggal"), urlencode("*Pertanyaan Anda pada tanggal*"), $body);
        // Tambahkan bold pada tanggal di body email
        $body = str_replace(urlencode($formattedDate), urlencode("*{$formattedDate}*"), $body);
        $body = str_replace(urlencode("Jawaban kami"), urlencode("*Jawaban kami*"), $body);

        return "mailto:{$email}?subject={$subject}&body={$body}";
    }

    public function confirmMarkAsRead($questionId)
    {
        $question = GuestQuestionModel::find($questionId);
        if (!$question) return;

        LivewireAlert::title('Konfirmasi')
            ->text('Tandai pertanyaan dari "' . $question->fullName . '" sebagai sudah dibaca?')
            ->question()
            ->withCancelButton('Batal')
            ->withConfirmButton('Ya, tandai sudah dibaca')
            ->onConfirm('markAsReadConfirmed', ['id' => $questionId])
            ->show();
    }

    public function markAsReadConfirmed($data)
    {
        $id = $data['id'] ?? null;
        $question = GuestQuestionModel::find($id);
        if ($question && !$question->is_read) {
            $question->is_read = true;
            $question->save();

            LivewireAlert::title('Berhasil')
                ->text('Pertanyaan berhasil ditandai sudah dibaca.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function confirmDeleteQuestion($questionId)
    {
        $question = GuestQuestionModel::find($questionId);
        if (!$question) return;

        LivewireAlert::title('Konfirmasi')
            ->text('Hapus pertanyaan dari "' . $question->fullName . '" secara permanen?')
            ->question()
            ->withCancelButton('Batal')
            ->withConfirmButton('Ya, hapus')
            ->onConfirm('deleteQuestionConfirmed', ['id' => $questionId])
            ->show();
    }

    public function deleteQuestionConfirmed($data)
    {
        $id = $data['id'] ?? null;
        $question = GuestQuestionModel::find($id);
        if ($question) {
            $name = $question->fullName;
            $question->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('Pertanyaan dari "' . $name . '" telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function render()
    {
        $guestQuestions = GuestQuestionModel::query();

        if ($this->search) {
            $guestQuestions->where(function ($query) {
                $query->where('fullName', 'like', "%{$this->search}%")
                    ->orWhere('formPhoneNumber', 'like', "%{$this->search}%")
                    ->orWhere('formEmail', 'like', "%{$this->search}%")
                    ->orWhere('message', 'like', "%{$this->search}%");
            });
            if ($this->readFilter === 'read') {
                $guestQuestions->where('is_read', true);
            } elseif ($this->readFilter === 'unread') {
                $guestQuestions->where('is_read', false);
            }
        } else {
            if ($this->readFilter === 'read') {
                $guestQuestions->where('is_read', true);
            } elseif ($this->readFilter === 'unread') {
                $guestQuestions->where('is_read', false);
            }
        }

        $guestQuestions->orderBy($this->orderBy, $this->sort);

        $guestQuestions = $guestQuestions->paginate($this->perPage);

        return view('livewire.managers.contents.guest-questions.index', compact('guestQuestions'));
    }
}
