<?php

namespace App\Form;

use App\Form\SocioType;
use App\Entity\SociedadAnonima;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SociedadAnonimaType extends AbstractType
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
            )
            ->add(
                'domicilioLegal',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Domicilio legal',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un domicilio.',
                        'tabindex' => '5'
                    )
                )
            )
            ->add(
                'domicilioReal',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Domicilio legal',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un domicilio.',
                        'tabindex' => '5'
                    )
                )
            )
            ->add(
                'mail',
                EmailType::class,
                array(
                    'required' => true,
                    'label' => 'Correo electrónico',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un correo electrónico.',
                        'tabindex' => '5'
                    )
                )
            )
            ->add(
                'pais',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Pais',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un pais.',
                        'tabindex' => '5'
                    )
                )
            )
            ->add(
                'estado',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Estado',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba un estado.',
                        'tabindex' => '5'
                    )
                )
                    )
            ->add('archivo', FileType::class, array(
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '200M',
                        'mimeTypes' => [
                            "application/pdf",
                            "application/x-pdf",
                            "application/msword",
                            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                            "application/vnd.ms-excel",
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                            "image/png",
                            "image/jpeg",
                            "image/pjpeg",
                            "application/x-rar",
                            "application/x-rar-compressed",
                            "application/octet-stream",
                            "application/zip",
                            "application/x-zip-compressed",
                            "multipart/x-zip",
                            "text/plain",
                        ],
                        'mimeTypesMessage' => 'El tipo de archivo no es válido.',
                    ])
                ],
                'label' => 'Estatuto de conformación',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array(
                    'class' => 'form-control filestyle',
                    'data-buttonText' => "Examinar"
                )
            ))
            ->add(
                'socios',
                CollectionType::class,
                array(
                    'entry_type' => SocioType::class,
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Socios',
                    'prototype_name' => '__socio__',
                    'label_attr' => array(
                        'class' => 'hidden'
                    ),
                    'attr' => array(
                        'class' => 'hidden'
                    )
                )
            )
            ->add('submit', SubmitType::class, array(
                'label' => 'Enviar',
                'attr' => array('class' => 'btn btn-primary')
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SociedadAnonima::class,
            'adjuntos' => []
        ]);
        $resolver->setRequired('adjuntos');
    }
}
