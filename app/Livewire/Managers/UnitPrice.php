<?php

namespace App\Livewire\Managers;

use App\Enums\PricingBasis;
use App\Models\OccupantType;
use App\Models\UnitPrice as UnitPriceModel;
use App\Models\UnitType;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class UnitPrice extends Component
{
    public UnitType $unitType;

    // Main data properties
    public
        $occupantTypeId = '',
        $pricingBasis = '',
        $price,
        $maxPrice,
        $notes;

    // Options properties
    public $pricingBasisOptions = [];
    public $occupantTypesOptions = [];

    public $unitPriceIdBeingEdited = null;

    public $showForm = false;

    public function mount()
    {
        $this->pricingBasisOptions = PricingBasis::options();
        $this->occupantTypesOptions = OccupantType::select('id', 'name', 'requires_verification')
            ->orderBy('name')
            ->get()
            ->map( fn ($occupantType) => [
                'value' => $occupantType->id,
                'label' => $occupantType->name . ($occupantType->requires_verification ? ' (Verified)' : ''),
            ])->toArray();
    }

    public function render()
    {
        $unitPrices = $this->unitType->unitPrices()->with('occupantType')->get();

        return view("livewire.managers.oprations.unit-types.prices", compact('unitPrices'));
    }

    public function create()
    {
        $this->showForm = true;
        $this->resetForm();
    }

    public function edit(UnitPriceModel $unitPrice)
    {
        $this->unitPriceIdBeingEdited = $unitPrice->id;
        $this->occupantTypeId = $unitPrice->occupant_type_id;
        $this->pricingBasis = $unitPrice->pricing_basis->value;
        $this->price = $unitPrice->price;
        $this->maxPrice = $unitPrice->max_price;
        $this->notes = $unitPrice->notes;

        $this->showForm = true;
    }

    public function rules()
    {
        return [
            'occupantTypeId' => 'required|exists:occupant_types,id',
            'pricingBasis' => [
                'required',
                Rule::in(PricingBasis::values()),
            ],
            'price' => 'required|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0|gte:price',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'unit_type_id' => $this->unitType->id,
            'occupant_type_id' => $this->occupantTypeId,
            'pricing_basis' => $this->pricingBasis,
            'price' => $this->price,
            'max_price' => $this->maxPrice,
            'notes' => $this->notes,
        ];

        try {
            UnitPriceModel::updateOrCreate(
                ['id' => $this->unitPriceIdBeingEdited],
                $data
            );

            // Flash message
            LivewireAlert::title($this->unitPriceIdBeingEdited ? 'Data berhasil diperbarui.' : 'Harga unit berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

            // Reset form
            $this->resetForm();
            $this->showForm = false;
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                LivewireAlert::title('Data Sudah Ada')
                    ->text('Kombinasi tipe unit, tipe penghuni, dan basis harga sudah ada.')
                    ->warning()
                    ->withConfirmButton('Dimengerti')
                    ->timer(10000)
                    ->show();
            } else {
                LivewireAlert::title('Terjadi Kesalahan')
                    ->text('Gagal menyimpan data. Silakan coba lagi.')
                    ->warning()
                    ->withConfirmButton('Dimengerti')
                    ->timer(10000)
                    ->show();
            }
        }
    }

    public function confirmDelete($data)
    {
        $pricingBasis = PricingBasis::from($data['pricing_basis']);
        LivewireAlert::title('Hapus data '. $data['occupant_type']['name'] . ' Basis Harga ' . $pricingBasis->label() . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteUnitPrice', ['id' => $data['id']])
            ->show();
    }

    public function deleteUnitPrice($data)
    {
        $id = $data['id'];
        $unitPrice = UnitPriceModel::find($id);
        if ($unitPrice) {
            $unitPrice->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text($unitPrice->occupantType->name. ' '. $unitPrice->pricing_basis->label() . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function resetForm()
    {
        $this->occupantTypeId = '';
        $this->pricingBasis = '';
        $this->price = null;
        $this->maxPrice = null;
        $this->notes = '';
        $this->unitPriceIdBeingEdited = null;
    }
}
