<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date','date')
            ->add('title', 'text')
            ->add('author','text')
            ->add('content', 'textarea')
            ->add('published', 'checkbox', array('required' => false))
            ->add('image',      new ImageType())
            /*
   * Rappel :
   ** - 1er argument : nom du champ, ici « categories », car c'est le nom de l'attribut
   ** - 2e argument : type du champ, ici « collection » qui est une liste de quelque chose
   ** - 3e argument : tableau d'options du champ
   */
    /*        ->add('categories', 'collection', array(
                'type'         => new CategoryType(),
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('save',      'submit')
        ;*/
        /* Avec un type de champs entity */
            ->add('categories', 'entity', array(
                    'class'    => 'OCPlatformBundle:Category',
                    'property' => 'name',
                    'multiple' => true
            ));
        /* autres options disponibles expanded (false=select ou true=radio) et multiple (false=select ou true=checkbox)*/
/* pour n'afficher que les titres d'article déjà publiées avec une requete au préalable
            ->add('advert', 'entity', array(
            'class'         => 'OCPlatformBundle:Advert',
            'property'      => 'title',
            'query_builder' => function(AdvertRepository $repo) {
                return $repo->getPublishedQueryBuilder();
            }
        ;*/
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }

}


