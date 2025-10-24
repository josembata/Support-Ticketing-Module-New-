<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================
    // RELATIONSHIPS
    // ==========================

    /**
     * Tickets created by the user (customer)
     */
    public function createdTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    /**
     * Tickets assigned to the user (agent)
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Comments made by the user
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }


    
public function routeNotificationForTwilio($notification)
{
    
    return $this->phone;
}

}
