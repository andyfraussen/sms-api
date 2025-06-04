<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
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

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateStudentRequest $request, Student $student): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $student);

        $student->update($request->validated());

        return (new StudentResource($student))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @throws AuthorizationException
     */
    public function delete(Student $student): \Illuminate\Http\Response
    {
        $this->authorize('delete', $student);

        $student->delete();
        return response()->noContent();
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Student $student): \Illuminate\Http\Response
    {
        $this->authorize('forceDelete', $student);   // stricter policy

        $student->forceDelete();          // hard delete
        return response()->noContent();   // 204
    }

    /**
     * @throws AuthorizationException
     */
    public function trashed(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->authorize('viewAny', Student::class); // or a dedicated ability

        $students = Student::onlyTrashed()
            ->latest('deleted_at');

        return StudentResource::collection(
            Student::with(['schoolClass.grade','parents'])
                ->paginate(15)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function restore(Student $student): \Illuminate\Http\JsonResponse
    {
        $this->authorize('restore', $student);

        $student->restore();

        return (new StudentResource($student))
            ->response()
            ->setStatusCode(200);                     // OK
    }
}
