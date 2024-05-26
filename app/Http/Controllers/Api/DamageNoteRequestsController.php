<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\DamageNoteRequests\Store as DamageNoteRequestsStore;
use App\Http\Requests\DamageNoteRequests\Approve as DamageNoteRequestsApprove;
use App\Http\Requests\DamageNoteRequests\Decline as DamageNoteRequestsDecline;

use App\Models\DamageNote;
use App\Models\DamageNoteRequest;

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
            'city' => $request->city,
            'street'  => $request->street,
            'building_number' => $request->building_number,
            'damage_type' => $request->damage_type,
            'restoration_cost' => $request->restoration_cost,
            'comment' => $request->comment
        ]);

        DamageNoteRequest::create([
            'full_name' => $request->full_name ?? null,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'damage_note_id' => $damageNote->id,
        ]);

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
