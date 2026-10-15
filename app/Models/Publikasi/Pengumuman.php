<?php

namespace App\Models\Publikasi;

// USE SYSTEM
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
// USE MODELS
use App\Models\Publikasi\Kategori;

class Pengumuman extends Model
{
    use SoftDeletes;
    
    protected $table = 'pengumumen';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Accessor to get full image URL
    public function getImageUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/images/pengumuman/' . $this->photo);
        }
        return null;
    }

    /**
     * Get attachments with full URLs for files
     */
    public function getFormattedAttachmentsAttribute()
    {
        if (!$this->attachments) {
            return [];
        }

        return collect($this->attachments)->map(function ($attachment) {
            if ($attachment['type'] === 'file' && isset($attachment['path'])) {
                $attachment['url'] = asset('storage/attachments/pengumuman/' . $attachment['path']);
            }
            return $attachment;
        })->toArray();
    }

    /**
     * Delete attachment files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pengumuman) {
            if ($pengumuman->attachments) {
                foreach ($pengumuman->attachments as $attachment) {
                    if ($attachment['type'] === 'file' && isset($attachment['path'])) {
                        Storage::disk('public')->delete('attachments/pengumuman/' . $attachment['path']);
                    }
                }
            }
        });
    }
}
