<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Occupant;
use App\Models\Invoice;
use App\Models\Contract;
use App\Notifications\OccupantVerificationNotification;
use App\Notifications\PaymentConfirmationNotification;
use App\Notifications\ContractNotification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin/manager users
        $managers = User::whereIn('role', ['admin', 'head_of_rusunawa', 'staff_of_rusunawa'])->get();

        if ($managers->isEmpty()) {
            $this->command->warn('No manager users found. Please create admin/manager users first.');
            return;
        }

        // Sample notifications for occupant verification
        if (Occupant::exists()) {
            $occupants = Occupant::take(3)->get();
            foreach ($occupants as $occupant) {
                foreach ($managers as $manager) {
                    $manager->notify(new OccupantVerificationNotification(
                        $occupant,
                        "New occupant {$occupant->name} is waiting for verification",
                        'pending'
                    ));
                }
            }
        }

        // Sample notifications for payment confirmation
        if (Invoice::exists()) {
            $invoices = Invoice::take(2)->get();
            foreach ($invoices as $invoice) {
                foreach ($managers as $manager) {
                    $manager->notify(new PaymentConfirmationNotification(
                        $invoice,
                        "Payment received for invoice #{$invoice->invoice_number}"
                    ));
                }
            }
        }

        // Sample notifications for contracts
        if (Contract::exists()) {
            $contracts = Contract::take(2)->get();
            foreach ($contracts as $contract) {
                foreach ($managers as $manager) {
                    $manager->notify(new ContractNotification($contract, 'expiring'));
                }
            }
        }

        $this->command->info('Sample notifications created successfully!');
    }
}
