<?php


namespace App\Service\Landing;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Retardator extends AbstractController
{

    public function redirectToRoutewithDelay(string $route, int $delay, array $parameters = [], int $status = 302): RedirectResponse
    {
        sleep($delay);
        return $this->redirectToRoute($route, $parameters, $status);
    }

}
