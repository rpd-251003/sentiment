<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request
$request = Illuminate\Http\Request::create(
    '/students/datatables',
    'GET',
    [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
        'search' => ['value' => ''],
        'order' => [['column' => 0, 'dir' => 'asc']]
    ]
);

// Mock authentication (you'll need to adjust user ID)
Auth::loginUsingId(3); // Assuming user ID 3 exists

$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . substr($response->getContent(), 0, 500) . "\n";

$kernel->terminate($request, $response);
