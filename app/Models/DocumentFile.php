<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'name',
        'path',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function getPathAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

}
