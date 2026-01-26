<?php

use App\Models\Ticket;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ticket = Ticket::latest()->with('sla')->first();

if (!$ticket) {
    echo json_encode(['error' => 'No ticket found']);
    exit;
}

$sla = $ticket->sla;
$timeParts = explode(':', $sla->response_time ?? '00:00:00');
$deadline = $ticket->created_at->copy()
    ->addDays((int)$sla->response_days)
    ->addHours((int)$timeParts[0])
    ->addMinutes((int)$timeParts[1]);

$repliedAt = $ticket->replied_at ? (
    $ticket->replied_at instanceof Carbon ? $ticket->replied_at : Carbon::parse($ticket->replied_at)
) : null;

$isOverdue = $repliedAt && $repliedAt->gt($deadline);

$result = [
    'id' => $ticket->id,
    'created_at' => (string)$ticket->created_at,
    'replied_at' => (string)$repliedAt,
    'sla_response_time' => $sla ? $sla->response_time : null,
    'sla_response_days' => $sla ? $sla->response_days : null,
    'deadline' => (string)$deadline,
    'is_overdue' => $isOverdue,
    'diff_minutes' => $repliedAt ? $ticket->created_at->diffInMinutes($repliedAt) : null,
];

echo json_encode($result, JSON_PRETTY_PRINT);
