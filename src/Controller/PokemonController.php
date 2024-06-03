<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PokemonController extends AbstractController
{
    private $apiKey = '626f4fe3-2736-458b-8d52-45fbb26c70e1';

    /**
     * @Route("/list", methods={"GET","HEAD"})
     */
    public function listCards(): Response
    {
        $client = new Client();

        try {
            $response = $client->get('https://api.pokemontcg.io/v2/cards?pageSize=50', [
                'headers' => [
                    'X-Api-Key' => $this->apiKey,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            // return new JsonResponse($data);

            if (!isset($data['data'])) {
                throw new Exception('API did not return cards data');
            }

            return $this->render('pokemon/list.html.twig', [
                'cards' => $data['data'],
            ]);
        } catch (Exception $e) {
            return new Response($e->getMessage());
            $this->addFlash('error', 'Error fetching cards: ' . $e->getMessage());
            return $this->redirectToRoute('pokemon_list');
        }
    }

    /**
     * @Route("/details", name="pokemon_details")
     */
    public function pokemon_details(string $cardId): Response
    {
        $client = new Client();

        try {
            $response = $client->get("https://api.pokemontcg.io/v2/cards/$cardId", [
                'headers' => [
                    'X-Api-Key' => $this->apiKey,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            if (!isset($data['data'])) {
                throw new Exception('API did not return card detail');
            }

            return $this->render('pokemon/detail.html.twig', [
                'cardDetail' => $data['data']
            ]);
        } catch (Exception $e) {
            return new Response($e->getMessage());

            $this->addFlash('error', 'Error fetching card detail: ' . $e->getMessage());
            return $this->redirectToRoute('pokemon_list');
        }
    }
}
