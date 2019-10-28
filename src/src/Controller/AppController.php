<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpClient\HttpClient;

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
            $response = $client->request('GET', 'https://apis.bbva.com/paystats_sbx/4/info/merchants_categories',[
                'headers' => [
                    'Authorization'=> $decodedResponse['token_type'].' '.$decodedResponse['access_token'],
                    'Accept' => 'application/json'
                ]
            ]);
            if($statusCode = $response->getStatusCode() != 200){
                echo "Error en la consulta get_merchants: ".$statusCode = $response->getStatusCode();
                
            }else{
                $decodedResponse = $response->toArray();//As array
                var_dump(json_decode($response->getContent())->data[0]-Z);
                $response->getContent();//As JSON
                
            }

        }
        return $this->render('base.html.twig');
    }
}
