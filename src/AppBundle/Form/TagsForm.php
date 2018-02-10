<?php

namespace AppBundle\Form;

use AppBundle\Entity\News;
use AppBundle\Repository\NewsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('news', EntityType::class, [
                'class' => News::class,
                'choice_label' => 'name',
                'query_builder' => function(NewsRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                array($this, 'onPostSetData')
            )
        ;
    }

    public function onPostSetData(FormEvent $event)
    {
        if ($event->getData() && $event->getData()->getId()) {
            $form = $event->getForm();
            unset($form['news']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class
        ]);
    }


}