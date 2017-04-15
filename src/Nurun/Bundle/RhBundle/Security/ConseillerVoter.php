<?php
namespace Nurun\Bundle\RhBundle\Security;

use Nurun\Bundle\RhBundle\Entity\Conseiller;
use Nurun\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConseillerVoter extends Voter
{    
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Conseiller;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // *********** Il faut regarder si on a des roles qui ont le droit de tout faire sur un conseiller **********
        
        foreach ($token->getRoles() as $role) {
            if (in_array($role->getRole(), ['ROLE_ROLE_GESTION', 'ROLE_ADMIN'])) {
                return true;
            }
        }

        //Logique des actions à faire
        $conseiller = $subject;        

        if($attribute == "changePhoto"){
            //on récupère toutes les fonctions du conseiller
            $conseillerFonctionList = $conseiller->getConseillerFonctions();

            $fonctionList = array();
            foreach ($conseillerFonctionList as $conseillerFonction) {
                $fonctionList[] = $conseillerFonction->getFonction();
            }

            //Maintenant qu'on a toutes les fonctions, regardons si une d'elle possède la permission
            foreach ($fonctionList as $fonction) {
                $fonctionPermissionList = $fonction->getFonctionPermissions();

                foreach ($fonctionPermissionList as $fonctionPermission) {
                    $permission = $fonctionPermission->getPermission();

                    if($permission->getName() == $attribute){
                        return true;
                    }
                }
            }
        }
        return false;
    }
}