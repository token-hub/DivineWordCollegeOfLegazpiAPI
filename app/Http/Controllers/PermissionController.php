<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function __invoke()
    {
        return PermissionResource::collection(Permission::latest()->get());
    }
}
