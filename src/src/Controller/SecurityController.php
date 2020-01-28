<?php

namespace App\Controller;
use App\Service\Languages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    private $languages;

    // Constructor con las variables iniciales
    function __construct() {
        // Lenguajes disponibles en la aplicación
        $languages = new Languages();
        $this->languages=$languages->getLangs();
    }
    /**
     * @Route("/", name="main")
     */
    public function root(Request $request,TranslatorInterface $translator,AuthenticationUtils $authenticationUtils): Response
    {
        // Recojo el lenguaje del navegador y lo uso como inicial
        return $this->redirectToRoute('app_login',[ '_locale' => explode(';',(explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"])[1]))[0]]);
    }
    /**
     * @Route("/{_locale}/login", name="app_login")
     */
    public function login(Request $request,AuthenticationUtils $authenticationUtils): Response
    {
        
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'languages' => $this->languages , 'selectedLanguage' => $request->getLocale()]);
    }
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
    }
}
