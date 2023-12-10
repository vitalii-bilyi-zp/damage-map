<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DamageNotes\GetApproved as DamageNotesGetApproved;
use App\Http\Requests\DamageNotes\GetNotApproved as DamageNotesGetNotApproved;
use App\Http\Requests\DamageNotes\Store as DamageNotesStore;
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
            ->select('communities.name AS community', 'object_types.name AS object_type', 'damage_notes.*')
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
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }

    public function store(DamageNotesStore $request): JsonResponse
    {
        DamageNote::create([
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

        return $this->respondWithSuccess();
    }

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
        } catch (Exception $e) {
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
        if (isset($damageNote->object_type_id)) {
            $damageNote->object_type = ObjectType::find($damageNote->object_type_id);
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($damageNote);
    }

    public function update(DamageNotesUpdate $request, DamageNote $damageNote): JsonResponse {
        $damageNote->update([
            'date' => $request->date ?? $damageNote->date,
            'object_type_id' => $request->object_type_id ?? $damageNote->object_type_id,
            'community_id' => $request->community_id ?? $damageNote->community_id,
            'city' => $request->city ?? $damageNote->city,
            'street'  => $request->street ?? $damageNote->street,
            'building_number' => $request->building_number ?? $damageNote->building_number,
            'damage_type' => $request->damage_type ?? $damageNote->damage_type,
            'restoration_cost' => $request->restoration_cost ?? $damageNote->restoration_cost,
            'comment' => $request->comment ?? $damageNote->comment
        ]);

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
            ->groupBy('regions.id')
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
            ->groupBy('districts.id')
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
            ->groupBy('communities.id')
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
