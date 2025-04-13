<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Agent extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'loaded'        => (bool) $this->id,
            'id'            => (int) $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'roles'         => $this->getRoleNames(),
            'permissions'   => $this->getPermissionNames(),
        ];
    }
}
