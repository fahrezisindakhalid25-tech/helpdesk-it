<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Prediksi kategori otomatis via NLP
        try {
            $teksKeluhan = strip_tags($data['deskripsi_umum_masalah'] . '. ' . $data['penjelasan_lengkap']);
            $daftarKategori = \App\Models\Category::pluck('name')->toArray();
            
            $setting = \App\Models\Setting::where('key', 'ai_confidence_threshold')->first();
            $threshold = $setting ? (float)$setting->value : 0.20;
            
            $response = Http::timeout(30)->post('http://127.0.0.1:8000/predict', [
                'description' => $teksKeluhan,
                'categories' => $daftarKategori,
                'threshold' => $threshold
            ]);
            
            if ($response->successful()) {
                $data['topik_bantuan'] = $response->json('predicted_category');
            } else {
                $data['topik_bantuan'] = 'Lain-lain';
            }
        } catch (\Exception $e) {
            \Log::error("NLP API Connection Error: " . $e->getMessage());
            $data['topik_bantuan'] = 'Lain-lain';
        }

        return $data;
    }
}
