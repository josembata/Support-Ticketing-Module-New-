<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'ticket_id';

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'assigned_to',
        'department_id',
        'priority',
        'status',
        'closed_at',
        'solution',
    ];

    protected $casts = [
    'resolved_at' => 'datetime',
];


    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

   public function assignedAgent()
{
    return $this->belongsTo(User::class, 'assigned_to');
}


    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function comments()
{
    return $this->hasMany(TicketComment::class, 'ticket_id')->latest();
}

public function histories()
{
    return $this->hasMany(TicketHistory::class, 'ticket_id', 'ticket_id')->latest();
}



public function logHistory($action, $oldValue = null, $newValue = null)
{
    \App\Models\TicketHistory::create([
        'ticket_id' => $this->ticket_id,
        'user_id' => auth()->id(),
        'action' => $action,
        'old_value' => $oldValue,
        'new_value' => $newValue,
    ]);
}


}
