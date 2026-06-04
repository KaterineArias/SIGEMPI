<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    protected $table      = 'password_reset_tokens';
    protected $primaryKey = 'id';
    public    $timestamps = false; 

    protected $fillable = [
        'ID_User',
        'token',
        'usado',
        'expires_at',
    ];

    protected $casts = [
        'usado'      => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function esValido(): bool
    {
        return !$this->usado && Carbon::now()->lessThan($this->expires_at);
    }
}