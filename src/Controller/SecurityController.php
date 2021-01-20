<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/profile", name="app_profile", methods={"GET"})
     */
    public function profile(): Response {
        return $this->render('security/profile.html.twig');
    }
    
    /**
     * @Route("/profile", name="app_profile_save", methods={"POST"})
     */
    public function saveProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder): RedirectResponse {
        
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($this->getUser()->getId());

        $user->setName($request->get('name'));
        $user->setEmail($request->get('email'));
        
        if (!empty($request->get('password'))) {
            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password')));
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('info', 'User updated');

        return $this->redirectToRoute('app_profile');
    }
}
