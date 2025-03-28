<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_to',
        'request_by',
        'request_by_role',
        'uploaded_by',
        'uploaded_by_role',
        'society_id',
        'status',
        'document_type_id',
        'file_type',
        'subject',
        'description',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }

    // Define the relationship with Society
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    // Define the relationship with User (created_by)
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'request_by');
    }
    public function requestTo()
    {
        return $this->belongsTo(User::class, 'request_to');
    }

    public function scopeSearchBySociety($query, $society_id)
    {
        if ($society_id) {
            return $query->where('society_id', '=', $society_id);
        }
        return $query;
    }

    public function requestedByMember()
    {
        return $this->belongsTo(Member::class, 'request_by', 'user_id');
    }

}
