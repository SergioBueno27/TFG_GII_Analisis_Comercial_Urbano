<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BasicData;
use App\Entity\ZipCode;

set_time_limit(0);
ini_set('memory_limit', '-1');
class AppController extends AbstractController
{

    /**
     * @Route("/{_locale}", name="home")
     */
    public function index()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login',['_locale']);
        }
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/{_locale}/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('/security/administration.html.twig', [
            'status' => "Recuerde que algunas operaciones pueden durar varios minutos",
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
        ]);
    }

}
