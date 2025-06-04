<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Auth\Access\AuthorizationException;

class AttendanceController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->authorize('viewAny', Attendance::class);

        $query = Attendance::with(['student.schoolClass.grade'])
            ->when(request('date'), fn ($q, $date) => $q->whereDate('date', $date));

        return AttendanceResource::collection($query->paginate(20));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreAttendanceRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', Attendance::class);

        $attendance = Attendance::create($request->validated() + [
                'recorded_by' => $request->user()->id,
            ]);

        return (new AttendanceResource($attendance->load('student')))
            ->response()
            ->setStatusCode(201);
    }
}
