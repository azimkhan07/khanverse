<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAttachment extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'project_id', 'file', 'created_at', 'updated_at'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
