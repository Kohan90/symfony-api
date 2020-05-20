<?php
namespace App\Service;

use App\Entity\Password;
use App\Repository\PasswordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Created by PhpStorm.
 * User: cstanciu
 * Date: 17.05.2020
 * Time: 17:04
 */
class PasswordService
{
    /**
     *
     */
    const GENERATE_REQUEST_BODY = 1;
    /**
     *
     */
    const VALIDATE_REQUEST_BODY = 2;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PasswordRepository
     */
    private $passwordRepository;

    /**
     * PasswordService constructor.
     */
    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, PasswordRepository $passwordRepository)
    {
        $this->em                   = $em;
        $this->userRepository       = $userRepository;
        $this->passwordRepository   = $passwordRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generatePassword(Request $request)
    {
        $this->checkValidBody($request, self::GENERATE_REQUEST_BODY);
        $userId = $request->request->get('user_id');
        $user   = $this->userRepository->find($userId);

        if (!$user) {
            throw new NotFoundHttpException('Invalid user!');
        }

        // If a valid password exists return that one
        $password = $this->passwordRepository->findValidPassword($userId);
        if (!$password) {
            $password = new Password();
            $password->setClient($user);
            $password->setHash($this->random_hash(32));
            $password->setCreated(new \DateTime());

            $this->em->persist($password);

            $this->em->flush();
        }

        return new JsonResponse(["password" => $password->getHash()]);
    }

    /**
     * @return JsonResponse
     */
    public function validatePassword(Request $request) : JsonResponse
    {
        $this->checkValidBody($request, self::VALIDATE_REQUEST_BODY);

        $password = $this->passwordRepository->findValidPassword($request->request->get('user_id'),
            $request->request->get('password'));

        if ($password) {
            $data = [
                'response'  => true,
                'message'   => 'Valid password!'
            ];
        } else {
            throw new BadRequestHttpException("The password for the provided user is not valid!");
        }
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     */
    private function checkValidBody(Request $request, int $request_type) : void
    {
        switch ($request_type) {
            case self::GENERATE_REQUEST_BODY:
                if (!$request->request->has("user_id")) {
                    throw new BadRequestHttpException("Body must contain user id!");
                }
                break;
            case self::VALIDATE_REQUEST_BODY:
                if (!$request->request->has("user_id") || !$request->request->has("password")) {
                    throw new BadRequestHttpException("Body must contain user id and password");
                }
                break;
        }
    }

    /**
     * @param $length
     * @return string
     */
    private function random_hash(int $length) : string
    {
        $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($str),
            0, $length);
    }
}