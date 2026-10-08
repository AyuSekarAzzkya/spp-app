<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentProof extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_id',
        'image_path',
        'amount',
        'note',
        'version',
    ];

    protected $casts = [
        'amount'  => 'integer',
        'version' => 'integer',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * URL aman untuk mengakses gambar bukti dengan otorisasi.
     */
    public function getUrlAttribute(): string
    {
        return route('payments.proof.show', $this->id);
    }

    /**
     * Memeriksa apakah file fisik bukti transfer ada di storage disk.
     */
    public function fileExists(): bool
    {
        if (empty($this->image_path)) {
            return false;
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->exists($this->image_path)
            || \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image_path)
            || file_exists(storage_path('app/' . $this->image_path))
            || file_exists(storage_path('app/public/' . $this->image_path))
            || file_exists(storage_path('app/private/' . $this->image_path));
    }
}
