<?php

use App\Models\Content;

$LogoTitle = optional(Content::where('content_key', 'logo_title')->first())->content_value ?? '';

?>

<div class="flex aspect-square size-8 items-center justify-center rounded-md text-accent-foreground">
    <x-default.app-logo-icon class="size-5 fill-current text-white dark:text-black" />
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-none font-semibold">{{$LogoTitle}}</span>
</div>
