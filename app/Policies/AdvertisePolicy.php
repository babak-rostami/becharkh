<?php

namespace App\Policies;

use App\Models\Advertise;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertisePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Advertise $advertise
     * @return mixed
     */
    public function view(User $user, Advertise $advertise)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Advertise $advertise
     * @return mixed
     */
    public function update(User $user, Advertise $advertise)
    {
        return $advertise->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Advertise $advertise
     * @return mixed
     */
    public function delete(User $user, Advertise $advertise)
    {
        return $advertise->user_id == $user->id;
    }


    public function addImage(User $user, Advertise $advertise)
    {
        return $advertise->user_id == $user->id;
    }

    public function deleteImage(User $user, Advertise $advertise)
    {
        return $advertise->user_id == $user->id;
    }


    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Advertise $advertise
     * @return mixed
     */
    public function restore(User $user, Advertise $advertise)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Advertise $advertise
     * @return mixed
     */
    public function forceDelete(User $user, Advertise $advertise)
    {
        //
    }

}
