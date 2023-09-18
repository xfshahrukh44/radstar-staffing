<?php

namespace App\Repositories\Users\Auth;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Users\Auth\UserRepository;
use App\User;
use App\Validators\Users\Auth\UserValidator;
use Illuminate\Support\Facades\Auth;


/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories\Users\Auth;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}