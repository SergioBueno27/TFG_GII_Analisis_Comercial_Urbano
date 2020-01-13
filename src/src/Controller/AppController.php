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
        $queryData = $this->getDoctrine()->getManager()->createQuery('SELECT category_data.id,zipcode.zipcode,category_data.avg,category_data.cards,category_data.merchants,
        category_data.txs,zipcode.zipcode,category.code,category.description,category_data.date FROM App\Entity\Zipcode zipcode 
        JOIN zipcode.dayData day_data WHERE zipcode.zipcode='.$zipcode)->getResult();
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

}
