<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'    =>  $this->id,
            'value'  =>  $this->name,
            'selectedPermissions'   =>  PermissionResource::collection($this->permissions),
            'permissions'   =>  PermissionResource::collection(Permission::all())
        ];
    }
}
