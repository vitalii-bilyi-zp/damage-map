<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegulationDocuments\Index as RegulationDocumentsIndex;
use App\Http\Requests\RegulationDocuments\Store as RegulationDocumentsStore;
use App\Http\Requests\RegulationDocuments\Destroy as RegulationDocumentsDestroy;
use App\Models\RegulationDocument;
use Illuminate\Support\Facades\Storage;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RegulationDocumentsController extends Controller
{
    use ApiResponseHelpers;

    public function index(RegulationDocumentsIndex $request): JsonResponse
    {
        $documents = RegulationDocument::all();
        $documents->transform(function ($item, $key) {
            $item->file_path = Storage::disk('regulation_documents')->url($item->hash_file_name);

            return $item;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($documents);
    }

    public function store(RegulationDocumentsStore $request): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('/', 'regulation_documents');
        if (!$path) {
            return $this->respondError();
        }

        $fileName = $request->file_name ?? null;
        if (empty($fileName)) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }
        $hashFileName = basename($path);
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();

        RegulationDocument::create([
            'file_name' => $fileName,
            'hash_file_name' => $hashFileName,
            'extension' => $extension,
            'size' => $size
        ]);

        return $this->respondWithSuccess();
    }

    public function destroy(RegulationDocumentsDestroy $request, RegulationDocument $regulationDocument): JsonResponse
    {
        $hashFileName = $regulationDocument->hash_file_name;

        if (isset($hashFileName)) {
            Storage::disk('regulation_documents')->delete($hashFileName);
        }

        $regulationDocument->delete();

        return $this->respondWithSuccess();
    }
}
