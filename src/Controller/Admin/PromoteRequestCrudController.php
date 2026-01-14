<?php

namespace App\Controller\Admin;

use App\Entity\Promote;
use App\Entity\PromoteRequest;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PromoteRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PromoteRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de promotion')
            ->setEntityLabelInPlural('Demandes de promotion')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('event', 'Événement'),
            TextField::new('event.createdBy.email', 'Demandeur')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    $user = $entity->getEvent()->getCreatedBy();
                    return $user->getFirstName() . ' ' . $user->getLastName();
                }),
            TextField::new('formulaLabel', 'Formule')->hideOnForm(),
            DateField::new('dateStart', 'Début'),
            DateField::new('dateEnd', 'Fin'),
            MoneyField::new('totalPrice', 'Prix')->setCurrency('EUR')->setStoredAsCents(false),
            ChoiceField::new('status', 'Statut')->setChoices([
                'En attente' => PromoteRequest::STATUS_PENDING,
                'Approuvée' => PromoteRequest::STATUS_APPROVED,
                'Refusée' => PromoteRequest::STATUS_REJECTED,
            ]),
            TextareaField::new('adminNote', 'Note admin')->hideOnIndex(),
            DateTimeField::new('createdAt', 'Demandé le')->hideOnForm(),
            DateTimeField::new('processedAt', 'Traité le')->hideOnForm(),
        ];
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        /** @var PromoteRequest $entityInstance */

        if ($entityInstance->getStatus() === PromoteRequest::STATUS_APPROVED) {
            $event = $entityInstance->getEvent();

            if (!$event->getPromote()) {
                $promote = new Promote();
                $promote->setEvent($event);
                $promote->setDateStart(\DateTimeImmutable::createFromInterface($entityInstance->getDateStart()));
                $promote->setDateEnd(\DateTimeImmutable::createFromInterface($entityInstance->getDateEnd()));
                $promote->setPrice($entityInstance->getTotalPrice());

                $em->persist($promote);
            }

            $entityInstance->setProcessedAt(new \DateTimeImmutable());
        } elseif ($entityInstance->getStatus() === PromoteRequest::STATUS_REJECTED) {
            $entityInstance->setProcessedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($em, $entityInstance);
    }
}
