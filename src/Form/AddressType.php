<?php

namespace App\Form;
// pour créer le formulaire on fait php bin/console 
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class,[
                'label'=>'Entrer votre prenom ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label'=>'Entrer votre nom ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('company', TextType::class,[
                'label'=>'Votre société ?',
                'required'=>false,
                'attr'=>[
                    'placeholder'=> '(facultatif)Entrez le nom de votre société'
                ]
            ])
            ->add('address', TextType::class,[
                'label'=>'Quel est votre adresse ?',
                'attr'=>[
                    'placeholder'=> '8 rue des lilas'
                ]
            ])
            ->add('postal', TextType::class,[
                'label'=>'Entrer votre code postal ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('city', TextType::class,[
                'label'=>'ville ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('country', CountryType::class,[
                'label'=>'Pays  ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('phone', TelType::class,[
                'label'=>'Quel votre numero ?',
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Ajouter mon adress',
                'attr'=>[
                    'class'=>'btn-block btn-info'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'=>Address::class,
        ]);
    }
}
