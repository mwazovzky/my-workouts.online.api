<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Api\V1\Resources\TemplateResource;
use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateController extends Controller
{
    public function index(): JsonResource
    {
        $templates = Template::with(['actions' => function ($query) {
            $query->with(['exercise' => ['group', 'equipment']]);
        }])->get();

        return TemplateResource::collection($templates);
    }
}
