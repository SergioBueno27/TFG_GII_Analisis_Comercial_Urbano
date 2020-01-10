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
            'status_upload_day_hour' => "0",
            'status_destination' => "0",
            'status_upload_destination' => "0",
            'status_origin' => "0",
            'status_upload_origin' => "0",
            'status_origin_age_gender' => "0",
            'status_upload_origin_age_gender' => "0",
        ]);
    }

    /**
     * @Route("/basic_data", name="basic_data")
     */
    public function basic_data()
    {
        // $basic_data = $this->getDoctrine()->getRepository(BasicData::class)->findAll();
        // var_dump($basic_data[0]);
        return $this->render('/data/basic_data.html.twig');
    }

    /**
     * @Route("/category_data", name="category_data")
     */
    public function category_data()
    {
        return $this->render('/data/category_data.html.twig');
    }

}
