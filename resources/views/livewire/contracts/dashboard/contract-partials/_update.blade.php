@php
    $hasInvoiceIssue =
        ($latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID) ||
        ($latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION);

    $isAnyOccupantPending = $occupants->contains(function ($occupant) {
        return $occupant->status === \App\Enums\OccupantStatus::PENDING_VERIFICATION;
    });

    $isAnyOccupantRejected = $occupants->contains(function ($occupant) {
        return $occupant->status === \App\Enums\OccupantStatus::REJECTED;
    });

    $isPaymentRejected =
        $this->latestInvoice?->status == \App\Enums\InvoiceStatus::UNPAID &&
        $this->latestInvoice->payments->isNotEmpty() &&
        $this->latestInvoice->payments->last()->status == \App\Enums\PaymentStatus::REJECTED;

    $isPaymentPendingVerification =
        $this->latestInvoice &&
        $this->latestInvoice->status === \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION &&
        $this->latestInvoice->payments->isNotEmpty() &&
        $this->latestInvoice->payments->last()->status === \App\Enums\PaymentStatus::PENDING_VERIFICATION;

    $isKeyPendingHandover = $this->contract->key_status === \App\Enums\KeyStatus::PENDING_HANDOVER;

    $isKeyHandover = $this->contract->key_status === \App\Enums\KeyStatus::HANDED_OVER;

    $isKeyReturned = $this->contract->key_status === \App\Enums\KeyStatus::RETURNED;

    $doesntHasInvoice = !isset($latestInvoice) && !$isAnyOccupantPending && !$isAnyOccupantRejected;
@endphp

<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Status</h3>

    <div class="flex flex-col gap-4" wire:poll.10s>
        @if ($doesntHasInvoice)
            @include('livewire.contracts.dashboard.contract-partials.status._invoice-creating')
        @endif

        @if ($hasInvoiceIssue)
            @include('livewire.contracts.dashboard.contract-partials.status._invoice-detail')
        @endif

        @if ($isPaymentRejected)
            @include('livewire.contracts.dashboard.contract-partials.status._payment-rejected')
        @endif

        @if ($isPaymentPendingVerification)
            @include('livewire.contracts.dashboard.contract-partials.status._payment-pending')
        @endif

        {{-- Consolidated pending status check --}}
        @if ($isAnyOccupantPending)
            @include('livewire.contracts.dashboard.contract-partials.status._occupant-pending')
        @endif

        {{-- Consolidated rejected status check --}}
        @if ($isAnyOccupantRejected)
            @include('livewire.contracts.dashboard.contract-partials.status._occupant-rejected')
        @endif

        @if (
            !$doesntHasInvoice &&
                !$hasInvoiceIssue &&
                !$isAnyOccupantPending &&
                !$isAnyOccupantRejected &&
                !$isPaymentRejected &&
                !$isPaymentPendingVerification)
            @include('livewire.contracts.dashboard.contract-partials.status._default')

            @if ($isKeyPendingHandover)
                @include('livewire.contracts.dashboard.contract-partials.status._key-pending-handover')
            @endif
        @endif

        @if ($isKeyHandover)
            @include('livewire.contracts.dashboard.contract-partials.status._key-handover')
        @endif

        @if ($isKeyReturned)
            @include('livewire.contracts.dashboard.contract-partials.status._key-returned')
        @endif
    </div>
</div>
