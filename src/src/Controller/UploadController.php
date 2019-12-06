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
 * @Route("/upload_category_data", name="uploadCategoryData")
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
        return $this->render('base.html.twig');
    }

/**
 * @Route("/upload_day_data", name="uploadDayData")
 */
    public function uploadDaysData()
    {

        $entityManager = $this->getDoctrine()->getManager();
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        // Primero elimino todo el contenido actual en base de datos para volver a rellenar
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
        return $this->render('base.html.twig');
    }

/**
 * @Route("/upload_hour_data", name="uploadHourData")
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
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/upload_destination_data", name="uploadCategoryData")
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

        $sql = 'ALTER TABLE destination_data AUTO_INCREMENT=1;';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'DELETE FROM destination';
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
        return $this->render('base.html.twig');
    }
}
