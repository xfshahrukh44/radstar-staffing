<?php

namespace App\Http\Controllers\Api\Company\Auth;

use App\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompanyApiRegisterFormRequest;
use App\Http\Requests\Front\CompanyFrontRegisterFormRequest;
use App\Repositories\Companies\Auth\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OA\PathItem;


class RegisterController extends Controller
{

    /**
     * @OA\PathItem(path="/company")
     */


    protected $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }
    /**
     * @OA\Post(
     *     path="/company/register",
     *     summary="register for company",
     *     tags={"Company"},
     *     requestBody={
     *         "description": "User register credentials",
     *         "required": true,
     *         "content": {
     *             "application/json": {
     *                 "schema": {
     *                     "type": "object",
     *                     "properties": {
     *                         "name": {
     *                             "type": "string",
     *                             "example": "John",
     *                         },
     *                         "last_name": {
     *                             "type": "string",
     *                             "example": "Smith",
     *                         },
     *                         "email": {
     *                             "type": "string",
     *                             "example": "john@gmail.com",
     *                         },
     *                         "password": {
     *                             "type": "string",
     *                             "example": "12345678",
     *                         },
     *                     },
     *                 },
     *             },
     *         },
     *     },
     *     responses={
     *         @OA\Response(
     *             response=200,
     *             description="OK",
     *             @OA\JsonContent(
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean",
     *                     example=true,
     *                     description="A boolean value."
     *                 ),
     *             ),
     *         ),
     *     },
     * )
     */

    public function register(CompanyApiRegisterFormRequest $request)
    {
        try {
            $password = Hash::make($request->input('password'));
            $array = [
                'name' => $request->input('name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => $password,
                // Add other fields and their values as needed
            ];
            $company = $this->companyRepository->create($array);
            $token = $company->createToken('MyApp')->accessToken;

            return APIResponse::success(
                'Company Register Successfully',
                compact('company', 'token')
            );
        } catch (\Exception $e) {
            return APIResponse::error($e->getMessage());
        }
    }
}
