<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ComponentRegistry;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GrapesJsController extends Controller
{
    protected ComponentRegistry $registry;

    public function __construct(ComponentRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function editor()
    {
        return view('grapesjs.editor');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:10240'
        ]);

        $model = $request->user();  // Or any other model implementing HasMedia
        $media = $model->addMediaFromRequest('file')
            ->toMediaCollection('grapesjs-images');

        return response()->json([
            'success' => true,
            'file' => [
                'url' => $media->getUrl(),
                'id' => $media->id
            ]
        ]);
    }
}
