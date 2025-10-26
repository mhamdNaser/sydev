<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => trim($this->first_name . ' ' . $this->medium_name . ' ' . $this->last_name),
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => [
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'address_3' => $this->address_3,
            ],
            'image' => $this->image,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'roles' => $this->getRoleNames(), // ترجع كل الأدوار كـ array
            'permissions' => $this->getAllPermissions()->pluck('name'), // كل الصلاحيات
            'created_at' => $this->created_at?->toDateTimeString(),
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
        ];
    }
}
