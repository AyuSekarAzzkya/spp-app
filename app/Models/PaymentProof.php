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
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
