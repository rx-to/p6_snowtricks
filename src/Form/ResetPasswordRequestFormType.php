<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'error_bubbling' => true,
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse email.'
                    ]),
                    new Length([
                        'min'        => 5,
                        'minMessage' => 'Votre adresse eemail est trop courte.',
                        'max'        => 255,
                        'maxMessage' => 'Votre adresse email ne peut pas excéder 255 caractères.'
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
