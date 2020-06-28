<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Proposition;

use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PropositionType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label'=> false,
                'attr' => [
                    'placeholder' => 'Intitulé'
                ]
            ])
            ->add('description',CKEditorType::class,[
                'label' => false
            ])
            ->add('poste',TextType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Poste'
                ]
            ])
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'label' => false,
                'choice_label' => function ($category) {
                    return $category->getName();
                }
            ])
/*            ->add('author', HiddenType::class,[
                'data' =>  $security = $this->security->getUser(),
                'attr' => [
                    'class' => 'hidden'
                ]
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proposition::class,
        ]);
    }
}
