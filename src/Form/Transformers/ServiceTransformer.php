<?php


namespace App\Form\Transformers;


use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ServiceTransformer implements DataTransformerInterface
{

    private $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @inheritDoc
     */
    public function transform($service)
    {

        if (null === $service) {

            return '';
        }
        return $service->getId();
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($serviceId)
    {
        // no issue number? It's optional, so that's ok
        if (!$serviceId) {
            return;
        }
        $serviceId = (int)$serviceId;
        $service = $this->serviceRepository->findOneById($serviceId);

        if (null === $service) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'Aucun service ne semble exister pour l\'ID n° "%s"',
                $serviceId
            ));
        }
        return $service;
    }
}
