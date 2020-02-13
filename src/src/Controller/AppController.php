<?php

namespace App\Controller;
use App\Service\Languages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BasicData;
use App\Entity\ZipCode;


use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

// Import the BinaryFileResponse to download files
use Symfony\Component\HttpFoundation\File\File;

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
    public function admin(TranslatorInterface $translator,Request $request)
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
    /**
     * @Route("/{_locale}/net/{month}", name="net")
     */
    public function net(Request $request, string $month)
    {
        // Con esta función recojo el fichero net del código postal pedido

        $queryDataOrigins = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode,origin_data.date,origin_data.originZipcode,origin_data.txs FROM App\Entity\Zipcode zipcode JOIN zipcode.originData origin_data WHERE origin_data.date='.$month.' AND origin_data.originZipcode NOT IN (:others,:filtered,:U)')->setParameters(['others'=>'others','filtered'=>'filtered','U'=>'U'])->getResult();
        $queryDataDestinations = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode,destinations.date,destination_data.destinationZipcode,destination_data.txs FROM App\Entity\Zipcode zipcode  JOIN  zipcode.destinations destinations JOIN destinations.destinationData destination_data WHERE destinations.date='.$month.' AND destination_data.destinationZipcode NOT IN (:others,:filtered,:U)')->setParameters(['others'=>'others','filtered'=>'filtered','U'=>'U'])->getResult();
        $origins = new File('./netfiles/netfile.net');
        $file = fopen("./netfiles/netfile.net", "w");
        $array=[];
        $arrayData=[];
        $cont=1;
        fwrite($file, "*Vertices ");
        // Primera vuelta para recoger los vértices
        foreach($queryDataOrigins as $actualData){
            if(!array_key_exists($actualData['zipcode'],$array)){
                $array+=[$actualData['zipcode'] => $cont++];
            }
            if(!array_key_exists($actualData['originZipcode'],$array)){
                $array+=[$actualData['originZipcode'] => $cont++];
            }
            // Dado un código postal de donde vienen
            $arrayData[]=[$actualData['originZipcode'],$actualData['zipcode'],$actualData['txs']];

        }
        
        foreach($queryDataDestinations as $actualData){
            if(!array_key_exists($actualData['zipcode'],$array)){
                $array+=[$actualData['zipcode'] => $cont++];
            }
            if(!array_key_exists($actualData['destinationZipcode'],$array)){
                $array+=[$actualData['destinationZipcode'] => $cont++];
            }
            // Dado un código postal a donde van
            $arrayData[]=[$actualData['zipcode'],$actualData['destinationZipcode'],$actualData['txs']];
        }
        fwrite($file, ($cont-1)."\n");
        foreach ($array as $key => $value){
            fwrite($file, $value." "."\"".$key."\""."\n");
        }
        fwrite($file, "*Arcslist");
        foreach ($arrayData as $element){
            fwrite($file, $array[$element[0]]." ".$array[$element[1]]." ".$element[2]."\n");
        }
        fclose($file);
        return $this->file($origins);
    }
}