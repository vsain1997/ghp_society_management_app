<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_type_id');
    }

}
