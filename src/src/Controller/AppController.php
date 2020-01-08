<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

set_time_limit(0);
ini_set('memory_limit', '-1');
class AppController extends AbstractController
{

    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('/security/administration.html.twig', [
            'status' => "0",
            'status_merchants' => "0",
            'status_basic' => "0",
            'status_category' => "0",
            'status_upload_category' => "0",
            'status_day_hour' => "0",
            'status_upload_day' => "0",
            'status_upload_hour' => "0",
        ]);
    }

}
