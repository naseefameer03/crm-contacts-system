<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportReady extends Model
{
    protected $fillable = ['filepath', 'is_completed', 'is_viewed'];
    protected $appends = ['full_file_path']; 

    /**
     * Get the export file path.
     *
     * @return string
     */
    public function getFullFilePathAttribute()
    {
        if (empty($this->attributes['filepath'])) {
            return '';
        }
        return asset('storage/' . $this->attributes['filepath']);
    }
}
