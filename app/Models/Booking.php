<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $fillable = [
        'email',
        'sender_location',
        'recipient_location',
        'length',
        'width',
        'height',
        'weight',
        'time',
        'distance',
        'inclusions',
        'remarks',
        'price',
        'mode_of_payment',
    ];


    protected $primaryKey = 'order_number';

}
