<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->authorize('viewAny', School::class);               // <- policy
        return SchoolResource::collection(
            School::with('grades')->paginate(10)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function show(School $school): SchoolResource
    {
        $this->authorize('view', $school);
        return new SchoolResource($school->load('grades.subjects'));
    }
}
