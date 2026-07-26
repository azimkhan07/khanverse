<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAttachment extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'user_id', 'uploaded_by', 'file_name', 'file_path', 'file_size', 'mime_type', 'attribute',];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
