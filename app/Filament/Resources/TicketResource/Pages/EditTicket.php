<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export')
                ->label('Export Ticket')
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function ($record) {
                    return \Maatwebsite\Excel\Facades\Excel::download(new class($record) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
                        public function __construct(private $record) {}
                        
                        public function collection()
                        {
                            return collect([$this->record]);
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
                                        $sender = $comment->user ? $comment->user->name : 'Unknown';
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
                                    $row->created_at,
                                    $row->replied_at,
                                    $row->solved_at ?? $row->closed_at,
                                    $firstResponseDuration,
                                    $resolutionDuration,
                                    $row->deskripsi_umum_masalah, // Subject matches deskripsi_umum usually, or heading custom
                                    strip_tags($row->penjelasan_lengkap),
                                    $chatHistory,
                                ];
                            }                  }, 'Ticket-' . $record->no_tiket . '.xlsx');
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
