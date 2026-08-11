<?php

use Illuminate\Support\Facades\Storage;

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        // Uploads (product/category/settings images, payment proofs) are
        // written directly under public/uploads instead of the usual
        // storage/app/public + public/storage symlink. That symlink needs
        // a real NTFS volume to create (via `php artisan storage:link` or
        // an OS-level junction) — on a mapped/virtual drive where the
        // filesystem type isn't resolvable, both symlinks and junctions
        // fail outright, silently or otherwise. Writing straight into
        // public/ sidesteps the problem entirely at the cost of the files
        // being physically inside the web root.
        'public' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'url' => env('APP_URL').'/uploads',
            'visibility' => 'public',
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
