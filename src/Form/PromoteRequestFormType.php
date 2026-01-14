<?php

namespace App\Form;

use App\Entity\PromoteRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class PromoteRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formula', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une formule']),
                ],
            ])
            ->add('dateStart', DateType::class, [
                'label' => 'Date de début de la promotion',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une date de début']),
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de début doit être aujourd\'hui ou dans le futur',
                    ]),
                ],
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PromoteRequest::class,
        ]);
    }
}
