<?php

namespace App\Form;

use App\Entity\Film;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
     $builder
    ->add('title', TextType::class)
    ->add('description', TextareaType::class)
    ->add('director', TextType::class)
    ->add('trailerFile', FileType::class, [
        'label' => 'Trailer (MP4 file)',
        'mapped' => false,
        'required' => false,
        'constraints' => [
            new File([
                'maxSize' => '50M',
                'mimeTypes' => ['video/mp4', 'video/mpeg', 'video/quicktime'],
                'mimeTypesMessage' => 'Please upload a valid video file (MP4, MPEG, MOV)',
            ])
        ],
    ])
    ->add('imageFile', FileType::class, [
        'label' => 'Image (JPG/PNG)',
        'mapped' => false,
        'required' => false,
        'constraints' => [
            new File([
                'maxSize' => '5M',
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'mimeTypesMessage' => 'Please upload a valid image file (JPG, PNG, WEBP)',
            ])
        ],
    ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
