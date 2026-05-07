<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['workspace_id', 'name', 'description'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }
}
