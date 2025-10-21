<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IconFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'dimensions'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * العلاقة مع موديل Icon
     */
    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    /**
     * سكوب للحصول على ملفات SVG فقط
     */
    public function scopeSvg($query)
    {
        return $query->where('file_type', 'svg');
    }

    /**
     * سكوب للحصول على ملفات PNG فقط
     */
    public function scopePng($query)
    {
        return $query->where('file_type', 'png');
    }

    /**
     * سكوب للحصول على ملفات بأبعاد محددة
     */
    public function scopeDimensions($query, $dimensions)
    {
        return $query->where('dimensions', $dimensions);
    }
}
