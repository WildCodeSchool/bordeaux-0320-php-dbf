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
    public function transform($subjectId)
    {
        //dd($subjectId);
        if (null === $subjectId) {

            return '';
        }
        return $subjectId;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($subject)
    {
        // no issue number? It's optional, so that's ok
        if (!$subject) {
            return;
        }

        return $subject;
    }
}
