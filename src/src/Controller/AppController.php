<?php

namespace App\Controller;
use App\Service\Languages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BasicData;
use App\Entity\ZipCode;

set_time_limit(0);
ini_set('memory_limit', '-1');
class AppController extends AbstractController
{
    private $languages;

    // Constructor con las variables iniciales
    function __construct() {
        // Lenguajes disponibles en la aplicación
        $languages = new Languages();
        $this->languages=$languages->getLangs();
    }  

    /**
     * @Route("/{_locale}/home", name="home")
     */
    public function index(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login',['_locale']);
        }
        return $this->render('base.html.twig',['languages' => $this->languages , 'selectedLanguage' => $request->getLocale()]);
    }

    /**
     * @Route("/{_locale}/admin", name="admin")
     */
    public function admin(Request $request)
    {
        return $this->render('/security/administration.html.twig', [
            'status' =>  $translator->trans('Recuerde que algunas operaciones pueden durar varios minutos'),
            'status_merchants' => "0",
            'status_basic' => "0",
            'status_category' => "0",
            'status_upload_category' => "0",
            'status_day_hour' => "0",
            'status_upload_day_hour' => "0",
            'status_destination' => "0",
            'status_upload_destination' => "0",
            'status_origin' => "0",
            'status_upload_origin' => "0",
            'status_origin_age_gender' => "0",
            'status_upload_origin_age_gender' => "0",
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()
        ]);
    }

}
