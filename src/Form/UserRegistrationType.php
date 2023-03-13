<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    protected $formAttributes = [
        'attr' => [
            'class' => 'form-control'
        ],
        /*'label' => [
            'class' => 'form-label'
        ],*/
        ];
    
    protected $submitButtonAttributes = [
        'attr' => [
            'class' => 'btn btn-primary btn-block mb-3 mt-3',
        ],
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, $this->formAttributes)
            ->add('email', EmailType::class, $this->formAttributes)
            ->add('name', TextType::class, $this->formAttributes)
            //->add('roles', JsonType::class)
            ->add('password', PasswordType::class, $this->formAttributes)
            ->add('Registrarme', SubmitType::class, $this->submitButtonAttributes)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
