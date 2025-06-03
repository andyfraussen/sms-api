<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Children this user cares for (parent → many students).
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,        // related
            'parent_student',      // pivot table
            'parent_id',           // FK on pivot pointing to this model
            'student_id'           // FK on pivot pointing to Student
        )
            ->withPivot('relationship')
            ->withTimestamps();
    }

    /**
     * Messages the user has sent.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Messages received (morph-to).
     */
    public function receivedMessages(): MorphMany
    {
        return $this->morphMany(Message::class, 'recipient');
    }

    public function teachingAssignments(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject_teacher', 'teacher_id', 'subject_id')->withPivot(
            ['school_class_id', 'academic_year']
        )->withTimestamps();
    }
}
