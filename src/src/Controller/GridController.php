<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BasicData;
use App\Entity\ZipCode;

set_time_limit(0);
ini_set('memory_limit', '-1');
class GridController extends AbstractController
{

    /**
     * @Route("/basic_data/{zipcode}", name="basic_data_zipcode")
     */
    public function basic_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT basic_data.id,zipcode.zipcode,basic_data.avg,basic_data.cards,basic_data.date,basic_data.txs,basic_data.merchants,basic_data.min,basic_data.peak_txs_day,basic_data.peak_txs_hour,basic_data.std,basic_data.valley_txs_day,basic_data.valley_txs_hour,basic_data.max FROM App\Entity\Zipcode zipcode JOIN zipcode.basicData basic_data WHERE zipcode.zipcode='.$zipcode)->getResult();
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        // var_dump($queryData[0]);
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"], $cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"], $cont++ => ["headerName" => "date", "field" => "date"], $cont++ => ["headerName" => "txs", "field" => "txs"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "peak_txs_day", "field" => "peak_txs_day"], $cont++ => ["headerName" => "peak_txs_hour", "field" => "peak_txs_hour"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "valley_txs_day", "field" => "valley_txs_day"], $cont++ => ["headerName" => "valley_txs_hour", "field" => "valley_txs_hour"], $cont++ => ["headerName" => "max", "field" => "max"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "date" => $actualData['date'], "txs" => $actualData['txs'], "merchants" => $actualData['merchants'], "min" => $actualData['min'], "peak_txs_day" => $actualData['peak_txs_day'], "peak_txs_hour" => $actualData['peak_txs_hour'], "std" => $actualData['std'], "valley_txs_day" => $actualData['valley_txs_day'], "valley_txs_hour" => $actualData['valley_txs_hour'], "max" => $actualData['max']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

    /**
     * @Route("/all_basic_data_zipcode", name="all_basic_data_zipcode")
     */
    public function all_basic_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT basic_data.id,zipcode.zipcode,basic_data.avg,
        basic_data.cards,basic_data.date,basic_data.txs,basic_data.merchants,basic_data.min,basic_data.peak_txs_day,
        basic_data.peak_txs_hour,basic_data.std,basic_data.valley_txs_day,basic_data.valley_txs_hour,basic_data.max 
        FROM App\Entity\Zipcode zipcode JOIN zipcode.basicData basic_data')->getResult();
        // var_dump($queryData[0]);
        $cont = 0;
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"], $cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"], $cont++ => ["headerName" => "date", "field" => "date"], $cont++ => ["headerName" => "txs", "field" => "txs"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "peak_txs_day", "field" => "peak_txs_day"], $cont++ => ["headerName" => "peak_txs_hour", "field" => "peak_txs_hour"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "valley_txs_day", "field" => "valley_txs_day"], $cont++ => ["headerName" => "valley_txs_hour", "field" => "valley_txs_hour"], $cont++ => ["headerName" => "max", "field" => "max"]];  
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "date" => $actualData['date'], "txs" => $actualData['txs'], "merchants" => $actualData['merchants'], "min" => $actualData['min'], "peak_txs_day" => $actualData['peak_txs_day'], "peak_txs_hour" => $actualData['peak_txs_hour'], "std" => $actualData['std'], "valley_txs_day" => $actualData['valley_txs_day'], "valley_txs_hour" => $actualData['valley_txs_hour'], "max" => $actualData['max']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
        ]);
    }

    /**
     * @Route("/category_data/{zipcode}", name="category_data_zipcode")
     */
    public function category_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT category_data.id,category_data.avg,category_data.cards,category_data.merchants,
        category_data.txs,zipcode.zipcode,category.code,category.description,category_data.date FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.categoryData category_data JOIN category_data.category category WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"], $cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "code", "field" => "code"], $cont++ => ["headerName" => "description", "field" => "description"], $cont++ => ["headerName" => "date", "field" => "date"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs'], "zipcode" => $actualData['zipcode'], "code" => $actualData['code'], "description" => $actualData['description'], "date" => $actualData['date']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

    /**
     * @Route("/all_category_data_zipcode", name="all_category_data_zipcode")
     */
    public function all_category_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT category_data.id,category_data.avg,category_data.cards,category_data.merchants,
        category_data.txs,zipcode.zipcode,category.code,category.description,category_data.date FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.categoryData category_data JOIN category_data.category category')->getResult();
        // var_dump($queryData[0]);
        $cont = 0;
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"], $cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "code", "field" => "code"], $cont++ => ["headerName" => "description", "field" => "description"], $cont++ => ["headerName" => "date", "field" => "date"]];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs'], "zipcode" => $actualData['zipcode'], "code" => $actualData['code'], "description" => $actualData['description'], "date" => $actualData['date']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
        ]);
    }

    /**
     * @Route("/day_data/{zipcode}", name="day_data_zipcode")
     */
    public function day_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT day_data.id,zipcode.zipcode,day_data.date,day_data.avg,day_data.day,
        day_data.max,day_data.min,day_data.merchants,day_data.mode,day_data.std,day_data.txs,day_data.cards FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.dayData day_data WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "day", "field" => "day"], $cont++ => ["headerName" => "max", "field" => "max"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "mode", "field" => "mode"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "txs", "field" => "txs"], $cont++ => ["headerName" => "cards", "field" => "cards"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'], "date" => $actualData['date'], "avg" => $actualData['avg'], "day" => $actualData['day'], "max" => $actualData['max'], "min" => $actualData['min'], "merchants" => $actualData['merchants'], "mode" => $actualData['mode'], "std" => $actualData['std'], "txs" => $actualData['txs'], "cards" => $actualData['cards']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

    /**
     * @Route("/all_day_data_zipcode", name="all_day_data_zipcode")
     */
    public function all_day_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT day_data.id,zipcode.zipcode,day_data.date,day_data.avg,day_data.day,
        day_data.max,day_data.min,day_data.merchants,day_data.mode,day_data.std,day_data.txs,day_data.cards FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.dayData day_data ')->getResult();
        // var_dump($queryData[0]);
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "day", "field" => "day"], $cont++ => ["headerName" => "max", "field" => "max"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "mode", "field" => "mode"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "txs", "field" => "txs"], $cont++ => ["headerName" => "cards", "field" => "cards"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'], "date" => $actualData['date'], "avg" => $actualData['avg'], "day" => $actualData['day'], "max" => $actualData['max'], "min" => $actualData['min'], "merchants" => $actualData['merchants'], "mode" => $actualData['mode'], "std" => $actualData['std'], "txs" => $actualData['txs'], "cards" => $actualData['cards']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
        ]);
    }

    /**
     * @Route("/hour_data/{zipcode}", name="hour_data_zipcode")
     */
    public function hour_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT hour_data.id,day_data.id as id_day,day_data.day,day_data.date,zipcode.zipcode,hour_data.avg,hour_data.cards,hour_data.hour,hour_data.max,hour_data.merchants,hour_data.min,hour_data.mode,hour_data.std,hour_data.txs FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.dayData day_data JOIN day_data.hourData hour_data WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "id_day", "field" => "id_day"],$cont++ => ["headerName" => "day", "field" => "day"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "hour", "field" => "hour"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "max", "field" => "max"], $cont++ => ["headerName" => "mode", "field" => "mode"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "id_day" => $actualData['id_day'],"day" => $actualData['day'],"date" => $actualData['date'], "zipcode" => $actualData['zipcode'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "hour" => $actualData['hour'], "max" => $actualData['max'], "merchants" => $actualData['merchants'], "min" => $actualData['min'], "mode" => $actualData['mode'], "std" => $actualData['std'], "txs" => $actualData['txs']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

    /**
     * @Route("/all_hour_data_zipcode", name="all_hour_data_zipcode")
     */
    public function all_hour_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT hour_data.id,day_data.id as id_day,day_data.day,day_data.date,zipcode.zipcode,hour_data.avg,hour_data.cards,hour_data.hour,hour_data.max,hour_data.merchants,hour_data.min,hour_data.mode,hour_data.std,hour_data.txs FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.dayData day_data JOIN day_data.hourData hour_data')->getResult();
        // var_dump($queryData[0]);
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "id_day", "field" => "id_day"],$cont++ => ["headerName" => "day", "field" => "day"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "hour", "field" => "hour"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "max", "field" => "max"], $cont++ => ["headerName" => "mode", "field" => "mode"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "id_day" => $actualData['id_day'],"day" => $actualData['day'],"date" => $actualData['date'], "zipcode" => $actualData['zipcode'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "hour" => $actualData['hour'], "max" => $actualData['max'], "merchants" => $actualData['merchants'], "min" => $actualData['min'], "mode" => $actualData['mode'], "std" => $actualData['std'], "txs" => $actualData['txs']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
        ]);
    }

    /**
     * @Route("/destination_data/{zipcode}", name="destination_data_zipcode")
     */
    public function destination_data_zipcode(string $zipcode)
    {
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT destination_data.id,zipcode.zipcode,destinations.date,destination_data.destinationZipcode,destination_data.avg,destination_data.cards,destination_data.txs,destination_data.merchants FROM App\Entity\Zipcode zipcode  JOIN  zipcode.destinations destinations JOIN destinations.destinationData destination_data  WHERE zipcode.zipcode='.$zipcode)->getResult();

        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "destinationZipcode", "field" => "destinationZipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "txs", "field" => "txs"],$cont++ => ["headerName" => "merchants", "field" => "merchants"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"destinationZipcode" => $actualData['destinationZipcode'],"date" => $actualData['date'], "zipcode" => $actualData['zipcode'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "txs" => $actualData['txs'], "merchants" => $actualData['merchants']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

    /**
     * @Route("/all_destination_data_zipcode", name="all_destination_data_zipcode")
     */
    public function all_destination_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT destination_data.id,zipcode.zipcode,destinations.date,destination_data.destinationZipcode,destination_data.avg,destination_data.cards,destination_data.txs,destination_data.merchants FROM App\Entity\Zipcode zipcode  JOIN  zipcode.destinations destinations JOIN destinations.destinationData destination_data')->getResult();
        // var_dump($queryData[0]);
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "id_day", "field" => "id_day"],$cont++ => ["headerName" => "day", "field" => "day"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"], $cont++ => ["headerName" => "avg", "field" => "avg"], $cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "hour", "field" => "hour"], $cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "min", "field" => "min"], $cont++ => ["headerName" => "max", "field" => "max"], $cont++ => ["headerName" => "mode", "field" => "mode"], $cont++ => ["headerName" => "std", "field" => "std"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"destinationZipcode" => $actualData['destinationZipcode'],"date" => $actualData['date'], "zipcode" => $actualData['zipcode'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "txs" => $actualData['txs'], "merchants" => $actualData['merchants']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
        ]);
    }

    /**
     * @Route("/origin_data/{zipcode}", name="origin_data_zipcode")
     */
    public function origin_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT origin_data.id,zipcode.zipcode,origin_data.avg,origin_data.cards,origin_data.originZipcode,origin_data.merchants,origin_data.txs,origin_data.date FROM App\Entity\Zipcode zipcode JOIN zipcode.originData origin_data WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "originZipcode", "field" => "originZipcode"],$cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"date" => $actualData['date'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "originZipcode" => $actualData['originZipcode'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

    /**
     * @Route("/all_origin_data_zipcode", name="all_origin_data_zipcode")
     */
    public function all_origin_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT origin_data.id,zipcode.zipcode,origin_data.avg,origin_data.cards,origin_data.originZipcode,origin_data.merchants,origin_data.txs,origin_data.date FROM App\Entity\Zipcode zipcode JOIN zipcode.originData origin_data')->getResult();
        // var_dump($queryData);
        // exit;
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "originZipcode", "field" => "originZipcode"],$cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"date" => $actualData['date'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "originZipcode" => $actualData['originZipcode'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),

        ]);
    }

        /**
     * @Route("/origin_age_data/{zipcode}", name="origin_age_data_zipcode")
     */
    public function origin_age_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT origin_age_data.id,zipcode.zipcode,origin_age_data.avg,origin_age_data.cards,origin_age_data.age,origin_age_data.merchants,origin_age_data.txs,origin_age_data.date,origin_age_data.originZipcode FROM App\Entity\Zipcode zipcode JOIN zipcode.originAgeData origin_age_data WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "age", "field" => "age"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "originZipcode", "field" => "originZipcode"],$cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"date" => $actualData['date'],"age" => $actualData['age'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "originZipcode" => $actualData['originZipcode'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

        /**
     * @Route("/all_origin_age_data_zipcode", name="all_origin_age_data_zipcode")
     */
    public function all_origin_age_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT origin_age_data.id,zipcode.zipcode,origin_age_data.avg,origin_age_data.cards,origin_age_data.age,origin_age_data.merchants,origin_age_data.txs,origin_age_data.date,origin_age_data.originZipcode FROM App\Entity\Zipcode zipcode JOIN zipcode.originAgeData origin_age_data WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "age", "field" => "age"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "originZipcode", "field" => "originZipcode"],$cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"date" => $actualData['date'],"age" => $actualData['age'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "originZipcode" => $actualData['originZipcode'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs']]];
            $cont++;
        }
        $cont = 0;
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),

        ]);
    }

            /**
     * @Route("/origin_gender_data/{zipcode}", name="origin_gender_data_zipcode")
     */
    public function origin_gender_data_zipcode(string $zipcode)
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT genders.id,origin_age_data.date,zipcode.zipcode,origin_age_data.originZipcode,genders.avg,genders.cards,genders.gender,genders.merchants,genders.txs FROM App\Entity\Zipcode zipcode JOIN zipcode.originAgeData origin_age_data JOIN origin_age_data.genders genders WHERE zipcode.zipcode='.$zipcode)->getResult();
        // var_dump($queryData);
        // exit;
        $queryZipCode = $this->getDoctrine()->getManager()->createQuery('SELECT zipcode.zipcode FROM App\Entity\Zipcode zipcode ORDER BY zipcode.zipcode')->getResult();
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "gender", "field" => "gender"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "originZipcode", "field" => "originZipcode"],$cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $zipcodes = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"date" => $actualData['date'],"gender" => $actualData['gender'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "originZipcode" => $actualData['originZipcode'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs']]];
            $cont++;
        }
        $cont = 0;
        foreach ( $queryZipCode as $actualData ){
            $zipcodes+= [$cont => $actualData['zipcode']];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'zipcodes' => $zipcodes,
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),
            'selectedZipcode' => $zipcode,

        ]);
    }

        /**
     * @Route("/all_origin_gender_data_zipcode", name="all_origin_gender_data_zipcode")
     */
    public function all_origin_gender_data_zipcode()
    {
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT genders.id,origin_age_data.date,zipcode.zipcode,origin_age_data.originZipcode,genders.avg,genders.cards,genders.gender,genders.merchants,genders.txs FROM App\Entity\Zipcode zipcode JOIN zipcode.originAgeData origin_age_data JOIN origin_age_data.genders')->getResult();
        // var_dump($queryData);
        // exit;
        $cont = 0;
        $columnDefs = [$cont++ => ["headerName" => "id", "field" => "id"],$cont++ => ["headerName" => "zipcode", "field" => "zipcode"],$cont++ => ["headerName" => "date", "field" => "date"],$cont++ => ["headerName" => "age", "field" => "age"],$cont++ => ["headerName" => "avg", "field" => "avg"],$cont++ => ["headerName" => "cards", "field" => "cards"],$cont++ => ["headerName" => "originZipcode", "field" => "originZipcode"],$cont++ => ["headerName" => "merchants", "field" => "merchants"], $cont++ => ["headerName" => "txs", "field" => "txs"]];  
        $gridOptions = ["defaultColDef"=>["sortable"=>true,"pagination" => false],"columnDefs" => "columnDefs","rowData" => "rowData"];
        $data = [];
        $cont = 0;
        foreach ( $queryData as $actualData ){
            $data +=  [$cont => ["id" => $actualData['id'], "zipcode" => $actualData['zipcode'],"date" => $actualData['date'],"age" => $actualData['age'], "avg" => $actualData['avg'], "cards" => $actualData['cards'], "originZipcode" => $actualData['originZipcode'], "merchants" => $actualData['merchants'], "txs" => $actualData['txs']]];
            $cont++;
        }
        
        return $this->render('/data/data.html.twig', [
            'data' => json_encode($data),
            'columnDefs' => json_encode($columnDefs),
            'gridOptions' => json_encode($gridOptions),

        ]);
    }
}
