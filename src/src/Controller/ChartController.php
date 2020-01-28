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
    private $colors;
    private $days;
    private $hours;

    // Constructor con las variables iniciales
    function __construct() {
        $this->colors = ['#bde0ff','#ffddbd','#e4f1cb','#dbbdff','#bdf1ff','#ffbdfd','#dee5e1','#ffffe1','#4bce6c','#4d493e','#66b03c','#6d76ba','#bde0ff','#ffddbd','#e4f1cb','#dbbdff','#bdf1ff','#ffbdfd','#dee5e1','#ffffe1','#4bce6c','#4d493e','#66b03c','#6d76ba'];
        $this->days=['monday', 'tuesday', 'wednesday', 'thursday', 'friday','saturday','sunday'];
        $this->hours=['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23' ];
    }
    // Función que devuelva un array de meses traducido
    function getTranslatedMonths(TranslatorInterface $translator,Array $queryMonths){
        $months = [];
        $cont = 0;
        foreach ( $queryMonths as $actualData ){
            $months[]= $translator->trans($actualData);
            $cont++;
        }
        return $months;
    }
    // Se selecciona por código postal en caso que no tenga informacion sobre algún mes no la muestro
    function getMonths(string $zipcode){
        $queryMonths = $this->getDoctrine()->getManager()->createQuery('SELECT DISTINCT basic_data.date FROM App\Entity\BasicData basic_data JOIN basic_data.zipcode zipcode where zipcode.zipcode='.$zipcode)->getResult();
        $months = [];
        $cont = 0;
        foreach ( $queryMonths as $actualData ){
            $months[]= $actualData['date'];
            $cont++;
        }
        return $months;
    }
    // Función que devuelve listado de códigos postales
    function getZipcodes(){
        $queryZipcodes = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $zipcodes = [];
        foreach ( $queryZipcodes as $actualData ){
            $zipcodes[]= $actualData['zipcode'];
        }
        return $zipcodes;
    }
    
    /**
     * @Route("/{_locale}/chart_basic_data/{zipcode}", name="chart_basic_data_zipcode")
     */
    public function chart_basic_data(TranslatorInterface $translator,string $zipcode)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        if (in_array(intval($zipcode),$zipcodes)){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT basic_data.avg,basic_data.merchants,basic_data.cards,basic_data.date FROM App\Entity\Zipcode zipcode 
            JOIN zipcode.basicData basic_data WHERE zipcode.zipcode='.$zipcode.' ORDER BY basic_data.date ASC')->getResult();
        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }
        
        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['date'],$months);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>$translator->trans('Media uso de tarjeta por Código postal').' '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true ]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>$translator->trans('Número de mercaderes por Código postal').' '.$zipcode,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>$translator->trans('Número de transacciones con tarjeta'),'backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'charts'=>$charts,
            'zipcodes'=>$zipcodes,
        ]);
    }
     /**
     * @Route("/{_locale}/chart_category_data/{zipcode}/{category_code}", name="chart_category_data_zipcode")
     */
    public function chart_category_data(TranslatorInterface $translator,string $zipcode,string $category_code)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        //Necesario para recoger las categorías de un determinado código postal ya que solo tendra algunas categorías de negocio
        $queryCategories = $this->getDoctrine()->getManager()->createQuery('SELECT DISTINCT category.code FROM App\Entity\Category category JOIN category.categoryData category_data JOIN category_data.zipcode zipcode  WHERE zipcode.zipcode='.$zipcode)->getResult(); 

        if (in_array(intval($zipcode),$zipcodes) && in_array([ 'code' => $category_code],$queryCategories)){
            // Hace falta poner :category_code para que lo trate como string
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT category_data.date,category_data.avg,category_data.cards,category_data.merchants,
            category_data.txs,zipcode.zipcode,category.code,category.description FROM App\Entity\Category category
            JOIN category.categoryData category_data JOIN category_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode. ' AND category.code=:category_code')->setParameters(['category_code'=>$category_code])->getResult();
        }else{
            throw $this->createNotFoundException($translator->trans('Código postal o categoría no disponible'));
        }
        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['date'],$months);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>$translator->trans('Media uso de tarjeta por Código postal').' '.$zipcode.' '.$translator->trans('y categoría').' '.$category_code,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$months,'datasets'=>[['label'=>$translator->trans('Número de mercaderes por Código postal').' '.$zipcode.' '.$translator->trans('y categoría').' '.$category_code,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>$translator->trans('Número de mercaderes')]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$months,'datasets'=>[['label'=>$translator->trans('Transacciones con tarjeta por Código postal').' '.$zipcode.' '.$translator->trans('y categoría').' '.$category_code,'backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        $cont=0;
        $categories=[];
        foreach ( $queryCategories as $actualData ){
            $categories+= [$cont++ => $actualData['code']];
        }

        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedCategory'=>$category_code,
            'charts'=>$charts,
            'categories'=>$categories,
            'zipcodes'=>$zipcodes,
        ]);
    }

     /**
     * @Route("/{_locale}/chart_day_data/{zipcode}/{date}", name="chart_day_data_zipcode")
     */
    public function chart_day_data(TranslatorInterface $translator,string $zipcode,string $date)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        
        //Necesario para recoger las categorías de un determinado código postal ya que solo tendra algunas categorías de negocio
        if (in_array(intval($zipcode),$zipcodes) && strlen($date) == 6 ){
            // Hace falta poner :category_code para que lo trate como string
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT day_data.date,day_data.day,day_data.avg,day_data.cards,day_data.merchants,
            day_data.txs,zipcode.zipcode FROM App\Entity\DayData day_data
            JOIN day_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode. ' AND day_data.date=:date ')->setParameters(['date'=>$date])->getResult();
        }else{
            throw $this->createNotFoundException($translator->trans('Código postal o categoría no disponible'));
        }
        // Valores iniciales por día
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        // Listado de días
        
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['day'],$this->days);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        for ( $i=0; $i < sizeof($this->days); $i++ ){
            $this->days[$i] = $translator->trans($this->days[$i]);
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$this->days,'datasets'=>[['label'=>'Media uso de tarjeta por Código postal: '.$zipcode.' y mes: '.$translator->trans($date),'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$this->days,'datasets'=>[['label'=>'Mercaderes por Código postal: '.$zipcode.' y mes: '.$translator->trans($date),'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>'Número de mercaderes']]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$this->days,'datasets'=>[['label'=>'Transacciones con tarjeta por Código postal: '.$zipcode.' y mes: '.$translator->trans($date),'backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $cont=0;

        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedDate'=>$date,
            'charts'=>$charts,
            'months'=>$months,
            'zipcodes'=>$zipcodes,
        ]);
    }

         /**
     * @Route("/{_locale}/chart_hour_data/{zipcode}/{date}/{day}", name="chart_hour_data_zipcode")
     */
    public function chart_hour_data(TranslatorInterface $translator,string $zipcode,string $date,string $day)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);

        //Necesario para recoger las categorías de un determinado código postal ya que solo tendra algunas categorías de negocio
        if (in_array(intval($zipcode),$zipcodes) && strlen($date) == 6 && strlen($day) <= 9){
            // Hace falta poner :category_code para que lo trate como string
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT day_data.date,hour_data.hour,hour_data.avg,hour_data.cards,hour_data.merchants,
            hour_data.txs,zipcode.zipcode FROM App\Entity\DayData day_data
            JOIN day_data.zipcode zipcode JOIN day_data.hourData hour_data WHERE zipcode.zipcode='.$zipcode. ' AND day_data.date=:date AND day_data.day=:day')->setParameters(['date'=>$date,'day'=>$day])->getResult();
        }else{
            throw $this->createNotFoundException($translator->trans('Código postal o categoría no disponible'));
        }
        // Valores iniciales por día
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,20=>0,21=>0,22=>0,23=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        // Listado de horas
        foreach ( $queryData as $actualData ){
            // Busco el índice de la fecha a introducir
            $index=array_search($actualData['hour'],$this->hours);
            $data[0][$index]=$actualData['avg'];
            $data[1][$index]=$actualData['merchants'];
            $data[2][$index]=$actualData['cards'];
        }
        
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$this->hours,'datasets'=>[['label'=>'Media uso de tarjeta por hora, Código postal: '.$zipcode.' y día: '.$translator->trans($day),'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$this->hours,'datasets'=>[['label'=>'Mercaderes por hora, Código postal: '.$zipcode.' y día: '.$translator->trans($day),'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true,'text'=>'Número de mercaderes']]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$this->hours,'datasets'=>[['label'=>'Transacciones con tarjeta por hora, Código postal: '.$zipcode.' y día: '.$translator->trans($day),'backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true,'text'=>'Prueba']]]]]])];
        $cont=0;

        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedDay'=>$day,
            'selectedDate'=>$date,
            'charts'=>$charts,
            'days'=>$this->days,
            'zipcodes'=>$zipcodes,
            'months'=>$months,
        ]);
    }

    /**
     * @Route("/{_locale}/chart_destination_data/{zipcode}/{date}", name="chart_destination_data_zipcode")
     */
    public function chart_destination_data(TranslatorInterface $translator,string $zipcode,string $date)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        if (in_array(intval($zipcode),$zipcodes) && strlen($date) == 6){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode,destination_data.destinationZipcode,destination_data.avg,destination_data.merchants,destination_data.cards,destination.date FROM App\Entity\DestinationData destination_data 
            JOIN destination_data.destination destination JOIN destination.zipcode zipcode WHERE zipcode.zipcode='.$zipcode.' AND destination.date ='.$date.' AND destination_data.destinationZipcode NOT IN (:others,:filtered)')->setParameters(['others'=>'others','filtered'=>'filtered'])->getResult();
            
            // Ordeno los datos recibidos para posteriormente mostrar los códigos postales más importantes
            foreach ($queryData as $key => $value) {
                $avg[$key] = $value['avg'];
                $merchants[$key] = $value['merchants'];
                $cards[$key] = $value['cards'];
            }
            // var_dump($queryData);
            // exit;
            $queryAvgData = $queryData;
            $queryMerchantsData= $queryData;
            $queryCardsData= $queryData;
            array_multisort($avg, SORT_DESC, $queryAvgData);
            array_multisort($merchants, SORT_DESC, $queryMerchantsData);
            array_multisort($cards, SORT_DESC, $queryCardsData);

        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }
        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de meses
        $top=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        $cont=0;
        for ( $i = 0; $i<10; $i++ ){
            // Almaceno el dato y el zipcode 
            $data[0][$i]=$queryAvgData[$i]['avg'];
            $top[0][$i]=$queryAvgData[$i]['destinationZipcode'];

            $data[1][$i]=$queryMerchantsData[$i]['merchants'];
            $top[1][$i]=$queryMerchantsData[$i]['destinationZipcode'];

            $data[2][$i]=$queryCardsData[$i]['cards'];
            $top[2][$i]=$queryCardsData[$i]['destinationZipcode'];
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[0],'datasets'=>[['label'=>'Top 10 destinos: Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true ]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$top[1],'datasets'=>[['label'=>'Top 10 destinos: Número de mercaderes por Código postal '.$zipcode,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[2],'datasets'=>[['label'=>'Top 10 destinos: Número de transacciones con tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedDate'=>$date,
            'charts'=>$charts,
            'months'=>$months,
            'zipcodes'=>$zipcodes,
        ]);
    }

    /**
     * @Route("/{_locale}/chart_origin_data/{zipcode}/{date}", name="chart_origin_data_zipcode")
     */
    public function chart_origin_data(TranslatorInterface $translator,string $zipcode,string $date)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        if (in_array(intval($zipcode),$zipcodes) && strlen($date) == 6){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode,origin_data.originZipcode,origin_data.avg,origin_data.merchants,origin_data.cards,origin_data.date FROM App\Entity\OriginData origin_data 
            JOIN origin_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode.' AND origin_data.date ='.$date.' AND origin_data.originZipcode NOT IN (:others,:filtered,:U)')->setParameters(['others'=>'others','filtered'=>'filtered','U'=>'U'])->getResult();
            // Ordeno los datos recibidos para posteriormente mostrar los códigos postales más importantes
            foreach ($queryData as $key => $value) {
                $avg[$key] = $value['avg'];
                $merchants[$key] = $value['merchants'];
                $cards[$key] = $value['cards'];
            }
            // var_dump($queryData);
            // exit;
            $queryAvgData = $queryData;
            $queryMerchantsData= $queryData;
            $queryCardsData= $queryData;
            array_multisort($avg, SORT_DESC, $queryAvgData);
            array_multisort($merchants, SORT_DESC, $queryMerchantsData);
            array_multisort($cards, SORT_DESC, $queryCardsData);

        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }

        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de meses
        $top=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        $cont=0;
        for ( $i = 0; $i<10; $i++ ){
            // Almaceno el dato y el zipcode 
            $data[0][$i]=$queryAvgData[$i]['avg'];
            $top[0][$i]=$queryAvgData[$i]['originZipcode'];

            $data[1][$i]=$queryMerchantsData[$i]['merchants'];
            $top[1][$i]=$queryMerchantsData[$i]['originZipcode'];

            $data[2][$i]=$queryCardsData[$i]['cards'];
            $top[2][$i]=$queryCardsData[$i]['originZipcode'];
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[0],'datasets'=>[['label'=>'Top 10 orígenes: Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true ]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$top[1],'datasets'=>[['label'=>'Top 10 orígenes: Número de mercaderes por Código postal '.$zipcode,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[2],'datasets'=>[['label'=>'Top 10 orígenes: Número de transacciones con tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedDate'=>$date,
            'charts'=>$charts,
            'months'=>$months,
            'zipcodes'=>$zipcodes,
        ]);
    }

    /**
     * @Route("/{_locale}/chart_origin_age_data/{zipcode}/{date}", name="chart_origin_age_data_zipcode")
     */
    public function chart_origin_age_data(TranslatorInterface $translator,string $zipcode,string $date)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        if (in_array(intval($zipcode),$zipcodes) && strlen($date) == 6){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode,origin_age_data.originZipcode,origin_age_data.age,origin_age_data.avg,origin_age_data.merchants,origin_age_data.cards,origin_age_data.date FROM App\Entity\OriginAgeData origin_age_data 
            JOIN origin_age_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode.' AND origin_age_data.date ='.$date.' AND origin_age_data.age NOT IN (:Unknown,:filtered) AND origin_age_data.originZipcode NOT IN (:others,:filtered,:U)')->setParameters(['Unknown'=>'Unknown','others'=>'others','filtered'=>'filtered','U'=>'U'])->getResult();
            // Ordeno los datos recibidos para posteriormente mostrar los códigos postales más importantes
            foreach ($queryData as $key => $value) {
                $avg[$key] = $value['avg'];
                $merchants[$key] = $value['merchants'];
                $cards[$key] = $value['cards'];
            }
            $queryAvgData = $queryData;
            $queryMerchantsData= $queryData;
            $queryCardsData= $queryData;
            array_multisort($avg, SORT_DESC, $queryAvgData);
            array_multisort($merchants, SORT_DESC, $queryMerchantsData);
            array_multisort($cards, SORT_DESC, $queryCardsData);

        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }

        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de meses
        $top=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        $cont=0;
        for ( $i = 0; $i<10; $i++ ){
            // Almaceno el dato y el zipcode 
            $data[0][$i]=$queryAvgData[$i]['avg'];
            $top[0][$i]=$queryAvgData[$i]['originZipcode'].' '.$queryAvgData[$i]['age'];

            $data[1][$i]=$queryMerchantsData[$i]['merchants'];
            $top[1][$i]=$queryMerchantsData[$i]['originZipcode'].' '.$queryAvgData[$i]['age'];

            $data[2][$i]=$queryCardsData[$i]['cards'];
            $top[2][$i]=$queryCardsData[$i]['originZipcode'].' '.$queryAvgData[$i]['age'];
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[0],'datasets'=>[['label'=>'Top 10 orígenes por edad: Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true ]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$top[1],'datasets'=>[['label'=>'Top 10 orígenes por edad: Número de mercaderes por Código postal '.$zipcode,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[2],'datasets'=>[['label'=>'Top 10 orígenes por edad: Número de transacciones con tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedDate'=>$date,
            'charts'=>$charts,
            'months'=>$months,
            'zipcodes'=>$zipcodes,
        ]);
    }

    /**
     * @Route("/{_locale}/chart_origin_gender_data/{zipcode}/{date}", name="chart_origin_gender_data_zipcode")
     */
    public function chart_origin_gender_data(TranslatorInterface $translator,string $zipcode,string $date)
    {
        $zipcodes = $this->getZipcodes();
        $months=$this->getMonths($zipcode);
        if (in_array(intval($zipcode),$zipcodes) && strlen($date) == 6){
            $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode,origin_age_data.originZipcode,origin_age_data.age,origin_gender_data.gender,origin_gender_data.avg,origin_gender_data.merchants,origin_gender_data.cards,origin_age_data.date FROM App\Entity\OriginGenderData origin_gender_data 
            JOIN origin_gender_data.originAgeData origin_age_data JOIN origin_age_data.zipcode zipcode WHERE zipcode.zipcode='.$zipcode.' AND origin_age_data.date ='.$date.' AND origin_age_data.age NOT IN (:Unknown,:filtered) AND origin_age_data.originZipcode NOT IN (:others,:filtered,:U) AND origin_gender_data.gender != :filtered')->setParameters(['Unknown'=>'Unknown','others'=>'others','filtered'=>'filtered','U'=>'U'])->getResult();
            // Ordeno los datos recibidos para posteriormente mostrar los códigos postales más importantes
            foreach ($queryData as $key => $value) {
                $avg[$key] = $value['avg'];
                $merchants[$key] = $value['merchants'];
                $cards[$key] = $value['cards'];
            }
            $queryAvgData = $queryData;
            $queryMerchantsData= $queryData;
            $queryCardsData= $queryData;
            array_multisort($avg, SORT_DESC, $queryAvgData);
            array_multisort($merchants, SORT_DESC, $queryMerchantsData);
            array_multisort($cards, SORT_DESC, $queryCardsData);

        }else{
            throw $this->createNotFoundException('Código postal no disponible');
        }

        // Valores iniciales por mes
        $initialValues=[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0];
        // Inicializo variable
        $data=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        
        // Listado de meses
        $top=[0=>$initialValues,1=>$initialValues,2=>$initialValues];
        $cont=0;
        for ( $i = 0; $i<10; $i++ ){
            // Almaceno el dato y el zipcode 
            $data[0][$i]=$queryAvgData[$i]['avg'];
            $top[0][$i]=$queryAvgData[$i]['originZipcode'].' '.$queryAvgData[$i]['age'].' '.$queryAvgData[$i]['gender'];

            $data[1][$i]=$queryMerchantsData[$i]['merchants'];
            $top[1][$i]=$queryMerchantsData[$i]['originZipcode'].' '.$queryAvgData[$i]['age'].' '.$queryAvgData[$i]['gender'];

            $data[2][$i]=$queryCardsData[$i]['cards'];
            $top[2][$i]=$queryCardsData[$i]['originZipcode'].' '.$queryAvgData[$i]['age'].' '.$queryAvgData[$i]['gender'];
        }
        $months=$this->getTranslatedMonths($translator,$months);
        $cont=0;
        $charts = [];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[0],'datasets'=>[['label'=>'Top 10 orígenes por edad y género: Media uso de tarjeta por Código postal '.$zipcode,'backgroundColor'=>$this->colors[0],'borderColor'=>'#000000','data'=>$data[0],'options'=>['title'=>['display'=>true ]]]]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'bar','data'=>['labels'=>$top[1],'datasets'=>[['label'=>'Top 10 orígenes por edad y género: Número de mercaderes por Código postal '.$zipcode,'backgroundColor'=>$this->colors,'data'=>$data[1]]]],'options'=>['title'=>['display'=>true]]])];
        $charts[] = [$cont++=>json_encode(['type'=>'line','data'=>['labels'=>$top[2],'datasets'=>[['label'=>'Top 10 orígenes por edad y género: Número de transacciones con tarjeta','backgroundColor'=>$this->colors[1],'borderColor'=>'#000000','data'=>$data[2],'options'=>['title'=>['display'=>true]]]]]])];
        return $this->render('/chart/data.html.twig',[
            'selectedZipcode'=>$zipcode,
            'selectedDate'=>$date,
            'charts'=>$charts,
            'months'=>$months,
            'zipcodes'=>$zipcodes,
        ]);
    }
}