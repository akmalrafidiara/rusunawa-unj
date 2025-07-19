{{-- resources/views/livewire/occupants/dashboard/contract-partials/status/_-main-card.blade.php --}}

{{-- Outer card container for the Status section --}}
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Status</h3>

    {{--
        Primary Logic Block:
        Checks if a contract exists and the latest invoice's status is either UNPAID
        or PENDING_PAYMENT_VERIFICATION. This signifies an active billing cycle
        or a payment awaiting confirmation.
    --}}
    @if (
        ($contract && $latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID) ||
            ($latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION))

        {{-- Include partial for detailed invoice status and payment actions --}}
        @include('livewire.occupants.dashboard.contract-partials.status/_invoice-detail')
    @else
        {{--
            Alternative Logic Blocks (executed if the primary invoice condition above is FALSE):
            These conditions check for various occupant verification statuses.
        --}}

        {{--
            Scenario 1: The current logged-in occupant's data verification is PENDING.
            This typically occurs after initial registration or data editing before any invoice is generated.
        --}}
        @if (isset($occupant) && $occupant->status === \App\Enums\OccupantStatus::PENDING_VERIFICATION && !isset($invoices))
            @include('livewire.occupants.dashboard.contract-partials.status/_occupant-pending')

            {{--
            Scenario 2: The contract exists, and *any* of its associated occupants (including or excluding the logged-in one)
            are in PENDING_VERIFICATION status. This is a broader check for pending verifications within the contract.
        --}}
        @elseif (
            $contract &&
                $contract->occupants->contains(function ($occupant) {
                    return $occupant->status === \App\Enums\OccupantStatus::PENDING_VERIFICATION;
                }))
            @include('livewire.occupants.dashboard.contract-partials.status/_other-occupant-pending')

            {{--
            Scenario 3: The current logged-in occupant's data verification has been REJECTED.
            They are prompted to re-submit their data.
        --}}
        @elseif (isset($occupant) && $occupant->status === \App\Enums\OccupantStatus::REJECTED)
            @include('livewire.occupants.dashboard.contract-partials.status/_occupant-rejected')

            {{--
            Default Scenario: None of the above conditions are met.
            This might indicate that the contract is fully active, or there's no contract yet,
            and no pending/rejected statuses need immediate attention.
        --}}
        @else
            @include('livewire.occupants.dashboard.contract-partials.status/_default')
        @endif
    @endif
</div>
