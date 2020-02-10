<?php

namespace App\Controller;
use App\Service\Languages;

use App\Entity\BasicData;
use App\Entity\Category;
use App\Entity\CategoryData;
use App\Entity\SubCategory;
use App\Entity\Zipcode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

set_time_limit(0);
ini_set('memory_limit', '-1');
class ExtractController extends AbstractController
{
    private $languages;

    //Este código proviene de BBVA tras registrarme en la página
    private $code = 'YXBwLmJidmEuQUNVOkxQWm9nNDJyRDNWcTI1eGN3ZVBmbkVzbHN4ZThnYlpjbFNiNDJrJERkTHl2YVYxZVRUdm5WbGlSeHFQaVR3SEw=';

    //Enlace al que hacer la petición a la API
    private $link = "https://apis.bbva.com/paystats_sbx/4/zipcodes/";

    private $intervalDate = "min_date=201501&max_date=201512";
    private $cont = 2;

    // Constructor con las variables iniciales
    function __construct() {
        // Lenguajes disponibles en la aplicación
        $languages = new Languages();
        $this->languages=$languages->getLangs();
    } 
    /**
     * @Route("/{_locale}/extract_merchants", name="merchantData")
     */
    public function dataMerchants(Request $request,TranslatorInterface $translator)
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        if ($response->getStatusCode() != 200) {
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
                'status_merchants' => $translator->trans('Error en la consulta getToken').': '. $response->getStatusCode(),
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
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()

            ]);

        } else {
            $decodedResponse = $response->toArray();
            $tokenType = $decodedResponse['token_type'];
            $accessToken = $decodedResponse['access_token'];
            $expiresIn = $decodedResponse['expires_in'];
            //Para controlar cuando a expirado el token
            $expirationTime = time() + $expiresIn;

            //Recojo los mercaderes con sus categorías y subcategorías
            $response = $this->getMerchants($request, $translator, $client, $tokenType, $accessToken, $expirationTime);

            if ($response->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => "0",
                    'status_merchants' => $translator->trans('Error en la consulta get_merchants').': '. $response->getStatusCode(),
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
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()

                ]);

            } else {
                $sql = 'DELETE FROM basic_data';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $sql = 'ALTER TABLE basic_data AUTO_INCREMENT=1;';
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $this->sendMerchants($response, $entityManager);
            }

        }
        return $this->render('/security/administration.html.twig', [
            'status' => $translator->trans('Operación correcta'),
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
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()

        ]);
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

    private function refreshToken($request, $translator, $client, &$tokenType, &$accessToken, &$expirationTime)
    {
        //Lo pongo a 100 segundos para tener margen que no expire el token
        if (($expirationTime - time()) < 100) {
            //Recojo el token inicial para las posteriores consultas
            $response = $this->getToken($client);
            if ($response->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => $translator->trans('Error en la consulta refreshToken').': '. $response->getStatusCode(),
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
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()

                ]);

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

    private function getMerchants($request, $translator, $client, $tokenType, $accessToken, $expirationTime)
    {
        $this->refreshToken($request, $translator, $client, $tokenType, $accessToken, $expirationTime);

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
     * @Route("/{_locale}/extract_data", name="basicData")
     */
    public function dataBasic(Request $request,TranslatorInterface $translator)
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();
        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);
        if ($response->getStatusCode() != 200) {
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
                'status_merchants' => "0",
                'status_basic' => $translator->trans('Error en la consulta post_token').': '. $response->getStatusCode(),
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
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()

            ]);

        } else {
            $decodedResponse = $response->toArray();
            $tokenType = $decodedResponse['token_type'];
            $accessToken = $decodedResponse['access_token'];
            $expiresIn = $decodedResponse['expires_in'];
            //Para controlar cuando a expirado el token
            $expirationTime = time() + $expiresIn;

            $this->getBasicData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime);
            return $this->render('/security/administration.html.twig', [
                'status' => $translator->trans('Operación correcta'),
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
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()

            ]);
        }
    }
    private function getBasicData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        //Conexión con la base de datos
        $conn = $entityManager->getConnection();
        $sqlLogger = $entityManager->getConnection()->getConfiguration()->getSQLLogger();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $this->cont = 5000;
        $responses = [];
        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($request, $translator, $client, $tokenType, $accessToken, $expirationTime);
            $responses[] = $client->request('GET', $this->link . $zipcode->getZipcode() . "/basic_stats?". $this->intervalDate , [
                'buffer' => true,
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        //Si todas las peticiones son válidas elimino los datos
        $sql = 'DELETE FROM basic_data';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $sql = 'ALTER TABLE basic_data AUTO_INCREMENT=1;';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        for ($i = 0, $count = count($zipcodes); $i < $count; $i++) {
            $decodedResponseData = $responses[$i]->toArray()['data'];
            unset($responses[$i]);
            $this->sendBasicData($decodedResponseData, $zipcodes[$i], $entityManager);
        }

        //Una vez que he persistido todos los datos los integro en la base de datos
        $entityManager->flush();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);
    }

    private function sendBasicData($decodedResponseData, $zipcode, $entityManager)
    {
        foreach ($decodedResponseData as $actualData) {
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
                if ($this->cont != 0) {
                    $this->cont = $this->cont - 1;
                } else {
                    $this->cont = 5000;
                    $entityManager->flush();
                }
            }
        }

    }

    /**
     * @Route("/{_locale}/extract_category_data", name="categoryData")
     */
    public function dataCategory(Request $request,TranslatorInterface $translator)
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);
        if ( $response->getStatusCode() != 200){
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
                'status_merchants' => "0",
                'status_basic' => "0",
                'status_category' => $translator->trans('Error en la consulta dataCategory').': '. $response->getStatusCode(),
                'status_upload_category' => "0",
                'status_day_hour' => "0",
                'status_upload_day_hour' => "0",
                'status_destination' => "0",
                'status_upload_destination' => "0",
                'status_origin' => "0",
                'status_upload_origin' => "0",
                'status_origin_age_gender' => "0",
                'status_upload_origin_age_gender' => "0",
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()
    
            ]);
        }

        $decodedResponse = $response->toArray();
        $tokenType = $decodedResponse['token_type'];
        $accessToken = $decodedResponse['access_token'];
        $expiresIn = $decodedResponse['expires_in'];
        //Para controlar cuando a expirado el token
        $expirationTime = time() + $expiresIn;

        $this->getCategoryData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime);
        return $this->render('/security/administration.html.twig', [
            'status' => "0",
            'status_merchants' => "0",
            'status_basic' => "0",
            'status_category' => "0",
            'status_upload_category' => $translator->trans('Recuerde mover los ficheros csv a la carpeta MYSQL antes de subir a base de datos (se puede usar /scripts/copycsv.sh)'),
            'status_day_hour' => "0",
            'status_upload_day_hour' => "0",
            'status_destination' => "0",
            'status_upload_destination' => "0",
            'status_origin' => "0",
            'status_upload_origin' => "0",
            'status_origin_age_gender' => "0",
            'status_upload_origin_age_gender' => "0",
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()

        ]);
    }

    private function getCategoryData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $sqlLogger = $entityManager->getConnection()->getConfiguration()->getSQLLogger();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $responses = [];
        $categoryFile = fopen('../csv/category.csv', 'w');
        fputcsv($categoryFile, ["avg", "cards", "merchants", "txs", "category_id", "zipcode_id", "date"]);
        $this->cont = 5000;
        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($request, $translator,$client , $tokenType, $accessToken, $expirationTime);
            $responses[] = $client->request('GET', $this->link . $zipcode->getZipcode() . "/category_distribution?". $this->intervalDate , [
                'buffer' => true,
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        for ($i = 0, $count = count($zipcodes); $i < $count; $i++) {
            if ($responses[$i]->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => "0",
                    'status_merchants' => "0",
                    'status_basic' => "0",
                    'status_category' => $translator->trans('Error en la consulta getCategoryData').': '. $responses[$i]->getStatusCode() ,
                    'status_upload_category' => "0",
                    'status_day_hour' => "0",
                    'status_upload_day_hour' => "0",
                    'status_destination' => "0",
                    'status_upload_destination' => "0",
                    'status_origin' => "0",
                    'status_upload_origin' => "0",
                    'status_origin_age_gender' => "0",
                    'status_upload_origin_age_gender' => "0",
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()
                ]);
                
            } else {
                $decodedResponseData = $responses[$i]->toArray()['data'];
                unset($responses[$i]);
                $this->sendCategoryData($decodedResponseData, $zipcodes[$i], $entityManager, $categoryFile);
            }
        }
        //Una vez que he persistido todos los datos los integro en la base de datos
        $entityManager->flush();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);

    }
    private function sendCategoryData($decodedResponseData, $zipcode, $entityManager, $categoryFile)
    {
        foreach ($decodedResponseData as $mainData) {
            if (sizeof($mainData) == 6) {
                foreach ($mainData['categories'] as $actualData) {
                    if (sizeof($actualData) != 1) {
                        //En el caso que sean datos filtrados solo me proporcionan 3
                        $categoryId = $this->getDoctrine()
                            ->getRepository(Category::class)
                            ->findOneBy(['code' => $actualData['id']])->getId();

                        if (sizeof($actualData) == 3) {
                            fputcsv($categoryFile, [$actualData['avg'], 0, 0, $actualData['txs'], $categoryId, $zipcode->getId(), $mainData['date']]);
                        } else {
                            fputcsv($categoryFile, [$actualData['avg'], $actualData['cards'], $actualData['merchants'], $actualData['txs'], $categoryId, $zipcode->getId(), $mainData['date']]);
                        }
                    }
                }
            }

        }
    }

    /**
     * @Route("/{_locale}/extract_consumption_data", name="consumptionData")
     */
    public function dataConsumption(Request $request,TranslatorInterface $translator)
    {
        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);
        if ( $response->getStatusCode() != 200){
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
                'status_merchants' => "0",
                'status_basic' => "0",
                'status_category' => "0",
                'status_upload_category' => "0",
                'status_day_hour' => $translator->trans('Error en la consulta dataConsumption').': '. $response->getStatusCode() ,
                'status_upload_day_hour' => "0",
                'status_destination' => "0",
                'status_upload_destination' => "0",
                'status_origin' => "0",
                'status_upload_origin' => "0",
                'status_origin_age_gender' => "0",
                'status_upload_origin_age_gender' => "0",
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()
    
            ]);
        }
        $decodedResponse = $response->toArray();
        $tokenType = $decodedResponse['token_type'];
        $accessToken = $decodedResponse['access_token'];
        $expiresIn = $decodedResponse['expires_in'];
        //Para controlar cuando a expirado el token
        $expirationTime = time() + $expiresIn;

        $this->getConsumptionData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime);
        return $this->render('/security/administration.html.twig', [
            'status' => "0",
            'status_merchants' => "0",
            'status_basic' => "0",
            'status_category' => "0",
            'status_upload_category' => "0",
            'status_day_hour' => "0",
            'status_upload_day_hour' => $translator->trans('Recuerde mover los ficheros csv a la carpeta MYSQL antes de subir a base de datos (se puede usar /scripts/copycsv.sh)'),
            'status_destination' => "0",
            'status_upload_destination' => "0",
            'status_origin' => "0",
            'status_upload_origin' => "0",
            'status_origin_age_gender' => "0",
            'status_upload_origin_age_gender' => "0",
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()
        ]);
    }

    private function getConsumptionData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $sqlLogger = $entityManager->getConnection()->getConfiguration()->getSQLLogger();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $responses = [];
        $this->cont = 5000;

        $dayFile = fopen('../csv/day.csv', 'w');
        $hourFile = fopen('../csv/hour.csv', 'w');

        fputcsv($dayFile, ["id", "zipcode_id", "date", "avg", "day", "max", "min", "merchants", "mode", "std", "txs", "cards"]);
        fputcsv($hourFile, ["id", "day_data_id", "avg", "hour", "max", "min", "merchants", "mode", "std", "txs", "cards"]);

        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($request, $translator,$client , $tokenType, $accessToken, $expirationTime);
            // Realizados cambios en fecha ojo
            $responses[] = $client->request('GET', $this->link . $zipcode->getZipcode() . "/consumption_pattern?". $this->intervalDate , [
                'buffer' => true,
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        $idDay = 1;
        $idHour = 1;
        for ($i = 0, $count = count($zipcodes); $i < $count; $i++) {
            $this->refreshToken($request, $translator,$client , $tokenType, $accessToken, $expirationTime);
            if ($response->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => "0",
                    'status_merchants' => "0",
                    'status_basic' => "0",
                    'status_category' => "0",
                    'status_upload_category' => "0",
                    'status_day_hour' => $translator->trans('Error en la consulta getConsumptionData').': '. $response->getStatusCode(),
                    'status_upload_day_hour' => "0",
                    'status_destination' => "0",
                    'status_upload_destination' => "0",
                    'status_origin' => "0",
                    'status_upload_origin' => "0",
                    'status_origin_age_gender' => "0",
                    'status_upload_origin_age_gender' => "0",
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()
                ]);
        
            } else {
                $decodedResponseData = $responses[$i]->toArray()['data'];
                unset($responses[$i]);
                $this->saveConsumptionData($decodedResponseData, $zipcodes[$i], $entityManager, $idDay, $idHour, $dayFile, $hourFile);
            }
        }
        fclose($dayFile);
        fclose($hourFile);
        //Una vez que he persistido todos los datos los integro en la base de datos
        $entityManager->flush();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);
    }

    private function saveConsumptionData($decodedResponseData, $zipcode, $entityManager, &$idDay, &$idHour, &$dayFile, &$hourFile)
    {
        foreach ($decodedResponseData as $mainData) {
            $actualDate = $mainData['date'];
            if (sizeof($mainData) == 6) {
                foreach ($mainData['days'] as $actualData) {
                    if (sizeof($actualData) == 10) {
                        fputcsv($dayFile, [$idDay++, $zipcode->getId(), $actualDate, $actualData['avg'], $actualData['day'], $actualData['max'],
                            $actualData['min'], $actualData['merchants'], $actualData['mode'], $actualData['std'], $actualData['txs'], $actualData['cards']]);
                        foreach ($actualData['hours'] as $actualHourData) {
                            if (sizeof($actualHourData) == 9) {
                                fputcsv($hourFile, [$idHour++, $idDay - 1, $actualHourData['avg'], $actualHourData['hour'], $actualHourData['max'], $actualHourData['min'], $actualHourData['merchants'], $actualHourData['mode'], $actualHourData['std'], $actualHourData['txs'], $actualHourData['cards']]);
                            }
                        }
                    }
                }

            }
        }

    }

    /**
     * @Route("/{_locale}/extract_destination_data", name="destinationData")
     */
    public function dataDestination(Request $request,TranslatorInterface $translator)
    {
        /*
        /* Dados los clientes de un código postal, listado de otros códigos postales dónde compran
         */

        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);
        if ( $response->getStatusCode() != 200){
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
                'status_merchants' => "0",
                'status_basic' => "0",
                'status_category' => "0",
                'status_upload_category' => "0",
                'status_day_hour' => "0",
                'status_upload_day_hour' => "0",
                'status_destination' => $translator->trans('Error en la consulta dataDestination').': '. $response->getStatusCode(),
                'status_upload_destination' => "0",
                'status_origin' => "0",
                'status_upload_origin' => "0",
                'status_origin_age_gender' => "0",
                'status_upload_origin_age_gender' => "0",
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()
    
            ]);
        }
        $decodedResponse = $response->toArray();
        $tokenType = $decodedResponse['token_type'];
        $accessToken = $decodedResponse['access_token'];
        $expiresIn = $decodedResponse['expires_in'];
        //Para controlar cuando a expirado el token
        $expirationTime = time() + $expiresIn;

        $this->getDestinationData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime);
        return $this->render('/security/administration.html.twig', [
            'status' => "0",
            'status_merchants' => "0",
            'status_basic' => "0",
            'status_category' => "0",
            'status_upload_category' => "0",
            'status_day_hour' => "0",
            'status_upload_day_hour' => "0",
            'status_destination' => "0",
            'status_upload_destination' => $translator->trans('Recuerde mover los ficheros csv a la carpeta MYSQL antes de subir a base de datos (se puede usar /scripts/copycsv.sh)'),
            'status_origin' => "0",
            'status_upload_origin' => "0",
            'status_origin_age_gender' => "0",
            'status_upload_origin_age_gender' => "0",
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()
        ]);
    }

    private function getDestinationData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $responses = [];
        $destinationFile = fopen('../csv/destination.csv', 'w');
        $destinationDataFile = fopen('../csv/destinationData.csv', 'w');
        fputcsv($destinationFile, ["id", "zipcode_id", "avg", "cards", "date", "merchants", "txs"]);
        fputcsv($destinationDataFile, ["destination_id", "avg", "cards", "txs", "merchants", "destination_zipcode"]);
        $this->cont = 5000;
        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($request, $translator,$client , $tokenType, $accessToken, $expirationTime);
            $responses[] = $client->request('GET', $this->link . $zipcode->getZipcode() . "/destination_distribution?". $this->intervalDate ."&destination_type=zipcodes", [
                'buffer' => true,
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        $idDestination = 1;
        for ($i = 0, $count = count($zipcodes); $i < $count; $i++) {
            if ($responses[$i]->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => "0",
                    'status_merchants' => "0",
                    'status_basic' => "0",
                    'status_category' => "0",
                    'status_upload_category' => "0",
                    'status_day_hour' => "0",
                    'status_upload_day_hour' => "0",
                    'status_destination' => $translator->trans('Error en la consulta getDestinationData').': '. $responses[$i]->getStatusCode(),
                    'status_upload_destination' => "0",
                    'status_origin' => "0",
                    'status_upload_origin' => "0",
                    'status_origin_age_gender' => "0",
                    'status_upload_origin_age_gender' => "0",
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()
                ]);
                
            } else {
                $decodedResponseData = $responses[$i]->toArray()['data'];
                unset($responses[$i]);
                $this->sendDestinationData($decodedResponseData, $zipcodes[$i], $destinationFile, $destinationDataFile, $idDestination);
            }
        }
        fclose($destinationFile);
        fclose($destinationDataFile);

    }
    private function sendDestinationData($decodedResponseData, $zipcode, &$destinationFile, &$destinationDataFile, &$idDestination)
    {
        foreach ($decodedResponseData as $mainData) {
            if (sizeof($mainData) == 6) {
                fputcsv($destinationFile, [$idDestination++, $zipcode->getId(), $mainData['avg'], $mainData['cards'], $mainData['date'], $mainData['merchants'], $mainData['cards']]);
                foreach ($mainData['zipcodes'] as $actualData) {
                    //En el caso que sean datos filtrados solo me proporcionan 3
                    if (sizeof($actualData) == 5) {
                        fputcsv($destinationDataFile, [$idDestination - 1, $actualData['avg'], $actualData['cards'], $actualData['txs'], $actualData['merchants'], $actualData['id']]);
                    }
                    if ((sizeof($actualData) == 3)) {
                        fputcsv($destinationDataFile, [$idDestination - 1, $actualData['avg'], 0, $actualData['txs'], 0, $actualData['id']]);
                    }
                }
            }

        }
    }

    /**
     * @Route("/{_locale}/extract_origin_data", name="originData")
     */
    public function dataOrigin(Request $request,TranslatorInterface $translator)
    {
        /*
        /* Dado un código postal, listado de clientes por código postal, que compran en el código postal proporcionado.
         */

        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);

        if ($response->getStatusCode() != 200) {
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
                'status_merchants' => "0",
                'status_basic' => "0",
                'status_category' => "0",
                'status_upload_category' => "0",
                'status_day_hour' => "0",
                'status_upload_day_hour' => "0",
                'status_destination' => "0",
                'status_upload_destination' => "0",
                'status_origin' => $translator->trans('Error en la consulta dataOrigin').': '. $response->getStatusCode(),
                'status_upload_origin' => "0",
                'status_origin_age_gender' => "0",
                'status_upload_origin_age_gender' => "0",
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()
    
            ]);
        }
        $decodedResponse = $response->toArray();
        $tokenType = $decodedResponse['token_type'];
        $accessToken = $decodedResponse['access_token'];
        $expiresIn = $decodedResponse['expires_in'];
        //Para controlar cuando a expirado el token
        $expirationTime = time() + $expiresIn;

        $this->getOriginData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime);
        return $this->render('/security/administration.html.twig', [
            'status' => "0",
            'status_merchants' => "0",
            'status_basic' => "0",
            'status_category' => "0",
            'status_upload_category' => "0",
            'status_day_hour' => "0",
            'status_upload_day_hour' => "0",
            'status_destination' => "0",
            'status_upload_destination' => "0",
            'status_origin' => "0",
            'status_upload_origin' => $translator->trans('Recuerde mover los ficheros csv a la carpeta MYSQL antes de subir a base de datos (se puede usar /scripts/copycsv.sh)'),
            'status_origin_age_gender' => "0",
            'status_upload_origin_age_gender' => "0",
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()
        ]);
        
    }

    private function getOriginData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $responses = [];
        $originFile = fopen('../csv/origin.csv', 'w');
        fputcsv($originFile, ["zipcode_id", "avg", "cards", "origin_zipcode", "merchants", "txs", "date"]);
        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($request, $translator,$client , $tokenType, $accessToken, $expirationTime);
            $responses[] = $client->request('GET', $this->link . $zipcode->getZipcode() . "/origin_distribution?". $this->intervalDate ."&origin_type=zipcodes", [
                'buffer' => true,
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        for ($i = 0, $count = count($zipcodes); $i < $count; $i++) {
            if ($responses[$i]->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => "0",
                    'status_merchants' => "0",
                    'status_basic' => "0",
                    'status_category' => "0",
                    'status_upload_category' => "0",
                    'status_day_hour' => "0",
                    'status_upload_day_hour' => "0",
                    'status_destination' => "0",
                    'status_upload_destination' => "0",
                    'status_origin' => $translator->trans('Error en la consulta getOriginData').': '. $responses[$i]->getStatusCode(),
                    'status_upload_origin' => "0",
                    'status_origin_age_gender' => "0",
                    'status_upload_origin_age_gender' => "0",
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()
                ]);
            } else {
                $decodedResponseData = $responses[$i]->toArray()['data'];
                unset($responses[$i]);
                $this->sendOriginData($decodedResponseData, $zipcodes[$i], $originFile);
            }
        }
        fclose($originFile);

    }
    private function sendOriginData($decodedResponseData, $zipcode, &$originFile)
    {
        foreach ($decodedResponseData as $mainData) {
            if (sizeof($mainData) == 6) {
                foreach ($mainData['zipcodes'] as $actualData) {
                    //En el caso que sean datos filtrados solo me proporcionan 3
                    if (sizeof($actualData) == 5) {
                        fputcsv($originFile, [$zipcode->getId(), $actualData['avg'], $actualData['cards'], $actualData['id'], $actualData['merchants'], $actualData['txs'], $mainData['date']]);
                    }
                    if ((sizeof($actualData) == 3)) {
                        fputcsv($originFile, [$zipcode->getId(), $actualData['avg'], 0, $actualData['id'], 0, $actualData['txs'], $mainData['date']]);
                    }
                }
            }

        }
    }

    /**
     * @Route("/{_locale}/extract_origin_age_gender_data", name="originAgeGenderData")
     */
    public function dataOriginAgeGender(Request $request,TranslatorInterface $translator)
    {
        /*
        /* Dado un código postal, listado de clientes por código postal, que compran en el código postal proporcionado.
         */

        //Entity manager necesario para gestionar las peticiones
        $entityManager = $this->getDoctrine()->getManager();

        //Conexión con la base de datos
        $conn = $entityManager->getConnection();

        //Cliente HTTP para peticiones a la API BBVA
        $client = HttpClient::create();

        //Recojo el token inicial para las posteriores consultas
        $response = $this->getToken($client);
        if ($response->getStatusCode() != 200) {
            return $this->render('/security/administration.html.twig', [
                'status' => "0",
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
                'status_origin_age_gender' => $translator->trans('Error en la consulta dataOriginAgeGender').': '. $response->getStatusCode(),
                'status_upload_origin_age_gender' => "0",
                'languages' => $this->languages , 
                'selectedLanguage' => $request->getLocale()
    
            ]);
        }
        $decodedResponse = $response->toArray();
        $tokenType = $decodedResponse['token_type'];
        $accessToken = $decodedResponse['access_token'];
        $expiresIn = $decodedResponse['expires_in'];
        //Para controlar cuando a expirado el token
        $expirationTime = time() + $expiresIn;

        $this->getOriginAgeGenderData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime);
        return $this->render('/security/administration.html.twig', [
            'status' => "0",
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
            'status_upload_origin_age_gender' => $translator->trans('Recuerde mover los ficheros csv a la carpeta MYSQL antes de subir a base de datos (se puede usar /scripts/copycsv.sh)'),
            'languages' => $this->languages , 
            'selectedLanguage' => $request->getLocale()
        ]);
        
    }

    private function getOriginAgeGenderData($request, $translator, $client, $tokenType, $accessToken, $entityManager, $expirationTime)
    {
        $zipcodes = $this->getDoctrine()
            ->getRepository(Zipcode::class)
            ->findAll();
        $responses = [];
        $originAgeDataFile = fopen('../csv/originAgeData.csv', 'w');
        $originGenderDataFile = fopen('../csv/originGenderData.csv', 'w');
        fputcsv($originAgeDataFile, ["id", "avg", "cards", "age", "merchants", "txs", "zipcode_id", "date", "origin_zipcode"]);
        fputcsv($originGenderDataFile, ["origin_age_data_id", "avg", "cards", "gender", "merchants", "txs"]);
        foreach ($zipcodes as $zipcode) {
            $this->refreshToken($request, $translator, $client , $tokenType, $accessToken, $expirationTime);
            $responses[] = $client->request('GET', $this->link . $zipcode->getZipcode() . "/origin_distribution?". $this->intervalDate ."&origin_type=zipcodes&expand=ages.genders", [
                'buffer' => true,
                'headers' => [
                    'Authorization' => $tokenType . ' ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
        }
        $idAge = 1;
        for ($i = 0, $count = count($zipcodes); $i < $count; $i++) {
            if ($responses[$i]->getStatusCode() != 200) {
                return $this->render('/security/administration.html.twig', [
                    'status' => "0",
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
                    'status_origin_age_gender' => $translator->trans('Error en la consulta getOriginAgeGenderData').': '. $responses[$i]->getStatusCode(),
                    'status_upload_origin_age_gender' => "0",
                    'languages' => $this->languages , 
                    'selectedLanguage' => $request->getLocale()
                ]);
            } else {
                $decodedResponseData = $responses[$i]->toArray()['data'];
                unset($responses[$i]);
                $this->sendOriginAgeGenderData($decodedResponseData, $zipcodes[$i], $originAgeDataFile, $originGenderDataFile, $idAge);
            }
        }
        fclose($originAgeDataFile);
        fclose($originGenderDataFile);

    }
    private function sendOriginAgeGenderData($decodedResponseData, $zipcode, &$originAgeDataFile, &$originGenderDataFile, &$idAge)
    {
        foreach ($decodedResponseData as $mainData) {
            if (sizeof($mainData) == 6) {
                foreach ($mainData['zipcodes'] as $actualData) {
                    if (sizeof($actualData) == 6) {
                        foreach ($actualData['ages'] as $age) {
                            if (sizeof($age) == 6) {
                                fputcsv($originAgeDataFile, [$idAge++, $age['avg'], $age['cards'], $age['id'], $age['merchants'], $age['txs'], $zipcode->getId(), $mainData['date'], $actualData['id']]);
                                foreach ($age['genders'] as $gender) {
                                    //En el caso que sean datos filtrados solo me proporcionan 3
                                    if (sizeof($gender) == 5) {
                                        fputcsv($originGenderDataFile, [$idAge - 1, $gender['avg'], $gender['cards'], $gender['id'], $gender['merchants'], $gender['txs']]);
                                    }
                                    if ((sizeof($gender) == 3)) {
                                        fputcsv($originGenderDataFile, [$idAge - 1, $gender['avg'], 0, $gender['id'], 0, $gender['txs']]);
                                    }
                                }
                            }
                            if (sizeof($age) == 3) {
                                fputcsv($originAgeDataFile, [$idAge++, $age['avg'], 0, $age['id'], 0, $age['txs'], $zipcode->getId(), $mainData['date'], $actualData['id']]);
                            }
                        }
                    }
                }
            }
        }
    }
}
