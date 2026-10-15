<?php

namespace App\Models\Publikasi;

// USE SYSTEM
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasLogAktivitas;
// USE MODELS
use App\Models\Publikasi\Kategori;

class Berita extends Model
{
    use SoftDeletes, HasLogAktivitas;
    
    protected $table = 'beritas';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attachments' => 'array',
        'dynamic_content' => 'array',
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
            return asset('storage/images/berita/' . $this->photo);
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
                $attachment['url'] = asset('storage/attachments/berita/' . $attachment['path']);
            }
            return $attachment;
        })->toArray();
    }

    /**
     * Get formatted dynamic content with full URLs for images
     */
    public function getFormattedDynamicContentAttribute()
    {
        if (!$this->dynamic_content) {
            return [];
        }

        return collect($this->dynamic_content)->map(function ($block) {
            if ($block['type'] === 'image' && isset($block['data']['image_path'])) {
                $block['data']['image_url'] = asset('storage/images/berita/content/' . $block['data']['image_path']);
            }
            return $block;
        })->toArray();
    }

    /**
     * Check if this berita uses dynamic content
     */
    public function isDynamicContent()
    {
        return $this->content_type === 'dynamic';
    }

    /**
     * Get rendered content based on content type
     */
    public function getRenderedContentAttribute()
    {
        if ($this->isDynamicContent()) {
            return $this->renderDynamicContent();
        }
        
        return nl2br(e($this->content));
    }

    /**
     * Render dynamic content blocks as HTML
     */
    protected function renderDynamicContent()
    {
        if (!$this->dynamic_content) {
            return '';
        }

        $html = '';
        foreach ($this->formatted_dynamic_content as $block) {
            $html .= $this->renderContentBlock($block);
        }
        
        return $html;
    }

    /**
     * Render individual content block
     */
    protected function renderContentBlock($block)
    {
        switch ($block['type']) {
            case 'text':
                return '<div class="content-block text-block">' . nl2br(e($block['data']['content'])) . '</div>';
                
            case 'image':
                $caption = isset($block['data']['caption']) ? '<figcaption class="figure-caption">' . e($block['data']['caption']) . '</figcaption>' : '';
                $alignment = $block['data']['alignment'] ?? 'center';
                return '<figure class="content-block image-block text-' . $alignment . '">
                    <img src="' . $block['data']['image_url'] . '" class="img-fluid" alt="' . e($block['data']['alt_text'] ?? '') . '">
                    ' . $caption . '
                </figure>';
                
            case 'link':
                return '<div class="content-block link-block">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><a href="' . e($block['data']['url']) . '" target="_blank" rel="noopener">' . e($block['data']['title']) . '</a></h6>
                            <p class="card-text">' . e($block['data']['description']) . '</p>
                            <small class="text-muted">' . e($block['data']['url']) . '</small>
                        </div>
                    </div>
                </div>';
                
            case 'divider':
                return '<div class="content-block divider-block"><hr class="my-4"></div>';
                
            default:
                return '';
        }
    }

    /**
     * Delete attachment files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($berita) {
            if ($berita->attachments) {
                foreach ($berita->attachments as $attachment) {
                    if ($attachment['type'] === 'file' && isset($attachment['path'])) {
                        Storage::disk('public')->delete('attachments/berita/' . $attachment['path']);
                    }
                }
            }
        });
    }
}
