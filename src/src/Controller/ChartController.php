<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BasicData;
use App\Entity\ZipCode;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

set_time_limit(0);
ini_set('memory_limit', '-1');
class ChartController extends AbstractController
{
    //Colores para gráficos
    private $colors=['#bde0ff','#ffddbd','#e4f1cb','#dbbdff','#bdf1ff','#ffbdfd','#dee5e1','#ffffe1','#4bce6c','#4d493e','#66b03c','#6d76ba','#bde0ff','#ffddbd','#e4f1cb','#dbbdff','#bdf1ff','#ffbdfd','#dee5e1','#ffffe1','#4bce6c','#4d493e','#66b03c','#6d76ba'];

    /**
     * @Route("/chart_basic_data/{zipcode}", name="chart_basic_data_zipcode")
     */
    public function chart_basic_data(TranslatorInterface $translator,string $zipcode)
    {
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        if (in_array([ 'zipcode' => intval($zipcode)],$queryZipCode)){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT basic_data.avg,basic_data.merchants,basic_data.cards,basic_data.date FROM App\Entity\Zipcode zipcode 
            JOIN zipcode.basicData basic_data WHERE zipcode.zipcode='.$zipcode.' ORDER BY basic_data.date ASC')->getResult();
        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }
        
        // Listado de meses
        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de meses
        $months=['201501', '201502', '201503', '201504', '201505','201506','201507','201508','201509','201510','201511','201512'];
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['date'],$months);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        $months=[$translator->trans('201501'), $translator->trans('201502'), $translator->trans('201503'), $translator->trans('201504'), $translator->trans('201505'), $translator->trans('201506'), $translator->trans('201507'),$translator->trans('201508'),$translator->trans('201509'),$translator->trans('201510'),$translator->trans('201511'),$translator->trans('201512')];
            
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true ]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>'Número de mercaderes por Código postal '.$zipcode,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Número de transacciones con tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        return $this->render('/chart/data.html.twig',[
            'label'=>$months,
            'zipcode'=>$zipcode,
            'charts'=>$charts,
        ]);
    }
     /**
     * @Route("/chart_category_data/{zipcode}/{category_code}", name="chart_category_data_zipcode")
     */
    public function chart_category_data(TranslatorInterface $translator,string $zipcode,string $category_code)
    {
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        //Necesario para recoger las categorías de un determinado código postal ya que solo tendra algunas categorías de negocio
        $queryCategories = $this->getDoctrine()->getManager()->createQuery('SELECT category.code FROM App\Entity\CategoryData category_data JOIN category_data.zipcode zipcode JOIN category_data.category category WHERE zipcode.zipcode='.$zipcode)->getResult(); 

        if (in_array([ 'zipcode' => intval($zipcode)],$queryZipCode) && in_array([ 'code' => $category_code],$queryCategories)){
            // Hace falta poner :category_code para que lo trate como string
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT category_data.date,category_data.avg,category_data.cards,category_data.merchants,
            category_data.txs,zipcode.zipcode,category.code,category.description FROM App\Entity\Category category
            JOIN category.categoryData category_data JOIN category_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode. ' AND category.code=:category_code')->setParameters(['category_code'=>$category_code])->getResult();
        }else{
            throw $this->createNotFoundException('Código postal o categoría no disponible');
        }
        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de meses
        $months=['201501', '201502', '201503', '201504', '201505','201506','201507','201508','201509','201510','201511','201512'];
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['date'],$months);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        $months=[$translator->trans('201501'), $translator->trans('201502'), $translator->trans('201503'), $translator->trans('201504'), $translator->trans('201505'), $translator->trans('201506'), $translator->trans('201507'),$translator->trans('201508'),$translator->trans('201509'),$translator->trans('201510'),$translator->trans('201511'),$translator->trans('201512')];
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Media uso de tarjeta por Código postal: '.$zipcode.' y categoría: '.$category_code,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>'Mercaderes por Código postal: '.$zipcode.' y categoría: '.$category_code,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>'Número de mercaderes']]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Transacciones con tarjeta por Código postal: '.$zipcode.' y categoría: '.$category_code,'backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $cont=0;
        $categories=[];
        foreach ( $queryCategories as $actualData ){
            $categories+= [$cont++ => $actualData['code']];
        }

        return $this->render('/chart/data.html.twig',[
            'label'=>$months,
            'zipcode'=>$zipcode,
            'selectedCategory'=>$category_code,
            'charts'=>$charts,
            'categories'=>$categories,
        ]);
    }

     /**
     * @Route("/chart_day_data/{zipcode}/{date}", name="chart_day_data_zipcode")
     */
    public function chart_day_data(TranslatorInterface $translator,string $zipcode,string $date)
    {
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        //Necesario para recoger las categorías de un determinado código postal ya que solo tendra algunas categorías de negocio
        if (in_array([ 'zipcode' => intval($zipcode)],$queryZipCode) && strlen($date) == 6 ){
            // Hace falta poner :category_code para que lo trate como string
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT day_data.date,day_data.day,day_data.avg,day_data.cards,day_data.merchants,
            day_data.txs,zipcode.zipcode FROM App\Entity\DayData day_data
            JOIN day_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode. ' AND day_data.date=:date ')->setParameters(['date'=>$date])->getResult();
        }else{
            throw $this->createNotFoundException('Código postal o categoría no disponible');
        }
        // Valores iniciales por día
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de días
        $days=['monday', 'tuesday', 'wednesday', 'thursday', 'friday','saturday','sunday'];
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['day'],$days);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        $months=[$translator->trans('monday'), $translator->trans('tuesday'), $translator->trans('wednesday'), $translator->trans('thursday'), $translator->trans('friday'), $translator->trans('saturday'), $translator->trans('sunday')];
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Media uso de tarjeta por Código postal: '.$zipcode.' y mes: '.$translator->trans($date),'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>'Mercaderes por Código postal: '.$zipcode.' y mes: '.$translator->trans($date),'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>'Número de mercaderes']]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>'Transacciones con tarjeta por Código postal: '.$zipcode.' y mes: '.$translator->trans($date),'backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $cont=0;

        return $this->render('/chart/data.html.twig',[
            'label'=>$months,
            'zipcode'=>$zipcode,
            'selectedDay'=>$date,
            'charts'=>$charts,
        ]);
    }
}