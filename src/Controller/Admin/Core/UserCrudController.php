<?php

namespace App\Controller\Admin\Core;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('User information')->hideOnForm();
        yield FormField::addTab('User information')->hideOnDetail();
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('username')->setColumns(4);
        yield EmailField::new('email')->setColumns(4);
        yield TextField::new('name')->setColumns(4);

        yield FormField::addPanel('Security')->hideOnForm();
        yield FormField::addTab('Security')->hideOnDetail();
        yield TextField::new('password')
            ->setHelp('Restore the password associated with this user.')
            ->setFormType(PasswordType::class)
            ->setColumns(6)
            ->hideOnIndex();
        yield ChoiceField::new('roles')
            ->setChoices(User::USER_ROLES)
            ->allowMultipleChoices()
            ->setColumns(6);
    }
}
