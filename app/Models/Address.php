<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $table = 'addresses';
    public $timestamps = true;
    public $incrementing = true;

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    protected $fillable = [
        'street',
        'city',
        'province',
        'country',
        'postal_code',
    ];
}
