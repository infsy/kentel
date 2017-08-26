<?php

namespace Nurun\Bundle\SystemBundle\Controller;

use Ivory\GoogleMap\Service\Geocoder\GeocoderService;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;

use Nurun\Bundle\RhBundle\Entity\Adresse;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\UserBundle\Entity\User;
//use Nurun\Bundle\RhBundle\Entity\Conseiller;
//use Nurun\Bundle\RhBundle\Entity\ConseillerMandat;
//use Nurun\Bundle\RhBundle\Form\ConseillerType;
//use Nurun\Bundle\RhBundle\Entity\Document;
use Nurun\Bundle\RhBundle\Entity\Competence;
use Nurun\Bundle\RhBundle\Entity\Certification;
use Nurun\Bundle\RhBundle\Entity\Language;
use Nurun\Bundle\RhBundle\Entity\StatutAffectation;
use Nurun\Bundle\RhBundle\Entity\TypeCompetence;
use Nurun\Bundle\RhBundle\Entity\Fonction;
use Nurun\Bundle\RhBundle\Entity\FonctionPermission;
use Nurun\Bundle\RhBundle\Entity\UserNotification;
use Nurun\Bundle\RhBundle\Entity\Action;
use Nurun\Bundle\RhBundle\Entity\UserFonction;
//use Nurun\Bundle\RhBundle\Form\ConseillerMandatCongeType;
use Nurun\Bundle\RhBundle\Form\CompetenceType;
use Nurun\Bundle\RhBundle\Form\CertificationType;
use Nurun\Bundle\RhBundle\Form\AdresseType;
use Nurun\Bundle\RhBundle\Form\FonctionType;
use Nurun\Bundle\RhBundle\Form\TypeCompetenceType;
use Nurun\Bundle\RhBundle\Form\ActionType;
use Nurun\Bundle\RhBundle\Form\LanguageType;
use Nurun\Bundle\RhBundle\Form\StatutAffectationType;
use Nurun\Bundle\UserBundle\Form\UserRoleType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class AdminController extends Controller
{
    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function usersAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('NurunUserBundle:User')->findAll();

        return $this->render('NurunSystemBundle:Admin:users.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function editUserRoleAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new UserRoleType(), $user);
        $form->handleRequest($request);

        if($form->isValid()&& $form->isSubmitted()){

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_users');
        }

        return $this->render('NurunSystemBundle:Admin:editUserRole.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function removeUserAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('NurunUserBundle:User')->findOneBy(array('id'=>$id));

        if($user){

            $permissions = $user->getPermissions();
            $em->remove($user);
            if ($permissions) {
                $em->remove($permissions);
            }
            $em->flush();
        }

        return $this->redirectToRoute('nurun_admin_users');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function fonctionsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $fonctionList = $em->getRepository('NurunRhBundle:Fonction')->findAll();

        $fonction = new Fonction();
        $form = $this->createForm(new FonctionType(), $fonction);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($fonction);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_fonctions');
        }

        return $this->render('NurunSystemBundle:Admin:fonctions.html.twig', array(
            'fonctionList'  => $fonctionList,
            'form'          => $form->createView()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     */
    public function editFonctionAction(Request $request, Fonction $fonction)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new FonctionType(), $fonction);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($fonction);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_fonctions');
        }

        return $this->render('NurunSystemBundle:Admin:editFonction.html.twig', array(
            'form'      => $form->createView(),
            'fonction'  => $fonction
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     */
    public function removeFonctionAction(Request $request, Fonction $fonction)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($fonction);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_fonctions');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     */
    public function editFonctionPermissionsAction(Request $request, Fonction $fonction)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $actionList = $em->getRepository('NurunRhBundle:Action')->findAll();

        return $this->render('NurunSystemBundle:Admin:editFonctionPermissions.html.twig', array(
            'actionList'    => $actionList,
            'fonction'      => $fonction
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("action", options={"mapping": {"actionId": "id"}})
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     */
    public function fonctionAddPermissionAction(Action $action, Fonction $fonction)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $fonctionPermission = new FonctionPermission();
        $fonctionPermission->setFonction($fonction);
        $fonctionPermission->setAction($action);

        $em->persist($fonctionPermission);
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_admin_fonction_permissions', array('fonctionId'=> $fonction->getId())));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("action", options={"mapping": {"actionId": "id"}})
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     */
    public function fonctionRemovePermissionAction(Action $action, Fonction $fonction)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $fonctionPermissionList = $em->getRepository('NurunRhBundle:FonctionPermission')->getByFonctionAndAction($fonction, $action);

        //À noter que normalement, il devrait en avoir qu'un seul
        foreach ($fonctionPermissionList as $fonctionPermission) {
            $em->remove($fonctionPermission);
        }

        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_admin_fonction_permissions', array('fonctionId'=> $fonction->getId())));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function competencesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $competenceList = $em->getRepository('NurunRhBundle:Competence')->findAll();

        $competence = new Competence();
        $form = $this->createForm(new CompetenceType(), $competence);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($competence);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_competences');
        }

        return $this->render('NurunSystemBundle:Admin:competences.html.twig', array(
            'competenceList'    => $competenceList,
            'form'              => $form->createView()
            )
        );
    }


    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function adressesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $adresseList = $em->getRepository('NurunRhBundle:Adresse')->findAll();

        $adresse = new Adresse();
        $form = $this->createForm(new AdresseType(), $adresse);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            // construction du geocoder
            $geocoder = new GeocoderService(new Client(), new GuzzleMessageFactory());

            $adresseString = $adresse->getNumeroAdresse() . ' ' .
                $adresse->getLigne1Adresse() . ', ' .
                $adresse->getVille() . ', CA';
            $request = new GeocoderAddressRequest($adresseString);
            $result = $geocoder->geocode($request);
            $geometryArray = $result->getResults();
            $geometryObject = $geometryArray[0];
            $adresse->setLatitude($geometryObject->getGeometry()->getLocation()->getLatitude());
            $adresse->setLongitude($geometryObject->getGeometry()->getLocation()->getLongitude());

            $em->persist($adresse);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_adresses');
        }

        return $this->render('NurunSystemBundle:Admin:adresses.html.twig', array(
                'adresseList'    => $adresseList,
                'form'              => $form->createView()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("adresse", options={"mapping": {"adresseId": "id"}})
     */
    public function editAdresseAction(Request $request, Adresse $adresse)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new AdresseType(), $adresse);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($adresse);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_adresses');
        }

        return $this->render('NurunSystemBundle:Admin:editAdresse.html.twig', array(
                'form'          => $form->createView(),
                'adresse'    => $adresse
            )
        );
    }


    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("adresse", options={"mapping": {"adresseId": "id"}})
     */
    public function removeAdresseAction(Request $request, Adresse $adresse)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($adresse);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_adresses');
    }


        /**
         * @Security("has_role('ROLE_ROOT')")
         */
        public function certificationsAction(Request $request)
        {
            $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

            $em = $this->getDoctrine()->getManager();
            $certificationList = $em->getRepository('NurunRhBundle:Certification')->findAll();

            $certification = new Certification();
            $form = $this->createForm(new CertificationType(), $certification);
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {
                $em->persist($certification);
                $em->flush();

                return $this->redirectToRoute('nurun_admin_certifications');
            }

            return $this->render('NurunSystemBundle:Admin:certifications.html.twig', array(
                'certificationList'    => $certificationList,
                'form'              => $form->createView()
                )
            );
        }

        /**
         * @Security("has_role('ROLE_ROOT')")
         * @ParamConverter("certification", options={"mapping": {"certificationId": "id"}})
         */
        public function editCertificationAction(Request $request, Certification $certification)
        {
            $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
            $em = $this->getDoctrine()->getManager();

            $form = $this->createForm(new CertificationType(), $certification);
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {
                $em->persist($certification);
                $em->flush();

                return $this->redirectToRoute('nurun_admin_certifications');
            }

            return $this->render('NurunSystemBundle:Admin:editCertification.html.twig', array(
                'form'          => $form->createView(),
                'certification'    => $certification
                )
            );
        }

            /**
             * @Security("has_role('ROLE_ROOT')")
             * @ParamConverter("certification", options={"mapping": {"certificationId": "id"}})
             */
            public function removeCertificationAction(Request $request, Certification $certification)
            {
                $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
                $em = $this->getDoctrine()->getManager();

                $em->remove($certification);
                $em->flush();

                return $this->redirectToRoute('nurun_admin_certifications');
            }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("competence", options={"mapping": {"competenceId": "id"}})
     */
    public function editCompetenceAction(Request $request, Competence $competence)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CompetenceType(), $competence);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($competence);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_competences');
        }

        return $this->render('NurunSystemBundle:Admin:editCompetence.html.twig', array(
            'form'          => $form->createView(),
            'competence'    => $competence
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("competence", options={"mapping": {"competenceId": "id"}})
     */
    public function removeCompetenceAction(Request $request, Competence $competence)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($competence);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_competences');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function typeCompetencesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $typeCompetenceList = $em->getRepository('NurunRhBundle:TypeCompetence')->findAll();

        $typeCompetence = new TypeCompetence();
        $form = $this->createForm(new TypeCompetenceType(), $typeCompetence);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($typeCompetence);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_typeCompetences');
        }

        return $this->render('NurunSystemBundle:Admin:typeCompetences.html.twig', array(
            'typeCompetenceList'    => $typeCompetenceList,
            'form'                  => $form->createView()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("typeCompetence", options={"mapping": {"typeCompetenceId": "id"}})
     */
    public function editTypeCompetenceAction(Request $request, TypeCompetence $typeCompetence)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new TypeCompetenceType(), $typeCompetence);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($typeCompetence);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_typeCompetences');
        }

        return $this->render('NurunSystemBundle:Admin:editTypeCompetence.html.twig', array(
            'form'              => $form->createView(),
            'typeCompetence'    => $typeCompetence
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("typeCompetence", options={"mapping": {"typeCompetenceId": "id"}})
     */
    public function removeTypeCompetenceAction(Request $request, TypeCompetence $typeCompetence)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($typeCompetence);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_typeCompetences');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function userNotificationsAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $actionList = $em->getRepository('NurunRhBundle:Action')->findAll();

        return $this->render('NurunSystemBundle:Admin:userNotifications.html.twig', array(
            'actionList'    => $actionList,
            'user'          => $user
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("action", options={"mapping": {"actionId": "id"}})
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function userAddNotificationAction(Action $action, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $userNotification = new UserNotification();
        $userNotification->setUser($user);
        $userNotification->setAction($action);

        $em->persist($userNotification);
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_admin_user_notifications', array('userId'=> $user->getId())));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("action", options={"mapping": {"actionId": "id"}})
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function userRemoveNotificationAction(Action $action, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $userNotificationList = $em->getRepository('NurunRhBundle:UserNotification')->getByUserAndAction($user, $action);

        //À noter que normalement, il devrait en avoir qu'un seul
        foreach ($userNotificationList as $userNotification) {
            $em->remove($userNotification);
        }

        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_admin_user_notifications', array('userId'=> $user->getId())));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function statutAffectationsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $statutAffectationList = $em->getRepository('NurunRhBundle:StatutAffectation')->findAll();

        $statutAffectation = new StatutAffectation();
        $form = $this->createForm(new StatutAffectationType(), $statutAffectation);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($statutAffectation);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_statutAffectations');
        }

        return $this->render('NurunSystemBundle:Admin:statutAffectations.html.twig', array(
            'statutAffectationList'    => $statutAffectationList,
            'form'                  => $form->createView()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("statutAffectation", options={"mapping": {"statutAffectationId": "id"}})
     */
    public function editStatutAffectationAction(Request $request, StatutAffectation $statutAffectation)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new StatutAffectationType(), $statutAffectation);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($statutAffectation);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_statutAffectations');
        }

        return $this->render('NurunSystemBundle:Admin:editStatutAffectation.html.twig', array(
            'form'              => $form->createView(),
            'statutAffectation'    => $statutAffectation
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("statutAffectation", options={"mapping": {"statutAffectationId": "id"}})
     */
    public function removeStatutAffectationAction(Request $request, StatutAffectation $statutAffectation)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($statutAffectation);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_statutAffectations');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function languagesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $languageList = $em->getRepository('NurunRhBundle:Language')->findAll();

        $language = new Language();
        $form = $this->createForm(new LanguageType(), $language);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($language);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_languages');
        }

        return $this->render('NurunSystemBundle:Admin:languages.html.twig', array(
            'languageList'    => $languageList,
            'form'            => $form->createView()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("language", options={"mapping": {"languageId": "id"}})
     */
    public function editLanguageAction(Request $request, Language $language)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new LanguageType(), $language);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($language);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_languages');
        }

        return $this->render('NurunSystemBundle:Admin:editLanguage.html.twig', array(
            'form'        => $form->createView(),
            'language'    => $language
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("language", options={"mapping": {"languageId": "id"}})
     */
    public function removeLanguageAction(Request $request, Language $language)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($language);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_languages');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    public function actionsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $actionList = $em->getRepository('NurunRhBundle:Action')->findAll();

        $action = new Action();
        $form = $this->createForm(new ActionType(), $action);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($action);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_actions');
        }

        return $this->render('NurunSystemBundle:Admin:actions.html.twig', array(
            'actionList'    => $actionList,
            'form'          => $form->createView()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("action", options={"mapping": {"actionId": "id"}})
     */
    public function editActionAction(Request $request, Action $action)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ActionType(), $action);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($action);
            $em->flush();

            return $this->redirectToRoute('nurun_admin_actions');
        }

        return $this->render('NurunSystemBundle:Admin:editAction.html.twig', array(
            'form'        => $form->createView(),
            'action'    => $action
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("action", options={"mapping": {"actionId": "id"}})
     */
    public function removeActionAction(Request $request, Action $action)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $em->remove($action);
        $em->flush();

        return $this->redirectToRoute('nurun_admin_actions');
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function userFonctionsAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $fonctionList = $em->getRepository('NurunRhBundle:Fonction')->findAll();

        return $this->render('NurunSystemBundle:Admin:userFonctions.html.twig', array(
            'fonctionList'  => $fonctionList,
            'user'          => $user
            )
        );
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function userAddFonctionAction(Fonction $fonction, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $userFonction = new UserFonction();
        $userFonction->setUser($user);
        $userFonction->setFonction($fonction);

        $em->persist($userFonction);
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_admin_user_notifications', array('userId'=> $user->getId())));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     * @ParamConverter("fonction", options={"mapping": {"fonctionId": "id"}})
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function userRemoveFonctionAction(Fonction $fonction, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ROOT', null,'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $userFonction = $em->getRepository('NurunRhBundle:UserFonction')->getByUserAndFonction($user, $fonction);
        $em->remove($userFonction);


        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_admin_user_notifications', array('userId'=> $user->getId())));
    }
}
