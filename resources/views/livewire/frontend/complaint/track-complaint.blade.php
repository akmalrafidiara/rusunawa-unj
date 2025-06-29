<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.frontend')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('modules.frontend.complaint.complaint-heading')
    <div class="container mx-auto relative overflow-hidden -mt-32 md:-mt-25 lg:-mt-25"> 
        <x-frontend.complaint.layout>
            <h2 class="text-xl font-semibold mb-4">{{ __('Lacak Pengaduan') }}</h2>
        </x-frontend.complaint.layout>
    </div>
</section>