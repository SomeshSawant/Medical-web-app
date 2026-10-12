<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CasePolicy
{
    /**
     * Determine whether the user can view any medical cases.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Viewer, and Engineer can all view cases list
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_VIEWER, User::ROLE_ENGINEER], true);
    }

    /**
     * Determine whether the user can view the specific medical case.
     */
    public function view(User $user, MedicalCase $case): bool
    {
        // Admin, Viewer, and Engineer can view case details
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_VIEWER, User::ROLE_ENGINEER], true);
    }

    /**
     * Determine whether the user can create medical cases.
     */
    public function create(User $user): Response
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_ENGINEER], true)
            ? Response::allow()
            : Response::deny('Unauthorized: Viewers cannot create cases.');
    }

    /**
     * Determine whether the user can update the medical case.
     */
    public function update(User $user, MedicalCase $case): Response
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_ENGINEER], true)
            ? Response::allow()
            : Response::deny('Unauthorized: Viewers cannot edit cases.');
    }

    /**
     * Determine whether the user can delete the medical case.
     */
    public function delete(User $user, MedicalCase $case): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Unauthorized: Only Admin users are authorized to delete medical cases.');
    }

    /**
     * Determine whether the user can change status of the medical case.
     */
    public function changeStatus(User $user, MedicalCase $case): Response
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_ENGINEER], true)
            ? Response::allow()
            : Response::deny('Unauthorized: Viewers cannot change case status.');
    }
}
