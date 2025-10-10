<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'languages';

    // الأعمدة القابلة للتعديل
    protected $fillable = [
        'code',
        'name',
        'is_default',
        'active',
    ];

    // الأعمدة التي يجب تحويلها لأنواع معينة
    protected $casts = [
        'is_default' => 'boolean',
        'active' => 'boolean',
    ];
}
