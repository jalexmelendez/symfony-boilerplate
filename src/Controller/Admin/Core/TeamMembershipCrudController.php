<?php

namespace App\Controller\Admin\Core;

use App\Entity\TeamMembership;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class TeamMembershipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TeamMembership::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('user');
        yield ChoiceField::new('role')
            ->setChoices(TeamMembership::TEAM_ROLES);
        yield AssociationField::new('team');
    }
}
