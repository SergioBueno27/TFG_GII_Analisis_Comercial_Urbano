<?php

namespace App\Controller;

use App\Entity\BasicData;
use App\Entity\Category;
use App\Entity\CategoryData;
use App\Entity\SubCategory;
use App\Entity\Zipcode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

set_time_limit(0);
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
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        if ($statusCode = $response->getStatusCode() != 200) {
            echo "Error en la consulta post_token: " . $statusCode = $response->getStatusCode();

        } else {
            $decodedResponse = $response->toArray();
            $tokenType = $decodedResponse['token_type'];
            $accessToken = $decodedResponse['access_token'];
            $expiresIn = $decodedResponse['expires_in'];
            //Para controlar cuando a expirado el token
            $expirationTime = time() + $expiresIn;

            //Recojo los mercaderes con sus categorías y subcategorías
            $response = $this->getMerchants($client, $tokenType, $accessToken, $expirationTime);

            if ($statusCode = $response->getStatusCode() != 200) {
                echo "Error en la consulta get_merchants: " . $statusCode = $response->getStatusCode();

            } else {
                //Primero elimino todo el contenido actual en base de datos para volver a rellenar
                $sql = 'DELETE FROM category_data';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $sql = 'ALTER TABLE category_data AUTO_INCREMENT=1;';
                $stmt = $conn->prepare($sql);
                $stmt->execute();

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

                $this->sendMerchants($response, $entityManager);
            }

        }
        return $this->render('base.html.twig');
    }

    private function getToken($client)
    {

        $response = $client->request('POST', 'https://connect.bbva.com/token?grant_type=client_credentials', [
            'headers' => [
                'Content_Type' => 'application/json',
                'Authorization' => 'Basic ' . $this->code,
            ],
        ]);
        return $response;
    }

    private function refreshToken($client, &$tokenType, &$accessToken, &$expirationTime)
    {
        //Lo pongo a 100 segundos para tener margen que no expire el token
        if (($expirationTime - time()) < 100) {
            //Recojo el token inicial para las posteriores consultas
            $response = $this->getToken($client);
            if ($statusCode = $response->getStatusCode() != 200) {
                echo "Error en la consulta get_token en refreshToken: " . $statusCode = $response->getStatusCode();
                exit;

            } else {
                $decodedResponse = $response->toArray();
                $tokenType = $decodedResponse['token_type'];
                $accessToken = $decodedResponse['access_token'];
                $expiresIn = $decodedResponse['expires_in'];
                //Para controlar cuando a expirado el token
                $expirationTime = time() + $expiresIn;
            }
        }

    }

    private function getMerchants($client, $tokenType, $accessToken, $expirationTime)
    {
        $this->refreshToken($client, $tokenType, $accessToken, $expirationTime);

        $response = $client->request('GET', 'https://apis.bbva.com/paystats_sbx/4/info/merchants_categories', [
            'headers' => [
                'Authorization' => $tokenType . ' ' . $accessToken,
                'Accept' => 'application/json',
            ],
        ]);
        return $response;
    }

    private function sendMerchants($response, $entityManager)
    {
        //Primero añado la categoría "filtered" que emplea más adelante como categoría filtered en caso que contega información sensibleç
        $category = new Category();
        $category->setCode('filtered');
        $category->setDescription('Filtered data by BBVA');
        $entityManager->persist($category);
        $entityManager->flush();
        //A partir de aquí recorro las categorías y subcategorías y las almaceno en la base de datos
        $categorias = json_decode($response->getContent())->data[0];
        foreach ($categorias->categories as $actualCategory) {
            $category = new Category();
            $category->setCode($actualCategory->code);
            $category->setDescription($actualCategory->description);
            $entityManager->persist($category);
            foreach ($actualCategory->subcategories as $actualSubCategory) {
                $subCategory = new SubCategory();
                $subCategory->setCode($actualSubCategory->code);
                $subCategory->setDescription($actualSubCategory->description);
                $subCategory->setCategory($category);
                $entityManager->persist($subCategory);
            }
        }
        $entityManager->flush();
    }

    /**
     * @Route("/extract_data", name="basicData")
     */
    public function dataBasic()
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        if ($statusCode = $response->getStatusCode() != 200) {
            echo "Error en la consulta get_token en extraer datos básicos: " . $statusCode = $response->getStatusCode();
            exit;

        } else {
            //Primero elimino todo el contenido actual en base de datos para volver a rellenar
            $sql = 'DELETE FROM basic_data';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $sql = 'ALTER TABLE basic_data AUTO_INCREMENT=1;';
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $decodedResponse = $response->toArray();
            $tokenType = $decodedResponse['token_type'];
            $accessToken = $decodedResponse['access_token'];
            $expiresIn = $decodedResponse['expires_in'];
            //Para controlar cuando a expirado el token
            $expirationTime = time() + $expiresIn;

            $this->getBasicData($client, $tokenType, $accessToken, $entityManager, $expirationTime);
        }
        return $this->render('base.html.twig');
    }
    private function getBasicData($client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $responses = [];
        foreach ($zipcodes as $zipcode) {
            $client = HttpClient::create();
            $this->refreshToken($client, $tokenType, $accessToken, $expirationTime);
            $responses[] = $client->request('GET', "https://apis.bbva.com/paystats_sbx/4/zipcodes/" . $zipcode->getZipcode() . "/basic_stats?min_date=201501&max_date=201512", [
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        foreach ($responses as $response) {
            $decodedResponse = $response->toArray();
            $this->sendBasicData($decodedResponse, $zipcode, $entityManager);
        }
        //Una vez que he persistido todos los datos los integro en la base de datos
        $entityManager->flush();
    }

    private function sendBasicData($decodedResponse, $zipcode, $entityManager)
    {
        foreach ($decodedResponse['data'] as $actualData) {
            if (sizeof($actualData) != 1) {
                $basicData = new BasicData();
                $basicData->setAvg($actualData['avg']);
                $basicData->setCards($actualData['cards']);
                $basicData->setDate($actualData['date']);
                $basicData->setMax($actualData['max']);
                $basicData->setMerchants($actualData['merchants']);
                $basicData->setMin($actualData['min']);
                $basicData->setPeakTxsDay($actualData['peak_txs_day']);
                $basicData->setPeakTxsHour($actualData['peak_txs_hour']);
                $basicData->setStd($actualData['std']);
                $basicData->setTxs($actualData['txs']);
                $basicData->setValleyTxsDay($actualData['valley_txs_day']);
                $basicData->setValleyTxsHour($actualData['valley_txs_hour']);
                $basicData->setZipcode($zipcode);
                $entityManager->persist($basicData);
            }
        }
        $entityManager->flush();

    }

    /**
     * @Route("/extract_category_data", name="categoryData")
     */
    public function dataCategory()
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        if ($statusCode = $response->getStatusCode() != 200) {
            echo "Error en la consulta post_token: " . $statusCode = $response->getStatusCode();
        } else {
            //Primero elimino todo el contenido actual en base de datos para volver a rellenar
            $sql = 'DELETE FROM category_data';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $sql = 'ALTER TABLE category_data AUTO_INCREMENT=1;';
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $decodedResponse = $response->toArray();
            $tokenType = $decodedResponse['token_type'];
            $accessToken = $decodedResponse['access_token'];
            $expiresIn = $decodedResponse['expires_in'];
            //Para controlar cuando a expirado el token
            $expirationTime = time() + $expiresIn;

            $this->getCategoryData($client, $tokenType, $accessToken, $entityManager, $expirationTime);
        }
        return $this->render('base.html.twig');
    }
    private function getCategoryData($client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($client, $tokenType, $accessToken, $expirationTime);
            // Realizados cambios en fecha ojo
            $response = $client->request('GET', "https://apis.bbva.com/paystats_sbx/4/zipcodes/" . $zipcode->getZipcode() . "/category_distribution?min_date=201501&max_date=201501", [
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
            if ($statusCode = $response->getStatusCode() != 200) {
                echo "Error en la consulta post_token: " . $statusCode = $response->getStatusCode();

            } else {
                $decodedResponse = $response->toArray();
                $this->sendCategoryData($decodedResponse, $zipcode, $entityManager);

            }
        }
    }
    private function sendCategoryData($decodedResponse, $zipcode, $entityManager)
    {
        foreach ($decodedResponse['data'] as $mainData) {
            $actualDate = $mainData['date'];
            if (sizeof($mainData) == 6) {
                foreach ($mainData['categories'] as $actualData) {
                    if (sizeof($actualData) != 1) {
                        //En el caso que sean datos filtrados solo me proporcionan 3
                        $categoryId = $this->getDoctrine()
                            ->getRepository(Category::class)
                            ->findOneBy(['code' => $actualData['id']]);

                        if (sizeof($actualData) == 3) {
                            $categoryData = new CategoryData();
                            $categoryData->setZipcode($zipcode);
                            $categoryData->setDate($actualDate);
                            $categoryData->setCategoryId($categoryId);
                            $categoryData->setAvg($actualData['avg']);
                            $categoryData->setTxs($actualData['txs']);
                            $entityManager->persist($categoryData);
                        } else {
                            $categoryData = new CategoryData();
                            $categoryData->setZipcode($zipcode);
                            $categoryData->setDate($actualDate);
                            $categoryData->setCategoryId($categoryId);
                            $categoryData->setAvg($actualData['avg']);
                            $categoryData->setCards($actualData['cards']);
                            $categoryData->setMerchants($actualData['merchants']);
                            $categoryData->setTxs($actualData['txs']);
                            $entityManager->persist($categoryData);
                        }
                    }
                }
            }

        }
        $entityManager->flush();

    }
}
