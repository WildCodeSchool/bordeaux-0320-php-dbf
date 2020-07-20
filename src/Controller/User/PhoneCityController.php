<?php

namespace App\Controller\User;

use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/phoneCity")
 */
class PhoneCityController extends AbstractController
{
    /**
     * @Route("/getId", name="get_phone_city_id", methods={"GET"})
     * @return JsonResponse
     */
    public function getPhoneCityId(CityRepository $cityRepository): JsonResponse
    {
        $phoneCity = $cityRepository->findOneByName('Cellule Téléphonique');
        $phoneCityId = $phoneCity->getId();
        $data =['phoneCityId' => $phoneCityId];
        $response = new JsonResponse();
        $status = ($phoneCityId) ? JsonResponse::HTTP_OK : JsonResponse::HTTP_NO_CONTENT;
        $response->setData($data);
        $response->setStatusCode($status);
        return $response;
    }

    /**
     * @Route("/random", name="user_random", methods={"GET"})
     */
    public function randomCellUser(UserRepository $userRepository)
    {
        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->setData([
            'recipientId' => $userRepository->getRandomUser()->getId()
        ]);
        return $response;
    }
}
