<?php


namespace App\Form\Transformers;


use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SubjectTransformer implements DataTransformerInterface
{

    private $subjectRepository;

    public function __construct(SubjectRepository $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @inheritDoc
     */
    public function transform($subject)
    {

        if (null === $subject) {

            return '';
        }
        return $subject->getId();
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($subjectId)
    {
        // no issue number? It's optional, so that's ok
        if (!$subjectId) {
            return;
        }
        $subjectId = (int)$subjectId;
        $subject = $this->subjectRepository->findOneById($subjectId);

        if (null === $subject) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'Aucun motif ne semble exister pour l\'ID n° "%s"',
                $subjectId
            ));
        }
        return $subject;
    }
}
