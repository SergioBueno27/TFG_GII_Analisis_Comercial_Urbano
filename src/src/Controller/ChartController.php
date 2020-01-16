<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BasicData;
use App\Entity\ZipCode;

set_time_limit(0);
ini_set('memory_limit', '-1');
class ChartController extends AbstractController
{
        /**
     * @Route("/chart_basic_data", name="chart_basic_data_zipcode")
     */
    public function chart_basic_data()
    {
        
        return $this->render('/chart/basic_data.html.twig');
    }
}