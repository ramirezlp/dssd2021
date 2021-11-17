<?php

namespace App\Form;

use App\Entity\Socio;
use App\Form\SocioType;
use Doctrine\ORM\EntityRepository;
use App\Entity\SociedadAnonimaSocio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SociedadAnonimaSocioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add(
                'socio',
                SocioType::class,
                array()
            )->add(
                'esRepresentanteLegal',
                ChoiceType::class,
                array(
                    'choices' => [
                        'Si' => true,
                        'No' => false
                    ],
                    'required' => true,
                    'label' => '¿Representante legal? *',
                    'placeholder' => '-- Elegir --',
                    'attr' => array(
                        'class' => 'form-control choice select-representante',
                        'data-placeholder' => '-- Elegir --',
                        'tabindex' => '5'
                    )
                )
            )->add(
                'porcentajeAporte',
                IntegerType::class,
                array(
                    'required' => true,
                    'label' => 'Porcentaje de aportes *',
                    'attr' => array(
                        'class' => 'form-control porcentaje',
                        'placeholder' => 'Escriba un porcentaje.',
                        'tabindex' => '5',
                        'min' => '0',
                        'max' => '100',
                    )
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SociedadAnonimaSocio::class,
        ]);
    }
}
