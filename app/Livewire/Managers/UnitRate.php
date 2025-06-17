<?php

namespace App\Livewire\Managers;

use App\Enums\PricingBasis;
use App\Models\UnitRate as UnitRateModel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class UnitRate extends Component
{
    // Main data properties
    public $price, $occupantType, $pricingBasis, $requiresVerification;

    // Options properties
    public $pricingBasisOptions;
    public $occupantTypeOptions = [];

    // Toolbar properties
    public $search = '';
    public $pricingBasisFilter = '';
    public $orderBy = 'created_at';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $unitRateIdBeingEdited = null;

    // Query string properties
    protected $queryString = [
        'search' => ['except' => ''],
        'pricingBasisFilter' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    /**
     * Initialize the component.
     */
    public function mount()
    {
        $this->pricingBasisOptions = PricingBasis::options();

        $this->refreshOccupantTypeOptions();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $unitRates = UnitRateModel::query()
            ->when($this->search, fn($q) => $q->where('occupant_type', 'like', "%{$this->search}%"))
            ->when($this->pricingBasisFilter, fn($q) => $q->where('pricing_basis', $this->pricingBasisFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        return view('livewire.managers.oprations.unit-rates.index', compact('unitRates'));
    }


    /**
     * Create a new unit rate.
     */
    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Edit an existing unit rate.
     *
     * @param UnitRateModel $unitRate
     */
    public function edit(UnitRateModel $unitRate)
    {
        $this->unitRateIdBeingEdited = $unitRate->id;
        $this->price = $unitRate->price;
        $this->occupantType = $unitRate->occupant_type;
        $this->pricingBasis = $unitRate->pricing_basis->value;
        $this->requiresVerification = $unitRate->requires_verification;
        $this->showModal = true;
    }

    /**
     * Validation rules for the form.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'price' => 'required|numeric|min:0',
            'occupantType' => 'required|string|max:255',
            'pricingBasis' => [
                'required',
                Rule::in(PricingBasis::values()),
            ],
            'requiresVerification' => 'boolean',
        ];
    }

    /**
     * Save the unit rate data.
     */
    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'price' => $this->price,
            'occupant_type' => $this->occupantType,
            'pricing_basis' => $this->pricingBasis,
            'requires_verification' => $this->requiresVerification,
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

    /**
     * Confirm deletion of the unit rate.
     *
     * @param array $data
     */
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

    /**
     * Delete the unit rate.
     *
     * @param array $data
     */
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

    /**
     * Reset the form fields.
     */
    public function resetForm() {
        $this->price = '';
        $this->occupantType = '';
        $this->pricingBasis = '';
        $this->requiresVerification = false;
        $this->unitRateIdBeingEdited = null;
    }

    /**
     * Refresh the occupant type options from the database.
     */
    private function refreshOccupantTypeOptions()
    {
        $this->occupantTypeOptions = UnitRateModel::query()
            ->select('occupant_type')
            ->distinct()
            ->pluck('occupant_type')
            ->toArray();
    }
}
