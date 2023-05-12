<?php

namespace App\Form;

use App\Entity\Animal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;



class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('codigo', TextType::class, [
                'label' => 'Código',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, insira um código.'
                    ])
                ]
            ])
            ->add('nascimento', DateType::class, [
                'label' => 'Data de nascimento',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Assert\LessThan([
                        'value' => 'today',
                        'message' => 'A data de nascimento não pode estar no futuro.'
                    ])
                ]
            ])
            ->add('peso', NumberType::class, [
                'label' => 'Peso (kg)',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+(\.\d{1,3})?$/',
                        'message' => 'O valor do peso deve ser numérico'
                    ])
                ]
            ])
            ->add('racao', NumberType::class, [
                'label' => 'Quantidade de Ração (kg)',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+(\.\d{1,3})?$/',
                        'message' => 'O valor do peso deve ser numérico'
                    ])
                ]
            ])
            ->add('leite', NumberType::class, [
                'label' => 'Quantidade de Leite (litros)',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+(\.\d{1,3})?$/',
                        'message' => 'O valor do peso deve ser numérico'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
