<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class MateriBab extends Model
{
    protected $table = 'materi_bab';

    protected $fillable = [
        'materi_id',
        'judul_bab',
        'urutan',
        'tipe_konten',
        'konten_teks',
        'file_path',
        'pdf_page_selection',
        'jumlah_halaman',
        'status_aktif',
        'summary_title',
        'summary_short',
        'summary_key_points',
        'summary_keywords',
        'summary_memory_tip',
        'summary_example',
        'summary_visual_path',
        'summary_generated_at',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'jumlah_halaman' => 'integer',
        'status_aktif' => 'boolean',
        'summary_key_points' => 'array',
        'summary_keywords' => 'array',
        'summary_generated_at' => 'datetime',
    ];

    protected $appends = [
        'file_url',
        'summary_visual_url',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function kuis()
    {
        return $this->hasMany(Kuis::class, 'materi_bab_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return URL::route('media.public.show', ['path' => $this->file_path], true);
    }

    public function getSummaryVisualUrlAttribute(): ?string
    {
        if (!$this->summary_visual_path) {
            return null;
        }

        return URL::route('media.public.show', ['path' => $this->summary_visual_path], true);
    }
}
