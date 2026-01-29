<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class TicketExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'No Tiket',
            'Lokasi',
            'Nama Lengkap',
            'Topik Bantuan',
            'Email',
            'No HP',
            'Deskripsi Masalah',
            'Status',
            'Dibuat Pada',
            'Direspon Pada',
            'Diselesaikan Pada',
            'Waktu First Response',
            'Waktu Pengerjaan',
            'Subjek',
            'Pesan Awal',
            'Riwayat Chat',
        ];
    }

    public function map($row): array
    {
        // Calculate SLA Durations (Format: 0d 0h 0m 0s)
        $firstResponseDuration = '-';
        if ($row->created_at && $row->replied_at) {
            $firstResponseDuration = $row->created_at->diff($row->replied_at)->format('%ad %hh %im %ss');
        }

        $resolutionDuration = '-';
        $end = $row->solved_at ?? $row->closed_at;
        if ($row->created_at && $end) {
            $resolutionDuration = $row->created_at->diff($end)->format('%ad %hh %im %ss');
        }

        // Format Chat History
        $chatHistory = '';
        if ($row->comments && $row->comments->count() > 0) {
            foreach ($row->comments as $comment) {
                $sender = $comment->user ? $comment->user->name : ($row->nama_lengkap ?? 'Pelapor');
                $time = $comment->created_at ? $comment->created_at->format('d/m/Y H:i') : '-';
                $content = strip_tags($comment->content);
                $chatHistory .= "[{$time}] {$sender}: {$content}\n";
            }
        }

        return [
            $row->no_tiket,
            $row->lokasi,
            $row->nama_lengkap,
            $row->topik_bantuan,
            $row->email,
            $row->no_hp,
            $row->deskripsi_umum_masalah,
            $row->status,
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '',
            $row->replied_at ? $row->replied_at->format('Y-m-d H:i:s') : '',
            ($row->solved_at ?? $row->closed_at) ? ($row->solved_at ?? $row->closed_at)->format('Y-m-d H:i:s') : '',
            $firstResponseDuration,
            $resolutionDuration,
            $row->deskripsi_umum_masalah,
            strip_tags($row->penjelasan_lengkap ?? ''),
            $chatHistory,
        ];
    }
}
