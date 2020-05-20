<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
//Services
use App\Service\PasswordService;

/**
 * Created by PhpStorm.
 * User: cstanciu
 * Date: 17.05.2020
 * Time: 16:55
 */
/**
 * @Route("/password")
 */
class PasswordController
{
    /**
     * @var PasswordService
     */
    protected $passwordService;

    /**
     * PasswordController constructor.
     */
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * Generate OTP for the given user id.
     *
     *
     * @Route("/generate", name="generate_otp", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the generated password.",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="password", type="string"),
     *     )
     * )
     * @SWG\Parameter(
     *     name="User ID",
     *     in="body",
     *     type="integer",
     *     description="The id of the user.",
     *     required=true,
     *         @SWG\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     * )
     * @SWG\Tag(name="Generation")
     */
    public function generatePasswordAction(Request $request)
    {
        return $this->passwordService->generatePassword($request);
    }

    /**
     * Validate OTP.
     *
     *
     * @Route("/validate", name="validate_otp", methods={"PUT"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the generated password.",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="response", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Parameter(
     *     name="User ID",
     *     in="body",
     *     type="integer",
     *     required=true,
     *     @SWG\Schema(
     *             type="integer",
     *             format="int64",
     *     )
     * )
     * @SWG\Parameter(
     *     name="Password",
     *     in="body",
     *     type="string",
     *     required=true,
     *     description="The generated password.",
     *     @SWG\Schema(
     *             type="integer",
     *             format="int64",
     *     )
     * )
     * @SWG\Tag(name="Validation")
     */
    public function validatePasswordAction(Request $request)
    {
        return $this->passwordService->validatePassword($request);
    }
}