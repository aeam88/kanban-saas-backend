<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'workspace_id',
        'board_id', 
        'title', 
        'description', 
        'position', 
        'assigned_to', 
        'due_date',
        'priority'
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
