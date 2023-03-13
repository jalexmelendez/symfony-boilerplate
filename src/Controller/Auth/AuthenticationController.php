<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\UserRegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // In this case returns the email.
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/authentication/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login/success', name: 'login_success')]
    public function loginRedirect(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $roles = $this->getUser()->getRoles();

        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)){
            return new RedirectResponse('/admin');
        } else {
            return new RedirectResponse('/dashboard');
        }
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $userWasSaved = false;
        $error = null;
        $registrationForm = $this->createForm(UserRegistrationType::class, $user);

        $registrationForm->handleRequest($request);
        if($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user = $registrationForm->getData();
            $manager = $managerRegistry->getManager();

            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            
            try {
                $manager->persist($user);
                $manager->flush();

                $userWasSaved = true;
            } catch(Exception $e) {
                $error = 'A user exists with this credentials.';
            }
        }

        return $this->render('auth/authentication/register.html.twig', [
            'form' => $registrationForm,
            'user_created' => $userWasSaved,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * --------------------
     * API REGISTER ROUTE
     * --------------------
     */

    #[Route('/api/users/register', name: 'app_api_register', methods: ['POST'])]
    public function apiRegister(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $userPasswordHasherInterface): JsonResponse
    {
        $data = json_decode($request->getContent());

        $user = new User();

        try {
            $user->setUsername($data->username);
            $user->setEmail($data->email);
            $user->setPassword($data->password);
            $user->setName($data->name);
            $user->setRoles(['ROLE_USER']);
        } catch (Exception $e) {
            return $this->json([
                'Created' => false,
                'Error' => $e,
            ]);
        }
        $manager = $managerRegistry->getManager();
        
        $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();

        return $this->json([
            'Created' => true
        ]);
    }
}
