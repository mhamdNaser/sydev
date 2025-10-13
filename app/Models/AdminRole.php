<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class AdminRole extends Model
{
    protected $table = 'roles'; // نفس جدول المكتبة

    protected $fillable = [
        'name',
        'guard_name',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * إضافة أي علاقات مخصصة لاحقاً
     */
    public function users()
    {
        // في حال بتستخدم Sanctum أو نظام مستخدمين خاص
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    }
}
