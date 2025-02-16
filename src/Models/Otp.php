<?php

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $table = 'otp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'expires_in',
        'valid',
    ];
}
