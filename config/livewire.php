<?php

return [
    // Livewire temporary uploads. Using the public disk makes previews reliable
    // on local dev without relying on the local file server.
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMP_DISK', 'public'),
        'directory' => env('LIVEWIRE_TEMP_DIR', 'livewire-tmp'),
        // Increase default limit to 25MB per file
        'rules' => 'image|max:25600', // size in KB
    ],

    // Default layout file for Livewire components
    'layout' => 'components.layouts.app',
    'component_layout' => 'components.layouts.app',
];
