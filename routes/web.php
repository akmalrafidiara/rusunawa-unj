<?php

use App\Livewire\Frontend\Announcement\ShowAnnouncement;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Enums\RoleUser; // Import RoleUser

// === SCOPE FRONTEND ROUTES ===
require __DIR__.'/frontend.php';

// === SCOPE MANAGERS ROUTES ===
require __DIR__.'/managers.php';

// === SCOPE CONTRACT ROUTES ===
require __DIR__.'/contract.php';

// === SCOPE AUTH ROUTES ===
require __DIR__.'/auth.php';
