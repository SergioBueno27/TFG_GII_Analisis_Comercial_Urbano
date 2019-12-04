<?php

namespace App\Controller;

use App\Entity\BasicData;
use App\Entity\Category;
use App\Entity\CategoryData;
use App\Entity\SubCategory;
use App\Entity\Zipcode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
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

}
