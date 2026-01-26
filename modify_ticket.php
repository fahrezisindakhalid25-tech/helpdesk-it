<?php

use App\Models\Ticket;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ticket = Ticket::latest()->first();

if ($ticket) {
    $ticket->created_at = now()->subDays(2);
    $ticket->save();
    echo "Ticket #{$ticket->no_tiket} updated created_at to 2 days ago.\n";
} else {
    echo "No ticket found.\n";
}
