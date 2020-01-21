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
    private $colors=['#bde0ff','#ffddbd','#e4f1cb','#dbbdff','#bdf1ff','#ffbdfd','#dee5e1','#ffffe1','#4bce6c','#4d493e','#66b03c','#6d76ba'];

    /**
     * @Route("/chart_basic_data/{zipcode}", name="chart_basic_data_zipcode")
     */
    public function chart_basic_data(string $zipcode)
    {
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        if (in_array([ 'zipcode' => intval($zipcode)],$queryZipCode)){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT basic_data.avg,basic_data.merchants,basic_data.std,basic_data.cards FROM App\Entity\Zipcode zipcode 
            JOIN zipcode.basicData basic_data WHERE zipcode.zipcode='.$zipcode.' ORDER BY basic_data.date ASC')->getResult();
        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }
        // Inicializo variable
        $data=[];
        // Listado de meses
        $months=['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        foreach ( $queryData as $actualData ){
            $data[0][]=$actualData['avg'];
            $data[1][]=$actualData['merchants'];
            $data[2][]=$actualData['cards'];
        }
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>'Mercaderes','backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>'Número de mercaderes']]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Número de transacciones por tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        return $this->render('/chart/basic_data.html.twig',[
            'label'=>$months,
            'zipcode'=>$zipcode,
            'charts'=>$charts,
        ]);
    }
        /**
     * @Route("/chart_category_data/{zipcode}", name="chart_category_data_zipcode")
     */
    public function chart_category_data(string $zipcode)
    {
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        if (in_array([ 'zipcode' => intval($zipcode)],$queryZipCode)){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT basic_data.avg,basic_data.merchants,basic_data.std,basic_data.cards FROM App\Entity\Zipcode zipcode 
            JOIN zipcode.basicData basic_data WHERE zipcode.zipcode='.$zipcode.' ORDER BY basic_data.date ASC')->getResult();
        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }
        // Inicializo variable
        $data=[];
        // Listado de meses
        $months=['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        foreach ( $queryData as $actualData ){
            $data[0][]=$actualData['avg'];
            $data[1][]=$actualData['merchants'];
            $data[2][]=$actualData['cards'];
        }
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>'Mercaderes','backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>'Número de mercaderes']]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Transacciones por tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        return $this->render('/chart/basic_data.html.twig',[
            'label'=>$months,
            'zipcode'=>$zipcode,
            'charts'=>$charts,
        ]);
    }
    /**
     * @Route("/chart_hour_data", name="chart_hour_data_zipcode")
     */
    public function chart_hour_data()
    {
        
        return $this->render('/chart/basic_data.html.twig');
    }
}