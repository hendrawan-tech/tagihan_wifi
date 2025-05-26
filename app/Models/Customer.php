<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoicesLunas()
    {
        return $this->hasMany(Invoice::class)->where('status', 'Lunas');
    }

    public function invoicesBelumLunas()
    {
        return $this->hasMany(Invoice::class)->where('status', 'Belum Lunas');
    }

    public function invoicesKonfirmasi()
    {
        return $this->hasMany(Invoice::class)->where('status', 'Konfirmasi');
    }
}
