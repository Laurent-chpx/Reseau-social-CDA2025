<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use function Sodium\add;


class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Événement')
            ->setEntityLabelInPlural('Événements')
            ->setSearchFields(['title', 'description', 'address'])
            ->setDefaultSort(['dateStart' => 'DESC']);
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
            TextField::new('title', 'Titre'),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            TextField::new('address', 'Adresse'),
            DateTimeField::new('dateStart', 'Date début'),
            DateTimeField::new('dateEnd', 'Date fin'),
            TextField::new('schedule', 'Horaires')->hideOnIndex(),
            MoneyField::new('price', 'Prix')->setCurrency('EUR')->setStoredAsCents(false),
            AssociationField::new('city', 'Ville'),
            AssociationField::new('categories', 'Catégories'),
            AssociationField::new('createdBy', 'Créé par')->hideOnForm(),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }
}
