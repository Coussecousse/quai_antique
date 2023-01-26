<?php

namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QueryValidatorDate extends ConstraintValidator 
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    public function validate($value, Constraint $constraint) {

        $day = date('D', $value);
        switch($day) {
            case 'Sun':
                $day = 0;
                break;
            case 'Mon':
                $day = 1;
                break;
            case 'Tue':
                $day = 2;
                break;
            case 'Wed':
                $day = 3;
                break;
            case 'Thu':
                $day = 4;
                break;
            case 'Fri':
                $day = 5;
                break;
            case 'Sat':
                $day = 6;
                break;
        };

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('d')
            ->where('d.day = :day')
            ->setParameter('day', $day);

        $daySelected = $queryBuilder->getQuery()->getSingleScalarResult();
        if ($daySelected) {
            $this->context->buildViolation($constraint->CLOSE)
                ->addViolation();
        }
        
    }
}