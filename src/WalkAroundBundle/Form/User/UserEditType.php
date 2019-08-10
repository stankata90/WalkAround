<?php

namespace WalkAroundBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WalkAroundBundle\Entity\User;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('fullName', TextType::class)
            ->add('password', RepeatedType::class, array(
                'data_class' => null,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Password'],
            ))
            ->add('age', NumberType::class)
            ->add('sex', ChoiceType::class, [

                'choices' => ['Male' => 'male', 'Female' => 'female'],
            ])
            ->add('image', FileType::class,
                [
                    'data_class' => null
                    ]
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'user';
    }
}
