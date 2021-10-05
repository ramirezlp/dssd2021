<?php

namespace App\Form;

use App\Entity\Socio;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class SocioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add(
                'nombre',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Nombre',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un nombre.',
                        'tabindex' => '5'
                    )
                )
            )->add(
                'apellido',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Apellido',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un apellido.',
                        'tabindex' => '5'
                    )
                )
            )->add(
                'esRepresentanteLegal',
                ChoiceType::class,
                array(
                    'choices' => [
                        'Si' => true,
                        'No' => false
                    ],
                    'required' => false,
                    'label' => '¿Representante legal?',
                    'placeholder' => '-- Elegir --',
                    'attr' => array(
                        'class' => 'form-control choice',
                        'data-placeholder' => '-- Elegir --',
                        'tabindex' => '5'
                    )
                )
            )->add(
                'porcentaje',
                IntegerType::class,
                array(
                    'required' => true,
                    'label' => 'Porcentaje de aportes',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un porcentaje.',
                        'tabindex' => '5'
                    )
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Socio::class,
            'adjuntos' => []
        ]);
        $resolver->setRequired('adjuntos');
    }
}
