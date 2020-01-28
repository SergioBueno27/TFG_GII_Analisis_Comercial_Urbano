<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

set_time_limit(0);
ini_set('memory_limit', '-1');
class UploadController extends AbstractController
{
/**
 * @Route("/{_locale}/upload_category_data", name="uploadCategoryData")
 */
    public function uploadCategoryData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar
        $sql = 'DELETE FROM category_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE category_data AUTO_INCREMENT=1;';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        //Subo el fichero a base de datos desde mi carpeta
        $sql = "LOAD DATA INFILE 'category.csv'
        INTO TABLE Proyecto.category_data
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        (avg,@vcards,@vmerchants,txs,category_id,zipcode_id,date)
        SET cards = nullif(@vcards,0),merchants = nullif(@vmerchants,0)
        ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $this->render('/security/administration.html.twig', [
            'status' => "Operación correcta",
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
 * @Route("/{_locale}/upload_day_data", name="uploadDayData")
 */
    public function uploadDaysData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar
        $sql = 'DELETE FROM hour_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'DELETE FROM day_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        //Subo el fichero a base de datos desde mi carpeta
        $sql = "LOAD DATA INFILE 'day.csv'
        INTO TABLE Proyecto.day_data
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $this->render('/security/administration.html.twig', [
            'status' => "Operación correcta",
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
 * @Route("/{_locale}/upload_hour_data", name="uploadHourData")
 */
    public function uploadHoursData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar
        $sql = 'DELETE FROM hour_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        //Subo el fichero a base de datos desde mi carpeta
        $sql = "LOAD DATA INFILE 'hour.csv'
        INTO TABLE Proyecto.hour_data
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        (id,day_data_id,avg,hour,max,min,merchants,mode,std,txs,cards)
        ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $this->render('/security/administration.html.twig', [
            'status' => "Operación correcta",
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
     * @Route("/{_locale}/upload_destination_data", name="uploadDestinationData")
     */
    public function uploadDestinationData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar
        $sql = 'DELETE FROM destination_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE destination_data AUTO_INCREMENT=1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'DELETE FROM destination';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE destination AUTO_INCREMENT=1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        //Subo el fichero a base de datos desde mi carpeta
        $sql = "LOAD DATA INFILE 'destination.csv'
        INTO TABLE Proyecto.destination
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = "LOAD DATA INFILE 'destinationData.csv'
        INTO TABLE Proyecto.destination_data
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        (destination_id,avg,@vcards,txs,@vmerchants,destination_zipcode)
        SET cards = nullif(@vcards,0),merchants = nullif(@vmerchants,0)
        ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // fputcsv($destinationDataFile, ["destination_id","avg","cards","txs","merchants","destination_zipcode"]);
        return $this->render('/security/administration.html.twig', [
            'status' => "Operación correcta",
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
     * @Route("/{_locale}/upload_origin_data", name="uploadOriginData")
     */
    public function uploadOriginData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar
        $sql = 'DELETE FROM origin_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE origin_data AUTO_INCREMENT=1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        //Subo el fichero a base de datos desde mi carpeta
        $sql = "LOAD DATA INFILE 'origin.csv'
        INTO TABLE Proyecto.origin_data
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        (zipcode_id,avg,cards,origin_zipcode,merchants,txs,date)
        ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $this->render('/security/administration.html.twig', [
            'status' => "Operación correcta",
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
     * @Route("/{_locale}/upload_origin_age_gender_data", name="uploadOriginAgeGenderData")
     */
    public function uploadOriginAgeGenderData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar

        $sql = 'DELETE FROM origin_gender_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE origin_gender_data AUTO_INCREMENT=1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'DELETE FROM origin_age_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        //Subo el fichero a base de datos desde mi carpeta
        $sql = "LOAD DATA INFILE 'originAgeData.csv'
         INTO TABLE Proyecto.origin_age_data
         FIELDS TERMINATED BY ','
         LINES TERMINATED BY '\n'
         IGNORE 1 LINES
         (id, avg, @vcards, age, @vmerchants, txs, zipcode_id, date, origin_zipcode)
         SET cards = nullif(@vcards,0),merchants = nullif(@vmerchants,0)
         ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = "LOAD DATA INFILE 'originGenderData.csv'
         INTO TABLE Proyecto.origin_gender_data
         FIELDS TERMINATED BY ','
         LINES TERMINATED BY '\n'
         IGNORE 1 LINES
         (origin_age_data_id, avg, @vcards, gender, @vmerchants, txs)
         SET cards = nullif(@vcards,0),merchants = nullif(@vmerchants,0)
         ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $this->render('/security/administration.html.twig', [
            'status' => "Operación correcta",
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
}
