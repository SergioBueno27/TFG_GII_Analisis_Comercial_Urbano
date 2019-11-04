<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\Category;
use App\Entity\SubCategory;

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
     * @Route("/extract_data", name="datos")
     */
    public function data()
    {
        //Este código proviene de BBVA tras registrarme en la página
        $code = 'YXBwLmJidmEuSGZqZmJmYjpmJXVORlN0cUlFVFRqWmVwRUdPKldqYjVxeUpKUkskeklXa01yVkk5dEsqbTI3ZTlFWmc3QFdYUUg4eE1QJEZH';
        
        $client = HttpClient::create();

        $entityManager = $this->getDoctrine()->getManager();
        $conn=$entityManager->getConnection();

        $response = $client->request('POST', 'https://connect.bbva.com/token?grant_type=client_credentials',[
            'headers' => [
                'Content_Type' => 'application/json',
                'Authorization' => 'Basic '.$code
            ]
        ]);
        if($statusCode = $response->getStatusCode() != 200){
            echo "Error en la consulta post_token: ".$statusCode = $response->getStatusCode();
            
        }else{
            $decodedResponse = $response->toArray();
            $tokenType=$decodedResponse['token_type'];
            $accessToken=$decodedResponse['access_token'];
            $response = $client->request('GET', 'https://apis.bbva.com/paystats_sbx/4/info/merchants_categories',[
                'headers' => [
                    'Authorization'=> $tokenType.' '.$decodedResponse['access_token'],
                    'Accept' => 'application/json'
                ]
            ]);
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

        }
        return $this->render('base.html.twig');
    }
}
