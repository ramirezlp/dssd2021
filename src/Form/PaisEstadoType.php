<?php

namespace App\Form;

use App\Entity\PaisEstado;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Controller\SociedadAnonimaController;
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

class PaisEstadoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'pais',
            TextType::class,
            array(
                'required' => true,
                'label' => 'Pais *',
                'attr' => array(
                    'class' => 'form-control choice choice-pais d-none',
                    'data-placeholder' => '-- Elegir --',
                    'tabindex' => '5'
                )
            )
        )->add(
            'paisAux',
            ChoiceType::class,
            array(
                'choices' => $options['choices'],
                'required' => true,
                'mapped' => false,
                'label' => 'Pais *',
                'placeholder' => '-- Elegir --',
                'attr' => array(
                    'class' => 'form-control choice choice-pais',
                    'data-placeholder' => '-- Elegir --',
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
                    'class' => 'form-control choice d-none',
                    'data-placeholder' => '-- Elegir --',
                    'tabindex' => '5'
                )
            )
        )->add(
            'estadoAux',
            ChoiceType::class,
            array(
                'choices' => $options['choices'],
                'required' => false,
                'mapped' => false,
                'label' => 'Estado',
                'placeholder' => '-- Elegir --',
                'attr' => array(
                    'class' => 'form-control choice choice-estado',
                    'data-placeholder' => '-- Elegir --',
                    'tabindex' => '5'
                )
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
        $resolver->setDefaults([
            'data_class' => PaisEstado::class,
            'choices' => []
        ]);
    }
}
