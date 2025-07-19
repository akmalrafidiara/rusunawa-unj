@php
    $hasInvoiceIssue =
        ($contract && $latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID) ||
        ($latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION);

    $isCurrentOccupantPending =
        isset($occupant) && $occupant->status === \App\Enums\OccupantStatus::PENDING_VERIFICATION;
    $isCurrentOccupantRejected = isset($occupant) && $occupant->status === \App\Enums\OccupantStatus::REJECTED;

    // Use the computed properties from the Livewire component
    $hasOtherOccupantPending = $this->pendingOtherOccupants->isNotEmpty();
    $hasOtherOccupantRejected = $this->rejectedOtherOccupants->isNotEmpty();

    $isPaymentRejected =
        $this->latestInvoice?->status == \App\Enums\InvoiceStatus::UNPAID &&
        $this->latestInvoice->payments->isNotEmpty() &&
        $this->latestInvoice->payments->last()->status == \App\Enums\PaymentStatus::REJECTED;

    $isPaymentPendingVerification =
        $this->latestInvoice &&
        $this->latestInvoice->status === \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION &&
        $this->latestInvoice->payments->isNotEmpty() &&
        $this->latestInvoice->payments->last()->status === \App\Enums\PaymentStatus::PENDING_VERIFICATION;
@endphp

<div wire:poll.10s class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Status</h3>

    @if ($hasInvoiceIssue)
        @include('livewire.occupants.dashboard.contract-partials.status._invoice-detail')
    @endif

    {{-- Consolidated pending status check --}}
    @if ($isCurrentOccupantPending || $hasOtherOccupantPending)
        @include('livewire.occupants.dashboard.contract-partials.status._occupant-pending', [
            'contract' => $contract, // Pass contract to the partial
            'occupant' => $occupant, // Pass occupant to the partial
        ])
    @endif

    {{-- Consolidated rejected status check --}}
    @if ($isCurrentOccupantRejected || $hasOtherOccupantRejected)
        @include('livewire.occupants.dashboard.contract-partials.status._occupant-rejected', [
            'contract' => $contract, // Pass contract to the partial
            'occupant' => $occupant, // Pass occupant to the partial
        ])
    @endif

    @if ($isPaymentRejected)
        @include('livewire.occupants.dashboard.contract-partials.status._payment-rejected')
    @endif

    @if ($isPaymentPendingVerification)
        @include('livewire.occupants.dashboard.contract-partials.status._payment-pending')
    @endif

    @if (
        !$hasInvoiceIssue &&
            !$isCurrentOccupantPending &&
            !$hasOtherOccupantPending &&
            !$isCurrentOccupantRejected &&
            !$hasOtherOccupantRejected &&
            !$isPaymentRejected &&
            !$isPaymentPendingVerification)
        @include('livewire.occupants.dashboard.contract-partials.status._default')
    @endif
</div>
