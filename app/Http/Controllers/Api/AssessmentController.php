<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\Assessment;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AssessmentResource::collection(
            Assessment::with(['student', 'subject', 'gradedBy'])->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssessmentRequest $request)
    {
        $this->authorize('create', Assessment::class);

        $assessment = Assessment::create($request->validated());
        return (new AssessmentResource($assessment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assessment $assessment)
    {
        return new AssessmentResource($assessment->loadMissing(['student', 'subject']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssessmentRequest $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $assessment->update($request->validated());

        return (new AssessmentResource($assessment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assessment $assessment)
    {
        //
    }
}
