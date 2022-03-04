<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true
            ])
            ->add('description')
            ->add('is_draft', TextType::class, [
                'required' => true
            ])
            ->add('category', TextType::class, [
                'required' => true
            ])
            ->add('image', CollectionType::class, [
                'entry_type' => FileType::class,
                'mapped'     => false
            ])
            ->add('embed_code', CollectionType::class, [
                'entry_type' => TextType::class,
                'mapped'     => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
