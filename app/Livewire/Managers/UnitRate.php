<?php

namespace App\Livewire\Managers;

use App\Enums\PricingBasis;
use App\Models\UnitRate as UnitRateModel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class UnitRate extends Component
{
    public $search = '';
    public $price, $occupantType;

    public $pricingBasis;

    public $pricingBasisOptions;
    public $occupantTypeOptions = [];

    public $pricingBasisFilter = '';

    public $orderBy = 'created_at';
    public $sort = 'asc';

    public $showModal = false;
    public $unitRateIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'pricingBasisFilter' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->pricingBasisOptions = PricingBasis::options();

        $this->refreshOccupantTypeOptions();
    }

    public function render()
    {
        $unitRates = UnitRateModel::query()
            ->when($this->search, fn($q) => $q->where('occupant_type', 'like', "%{$this->search}%"))
            ->when($this->pricingBasisFilter, fn($q) => $q->where('pricing_basis', $this->pricingBasisFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        return view('livewire.managers.unit-rate', compact('unitRates'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(UnitRateModel $unitRate)
    {
        $this->unitRateIdBeingEdited = $unitRate->id;
        $this->price = $unitRate->price;
        $this->occupantType = $unitRate->occupant_type;
        $this->pricingBasis = $unitRate->pricing_basis->value;
        $this->showModal = true;
    }

    public function rules()
    {
        return [
            'price' => 'required|numeric|min:0',
            'occupantType' => 'required|string|max:255',
            'pricingBasis' => [
                'required',
                Rule::in(PricingBasis::values()),
            ],
        ];
    }

    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName, $this->rules());
    // }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'price' => $this->price,
            'occupant_type' => $this->occupantType,
            'pricing_basis' => $this->pricingBasis,
        ];

        // Simpan atau update data
        UnitRateModel::updateOrCreate(
            ['id' => $this->unitRateIdBeingEdited],
            $data
        );

        $this->refreshOccupantTypeOptions();

        // Flash message
        LivewireAlert::title($this->unitRateIdBeingEdited ? 'Data berhasil diperbarui.' : 'Rate unit berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Reset form
        $this->resetForm();
        $this->showModal = false;
    }

    public function confirmDelete($data)
    {
        $pricingBasisInstance = PricingBasis::from($data['pricing_basis']);
        LivewireAlert::title('Hapus data harga tipe penghuni ' . $data['occupant_type'] . ' ' . $pricingBasisInstance->label() . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteUnitRate', ['id' => $data['id']])
            ->show();
    }

    public function deleteUnitRate($data)
    {
        $id = $data['id'];
        $unitRate = UnitRateModel::find($id);
        if ($unitRate) {

            // Hapus unit rate
            $unitRate->delete();

            $this->refreshOccupantTypeOptions();

            LivewireAlert::title('Berhasil Dihapus')
                ->text($unitRate->occupant_type . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function resetForm() {
        $this->price = '';
        $this->occupantType = '';
        $this->pricingBasis = '';
        $this->unitRateIdBeingEdited = null;
    }

    private function refreshOccupantTypeOptions()
    {
        $this->occupantTypeOptions = UnitRateModel::query()
            ->select('occupant_type')
            ->distinct()
            ->pluck('occupant_type')
            ->toArray();
    }
}
