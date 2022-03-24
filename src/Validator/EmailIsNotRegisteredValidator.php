<?php

// src/Validator/ContainsAlphanumericValidator.php
namespace App\Validator;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class EmailIsNotRegisteredValidator extends ConstraintValidator
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailIsNotRegistered) {
            throw new UnexpectedTypeException($constraint, EmailIsNotRegistered::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $userRepository  = new UserRepository($this->managerRegistry);

        if ($userRepository->findOneBy(['email' => $value, 'isVerified' => 1])) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
