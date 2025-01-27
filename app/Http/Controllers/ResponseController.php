<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use App\Models\Response;
use App\Models\Survey;
use App\Traits\HasBreadcrumbs;

use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    use HasBreadcrumbs;

    /**
     * Show the form for creating a new resource.
     * @deprecated
     */
    public function create(Collector $collector)
    {
        abort_if(!$collector->getKey(), 404);

        abort_if($collector->status != 'open', 404);

        $record = $collector->responses()->create([
            'remote_addr' => request()->ip(),
            'user_id' => request()->user()->id,
            'survey_id' => $collector->survey_id,
            'data' => []
        ]);

        return response()->json([
            'id' => $record->uuid
        ]);
    }

    public function update(Response $record) {

        abort_if(!$record->getKey(), 404);

        abort_if(!$record->survey, 404);

        abort_if($record->survey->status == 'closed', 404);

        $validator = Validator::make(request()->all(), [
            'data' => [
                'required',
                'array'
            ],
            'data.*' => [
                'sometimes'
            ]
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors()
            ], 422);
        }

        // Retrieve the validated input...
        $data = $validator->validated();

        $record->update([
            'data' => $data
        ]);

        /**
         * Add in notificaiton.
         */

        return response()->json([
            'status' => 'success'
        ]);
    }

}
