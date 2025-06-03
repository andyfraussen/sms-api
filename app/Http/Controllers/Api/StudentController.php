<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Auth\Access\AuthorizationException;

class StudentController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Student::class);

        return StudentResource::collection(
            Student::with(['schoolClass.grade','parents'])
                ->paginate(15)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Student $student): StudentResource
    {
        $this->authorize('view', $student);
        return new StudentResource($student->loadMissing(['classes','parents']));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreStudentRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', Student::class);

        $student = Student::create($request->validated());
        return (new StudentResource($student))
            ->response()
            ->setStatusCode(201);
    }
}
