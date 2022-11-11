<?php

namespace Syngenta\DataImport\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Syngenta\DataImport\Importer;

class UploadController
{
    use Importable;

    public function handle(NovaRequest $request)
    {
        $data = Validator::make($request->all(), [
            'file' => 'required|file',
        ])->validate();

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            (new Importer)->toCollection($file, null);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'Sorry, we could not import that file'], 422);
        }

        // Store the file temporarily
        $hash = File::hash($file->getRealPath()).".".$extension;

        $file->move(storage_path('nova/data-import/tmp'), $hash);

        return response()->json(['result' => 'success', 'file' => $hash]);
    }
}
