<?php

use App\Models\Content;
$LogoUrl = optional(Content::where('content_key', 'logo_image_url')->first())->content_value ?? '';

?>

<img src="{{ url($LogoUrl) }}" alt="Logo Utama">