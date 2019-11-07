<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\Category;
use App\Entity\SubCategory;

class AppController extends AbstractController
{
    //Este código proviene de BBVA tras registrarme en la página
    private $code = 'YXBwLmJidmEuSGZqZmJmYjpmJXVORlN0cUlFVFRqWmVwRUdPKldqYjVxeUpKUkskeklXa01yVkk5dEsqbTI3ZTlFWmc3QFdYUUg4eE1QJEZH';

    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/extract_merchants", name="merchantData")
     */
    public function dataMerchants()
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn=$entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        if($statusCode = $response->getStatusCode() != 200){
            echo "Error en la consulta post_token: ".$statusCode = $response->getStatusCode();
            
        }else{
            $decodedResponse = $response->toArray();
            $tokenType=$decodedResponse['token_type'];
            $accessToken=$decodedResponse['access_token'];

            //Recojo los mercaderes con sus categorías y subcategorías
            $response = $this->getMerchants($client,$tokenType,$accessToken);

            if($statusCode = $response->getStatusCode() != 200){
                echo "Error en la consulta get_merchants: ".$statusCode = $response->getStatusCode();
                
            }else{

                //Primero elimino todo el contenido actual en base de datos para volver a rellenar
                $sql = 'DELETE FROM sub_category';
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $sql = 'DELETE FROM category';
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                //Reinicio el valor del id auto incremental
                $sql = 'ALTER TABLE sub_category AUTO_INCREMENT=1;';
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $sql = 'ALTER TABLE category AUTO_INCREMENT=1;';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                
                $this->sendMerchants($response,$entityManager);
            }

        }
        return $this->render('base.html.twig');
    }

    private function getToken($client){

        $response = $client->request('POST', 'https://connect.bbva.com/token?grant_type=client_credentials',[
            'headers' => [
                'Content_Type' => 'application/json',
                'Authorization' => 'Basic '.$this->code
            ]
        ]);
        return $response;
    }

    private function getMerchants($client,$tokenType,$accessToken){
        $response = $client->request('GET', 'https://apis.bbva.com/paystats_sbx/4/info/merchants_categories',[
            'headers' => [
                'Authorization'=> $tokenType.' '.$accessToken,
                'Accept' => 'application/json'
            ]
        ]);
        return $response;
    }

    private function sendMerchants($response,$entityManager){
        //A partir de aquí recorro las categorías y subcategorías y las almaceno en la base de datos
        $categorias=json_decode($response->getContent())->data[0];
        foreach($categorias->categories as $actualCategory){
            $category = new Category();
            $category->setCode($actualCategory->code);
            $category->setDescription($actualCategory->description);
            $entityManager->persist($category);
            foreach($actualCategory->subcategories as $actualSubCategory){
                $subCategory = new SubCategory();
                $subCategory->setCode($actualSubCategory->code);
                $subCategory->setDescription($actualSubCategory->description);
                $subCategory->setCategory($category);
                $entityManager->persist($subCategory);
            }
        }
        $entityManager->flush();
        var_dump($categorias->categories);
    }

     /**
     * @Route("/extract_data", name="basicData")
     */
    public function dataBasic(){
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn=$entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        
        return $this->render('base.html.twig');
    }
}
