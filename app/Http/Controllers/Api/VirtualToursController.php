<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VirtualTours\Index as VirtualToursIndex;
use App\Http\Requests\VirtualTours\Store as VirtualToursStore;
use App\Models\VirtualTour;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class VirtualToursController extends Controller
{
    use ApiResponseHelpers;

    public function index(VirtualToursIndex $request): JsonResponse
    {
        $virtualTours = VirtualTour::all();
        return $this->setDefaultSuccessResponse([])->respondWithSuccess($virtualTours);
    }

    public function store(VirtualToursStore $request): JsonResponse
    {
        $imageName = null;
        $imageHashName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'virtual_tours');
            $imageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $imageHashName = basename($imagePath);
        }

        $audioName = null;
        $audioHashName = null;
        if ($request->hasFile('audio_file')) {
            $audio = $request->file('audio_file');
            $audioPath = $audio->store('audio', 'virtual_tours');
            $audioName = pathinfo($audio->getClientOriginalName(), PATHINFO_FILENAME);
            $audioHashName = basename($audioPath);
        }

        VirtualTour::create([
            'title' => $request->title,
            'description' => $request->description ?? null,
            'damage_note_id' => $request->damage_note_id ?? null,
            'image_file_name' => $imageName,
            'image_hash_file_name' => $imageHashName,
            'audio_file_name' => $audioName,
            'audio_hash_file_name' => $audioHashName,
        ]);

        return $this->respondWithSuccess();
    }
}
