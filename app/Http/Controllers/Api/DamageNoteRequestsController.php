<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\DamageNoteRequests\Store as DamageNoteRequestsStore;
use App\Http\Requests\DamageNoteRequests\Approve as DamageNoteRequestsApprove;
use App\Http\Requests\DamageNoteRequests\Decline as DamageNoteRequestsDecline;

use App\Models\DamageNote;
use App\Models\DamageNoteRequest;
use App\Models\DamageNoteImage;
use Illuminate\Support\Facades\Storage;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class DamageNoteRequestsController extends Controller
{
    use ApiResponseHelpers;

    public function store(DamageNoteRequestsStore $request): JsonResponse
    {
        $damageNote = DamageNote::create([
            'date' => $request->date,
            'object_type_id' => $request->object_type_id,
            'community_id' => $request->community_id,
            'city' => $request->city ?? null,
            'street'  => $request->street ?? null,
            'building_number' => $request->building_number ?? null,
            'floors' => $request->floors,
            'area' => $request->area,
            'damage_type' => $request->damage_type ?? null,
            'repair_type_id' => $request->repair_type_id ?? null,
            'restoration_cost' => $request->restoration_cost ?? null,
            'comment' => $request->comment ?? null
        ]);

        DamageNoteRequest::create([
            'full_name' => $request->full_name ?? null,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'damage_note_id' => $damageNote->id,
        ]);

        $images = $request->images;

        foreach($images as $image) {
            $fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $hashFileName = uniqid() . '.' . $image->getClientOriginalExtension();

            Storage::disk('damage_note_images')->putFileAs(
                '', // Directory (empty means root of the disk)
                $image,
                $hashFileName
            );

            DamageNoteImage::create([
                'file_name' => $fileName,
                'hash_file_name' => $hashFileName,
                'damage_note_id' => $damageNote->id,
            ]);
        }

        return $this->respondWithSuccess();
    }

    public function approveRequest(DamageNoteRequestsApprove $request, DamageNoteRequest $damageNoteRequest): JsonResponse
    {
        $damageNoteRequest->update([
            'approver_id' => auth()->user()->id,
            'approved_at' => now(),
        ]);

        return $this->respondWithSuccess();
    }

    public function declineRequest(DamageNoteRequestsDecline $request, DamageNoteRequest $damageNoteRequest): JsonResponse
    {
        $damageNoteRequest->update([
            'approver_id' => auth()->user()->id,
            'approver_comment' => $request->comment,
            'declined_at' => now(),
        ]);

        return $this->respondWithSuccess();
    }
}
