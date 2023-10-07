<?php

namespace App\Form;

use App\Entity\Bed;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;




class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $room = $options['room']; // Access the passed room here

        $builder
            ->add('name')
            ->add('dateIn' ,DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThan([
                        'value' => new \DateTime('today')]), // Minimum date constraint
                ]])
            ->add('dateOut',DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Callback([$this, 'validateEndDate']),
                ]])
            ->add('bed', EntityType::class, [
                'class' => Bed::class,
                'choice_label' => 'name',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($room) {
                    return $er->createQueryBuilder('b')
                        ->where('b.room = :room')
                        ->setParameter('room', $room);
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'room' => null, // Add an option to accept the room

        ]);
    }
    public function validateEndDate($endDate, ExecutionContextInterface $context)
    {
        /** @var FormInterface $form */
        $form = $context->getRoot();
        $startDate = $form->get('dateIn')->getData();

        if ($startDate >= $endDate) {
            $context->buildViolation('The end date must be after the start date.')
                ->atPath('endReversed')
                ->addViolation();
        }
    }

}