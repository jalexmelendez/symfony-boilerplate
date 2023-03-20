<?php

namespace App\Controller\Admin\Core;

use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Team Information')->hideOnForm();
        yield FormField::addTab('Team information')->hideOnDetail();
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextareaField::new('description');

        yield FormField::addPanel('Team Members')->hideOnForm();
        yield FormField::addTab('Team Members')->hideOnDetail();
        yield AssociationField::new('owner', 'Owner');
        yield CollectionField::new('teamMemberships', 'Team Members')
            ->useEntryCrudForm();
    }
}
