<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DamageNotes\GetApproved as DamageNotesGetApproved;
use App\Http\Requests\DamageNotes\GetNotApproved as DamageNotesGetNotApproved;
use App\Http\Requests\DamageNotes\Search as DamageNotesSearch;
// use App\Http\Requests\DamageNotes\Store as DamageNotesStore;
use App\Http\Requests\DamageNotes\StoreFromFile as DamageNotesStoreFromFile;
use App\Http\Requests\DamageNotes\Show as DamageNotesShow;
use App\Http\Requests\DamageNotes\Update as DamageNotesUpdate;
use App\Http\Requests\DamageNotes\Destroy as DamageNotesDestroy;
use App\Http\Requests\DamageNotes\ShowRegions as DamageNotesShowRegions;
use App\Http\Requests\DamageNotes\ShowDistricts as DamageNotesShowDistricts;
use App\Http\Requests\DamageNotes\ShowCommunities as DamageNotesShowCommunities;
use App\Http\Requests\DamageNotes\ExportCsv as DamageNotesExportCsv;

use App\Models\DamageNote;
use App\Models\DamageNoteRequest;
use App\Models\ObjectType;
use App\Models\Community;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Actions\PredictRestorationCostAction;
use App\Exceptions\UpstreamRequestException;

use PhpOffice\PhpSpreadsheet\IOFactory;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class DamageNotesController extends Controller
{
    use ApiResponseHelpers;

    public function getApproved(DamageNotesGetApproved $request): JsonResponse
    {
        $user = auth()->user();

        $aggregation = DamageNoteRequest::query()
            ->join('damage_notes', 'damage_note_requests.damage_note_id', '=', 'damage_notes.id')
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
            ->select('communities.name AS community', 'object_types.name AS object_type', 'damage_notes.*')
            ->whereNotNull('damage_note_requests.approved_at')
            ->when(isset($user->region_id), function($query) use (&$user) {
                $query
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->where('districts.region_id', '=', $user->region_id);
            })
            ->when(isset($user->district_id), function($query) use (&$user) {
                $query->where('communities.district_id', '=', $user->district_id);
            })
            ->when(isset($user->community_id), function($query) use (&$user) {
                $query->where('damage_notes.community_id', '=', $user->community_id);
            })
            ->orderBy('damage_notes.id', 'desc')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }

    public function getNotApproved(DamageNotesGetNotApproved $request): JsonResponse
    {
        $user = auth()->user();

        $aggregation = DamageNoteRequest::query()
            ->join('damage_notes', 'damage_note_requests.damage_note_id', '=', 'damage_notes.id')
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
            ->select('communities.name AS community', 'object_types.name AS object_type', 'damage_notes.*', 'damage_note_requests.id AS request_id')
            ->whereNull('damage_note_requests.approved_at')
            ->whereNull('damage_note_requests.declined_at')
            ->when(isset($user->region_id), function($query) use (&$user) {
                $query
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->where('districts.region_id', '=', $user->region_id);
            })
            ->when(isset($user->district_id), function($query) use (&$user) {
                $query->where('communities.district_id', '=', $user->district_id);
            })
            ->when(isset($user->community_id), function($query) use (&$user) {
                $query->where('damage_notes.community_id', '=', $user->community_id);
            })
            ->orderBy('damage_notes.id', 'desc')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }

    public function search(DamageNotesSearch $request): JsonResponse
    {
        $searchQuery = $request->q;

        $aggregation = DamageNoteRequest::query()
            ->join('damage_notes', 'damage_note_requests.damage_note_id', '=', 'damage_notes.id')
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->select('communities.name AS community', 'damage_notes.*', \DB::raw("CONCAT(street, ' ', building_number, ', ', city) AS address"))
            ->whereNotNull('damage_note_requests.approved_at')
            ->when(isset($searchQuery), function($query) use ($searchQuery) {
                $query->whereRaw("CONCAT(street, ' ', building_number, ', ', city) LIKE '%{$searchQuery}%' OR communities.name LIKE '%{$searchQuery}%'");
            })
            ->orderBy('damage_notes.id', 'desc')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }

    // public function store(DamageNotesStore $request): JsonResponse
    // {
    //     DamageNote::create([
    //         'date' => $request->date,
    //         'object_type_id' => $request->object_type_id,
    //         'community_id' => $request->community_id,
    //         'city' => $request->city,
    //         'street'  => $request->street,
    //         'building_number' => $request->building_number,
    //         'floors' => $request->floors,
    //         'area' => $request->area,
    //         'damage_type' => $request->damage_type,
    //         'repair_type_id' => $request->repair_type_id,
    //         'restoration_cost' => $request->restoration_cost,
    //         'comment' => $request->comment
    //     ]);

    //     return $this->respondWithSuccess();
    // }

    public function storeFromFile(DamageNotesStoreFromFile $request): JsonResponse
    {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->storeAs('tmp', \Str::random(40) . '.' . $extension);
        $fullPath = storage_path() . '/app/' . $path;

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            for ($row = 1; $row <= $highestRow; ++$row) {
                // date
                $date = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                if (!isset($date)) {
                    continue;
                }
                $pattern = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';
                if (!preg_match($pattern, $date)) {
                    continue;
                }
                $startDate = '2022-02-24';
                $endDate = date('Y-m-d');
                if ($date < $startDate || $date > $endDate) {
                    continue;
                }

                // object type
                $object_type = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                if (!isset($object_type)) {
                    continue;
                }
                $db_object_type = ObjectType::firstWhere('name', trim($object_type));
                if (!isset($db_object_type)) {
                    continue;
                }
                $object_type_id = $db_object_type->id;

                // community
                $community = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                if (!isset($community)) {
                    continue;
                }
                $db_community = Community::firstWhere('name', trim($community));
                if (!isset($db_community)) {
                    continue;
                }
                $community_id = $db_community->id;

                // city
                $city = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                if (isset($city)) {
                    $city = trim($city);
                }

                // street
                $street = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                if (isset($street)) {
                    $street = trim($street);
                }

                // building number
                $building_number = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                if (isset($building_number)) {
                    $building_number = trim($building_number);
                }

                // damage type
                $damage_type = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                if (!isset($damage_type)) {
                    continue;
                }
                $damage_type_key = array_search(trim($damage_type), DamageNote::DAMAGE_TYPES_MAPPING);
                if (!$damage_type_key) {
                    continue;
                }

                // restoration cost
                $restoration_cost = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                if (!isset($restoration_cost) || !is_numeric($restoration_cost)) {
                    continue;
                }

                $comment = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                if (isset($comment)) {
                    $comment = trim($comment);
                }

                DamageNote::create([
                    'date' => $date,
                    'object_type_id' => $object_type_id,
                    'community_id' => $community_id,
                    'city' => $city,
                    'street'  => $street,
                    'building_number' => $building_number,
                    'damage_type' => $damage_type_key,
                    'restoration_cost' => $restoration_cost,
                    'comment' => $comment
                ]);
            }
        } catch (\Exception $e) {
            return $this->respondError();
        }

        Storage::delete($path);

        return $this->respondWithSuccess();
    }

    public function exportCsv(DamageNotesExportCsv $request)
    {
        $fileName = 'damage-notes.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $user = auth()->user();

        $damageNotes = DamageNote::query()
            ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('districts', 'communities.district_id', '=', 'districts.id')
            ->join('regions', 'districts.region_id', '=', 'regions.id')
            ->select('communities.name AS community', 'districts.name AS district', 'regions.name AS region', 'object_types.name AS object_type', 'damage_notes.*')
            ->when(isset($user->region_id), function($query) use (&$user) {
                $query->where('regions.id', '=', $user->region_id);
            })
            ->when(isset($user->district_id), function($query) use (&$user) {
                $query->where('districts.id', '=', $user->district_id);
            })
            ->when(isset($user->community_id), function($query) use (&$user) {
                $query->where('communities.id', '=', $user->community_id);
            })
            ->get();

        $columns = ['ID', 'Date', 'Object type', 'Region', 'District', 'Community', 'City', 'Street', 'Building number', 'Damage type', 'Restoration cost', 'Comment'];

        $callback = function() use($damageNotes, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($damageNotes as $damageNote) {
                $row = [
                    'ID' => $damageNote->id,
                    'Date' => $damageNote->date,
                    'Object type' => $damageNote->object_type,
                    'Region' => $damageNote->region,
                    'District' => $damageNote->district,
                    'Community' => $damageNote->community,
                    'City' => $damageNote->city,
                    'Street' => $damageNote->street,
                    'Building number' => $damageNote->building_number,
                    'Damage type' => $damageNote->damage_type,
                    'Restoration cost' => $damageNote->restoration_cost,
                    'Comment' => $damageNote->comment,
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(DamageNotesShow $request, DamageNote $damageNote): JsonResponse {
        $damageNote->load([
            'damageNoteRequest',
            'damageNoteImages',
            'objectType',
            'objectType.objectCategory',
            'community',
            'community.district',
            'community.district.region',
            'repairType'
        ]);

        $damageNote->damageNoteImages->transform(function ($item, $key) {
            $item->file_path = Storage::disk('damage_note_images')->url($item->hash_file_name);

            return $item;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($damageNote);
    }

    public function update(DamageNotesUpdate $request, DamageNote $damageNote, PredictRestorationCostAction $action): JsonResponse {
        $data = $request->validated();

        $newAttributes = [
            'date'             => $data['date']            ?? $damageNote->date,
            'object_type_id'   => $data['object_type_id']  ?? $damageNote->object_type_id,
            'community_id'     => $data['community_id']    ?? $damageNote->community_id,
            'city'             => $data['city']            ?? $damageNote->city,
            'street'           => $data['street']          ?? $damageNote->street,
            'building_number'  => $data['building_number'] ?? $damageNote->building_number,
            'floors'           => $data['floors']          ?? $damageNote->floors,
            'area'             => $data['area']            ?? $damageNote->area,
            'damage_type'      => $data['damage_type']     ?? $damageNote->damage_type,
            'repair_type_id'   => $data['repair_type_id']  ?? $damageNote->repair_type_id,
            'restoration_cost' => $data['restoration_cost']?? $damageNote->restoration_cost,
            'comment'          => $data['comment']         ?? $damageNote->comment,
        ];

        $predictorKeys = ['object_type_id', 'community_id', 'repair_type_id', 'damage_type', 'area', 'floors'];

        $shouldRecalculate = false;
        foreach ($predictorKeys as $key) {
            if ((string)($newAttributes[$key] ?? null) !== (string)($damageNote->{$key} ?? null)) {
                $shouldRecalculate = true;
                break;
            }
        }

        $newPredictedCost = null;
        if ($shouldRecalculate) {
            try {
                $prediction = $action->execute([
                    'object_type_id' => $newAttributes['object_type_id'],
                    'community_id'   => $newAttributes['community_id'],
                    'repair_type_id' => $newAttributes['repair_type_id'],
                    'damage_type'    => $newAttributes['damage_type'],
                    'area'           => $newAttributes['area'],
                    'floors'         => $newAttributes['floors'],
                ]);

                $newPredictedCost = $prediction['predicted_cost'] ?? null;
                $newAttributes['predicted_restoration_cost'] = $newPredictedCost;
            } catch (UpstreamRequestException $e) {
                return $this->respondError('Flask request failed', [
                    'status' => $e->status(),
                    'flask_error' => $e->response(),
                ], 502);
            } catch (\Throwable $e) {
                return $this->respondError('Upstream request failed', [
                    'message' => $e->getMessage(),
                ], 502);
            }
        }

        $damageNote->update($newAttributes);

        return $this->respondWithSuccess();
    }

    public function destroy(DamageNotesDestroy $request, DamageNote $damageNote): JsonResponse
    {
        $damageNote->delete();

        return $this->respondWithSuccess();
    }

    public function showRegions(DamageNotesShowRegions $request): JsonResponse
    {
        $query = DamageNote::query()
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('districts', 'communities.district_id', '=', 'districts.id')
            ->join('regions', 'districts.region_id', '=', 'regions.id')
            ->groupBy('regions.id', 'regions.name')
            ->select(DB::raw('regions.name, SUM(damage_notes.restoration_cost) AS restoration_cost'));

        if ($request->get('object_type_id')) {
            $query->where('damage_notes.object_type_id', $request->get('object_type_id'));
        } else if ($request->get('object_category_id')) {
            $query
                ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
                ->join('object_categories', 'object_types.object_category_id', '=', 'object_categories.id')
                ->where('object_categories.id', $request->get('object_category_id'));
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($query->get());
    }

    public function showDistricts(DamageNotesShowDistricts $request): JsonResponse
    {
        $query = DamageNote::query()
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('districts', 'communities.district_id', '=', 'districts.id')
            ->groupBy('districts.id', 'districts.name')
            ->select(DB::raw('districts.name, SUM(damage_notes.restoration_cost) AS restoration_cost'));

        if ($request->get('object_type_id')) {
            $query->where('damage_notes.object_type_id', $request->get('object_type_id'));
        } else if ($request->get('object_category_id')) {
            $query
                ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
                ->join('object_categories', 'object_types.object_category_id', '=', 'object_categories.id')
                ->where('object_categories.id', $request->get('object_category_id'));
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($query->get());
    }

    public function showCommunities(DamageNotesShowCommunities $request): JsonResponse
    {
        $query = DamageNote::query()
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->groupBy('communities.id', 'communities.name')
            ->select(DB::raw('communities.name, SUM(damage_notes.restoration_cost) AS restoration_cost'));

        if ($request->get('object_type_id')) {
            $query->where('damage_notes.object_type_id', $request->get('object_type_id'));
        } else if ($request->get('object_category_id')) {
            $query
                ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
                ->join('object_categories', 'object_types.object_category_id', '=', 'object_categories.id')
                ->where('object_categories.id', $request->get('object_category_id'));
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($query->get());
    }
}
