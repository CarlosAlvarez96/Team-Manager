<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\SquadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Squad;

#[Route('/game', name: 'app_game')]
class GameController extends AbstractController
{
    #[Route('/all', name: 'game_all', methods: ['GET'])]
    public function getAllGames(EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtiene el repositorio de la entidad Game
        $gameRepository = $entityManager->getRepository(Game::class);

        // Obtiene todos los juegos
        $games = $gameRepository->findAll();

        // Formatea los datos de los juegos para la respuesta
        $formattedGames = [];
        foreach ($games as $game) {
            $formattedGames[] = [
                'id' => $game->getId(),
                'datetime' => $game->getDatetime() ? $game->getDatetime()->format('Y-m-d H:i:s') : null,
                'location' => $game->getLocation(),
                // Puedes incluir otros campos si lo deseas
            ];
        }

        // Devuelve una respuesta JSON con todos los juegos
        return new JsonResponse($formattedGames);
    }

    #[Route('/create', name: 'game_create', methods: ['POST'])]
    public function createGame(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtiene los datos del cuerpo de la solicitud
        $data = json_decode($request->getContent(), true);
    
        // Verifica si se han proporcionado la fecha y la hora del juego
        if (!isset($data['datetime'])) {
            return new JsonResponse(['error' => 'Game datetime is required'], Response::HTTP_BAD_REQUEST);
        }
    
        // Verifica si se ha proporcionado el ID del escuadrón
        if (!isset($data['squad_id'])) {
            return new JsonResponse(['error' => 'Squad ID is required'], Response::HTTP_BAD_REQUEST);
        }
    
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);
    
        // Busca el escuadrón por su ID
        $squad = $squadRepository->find($data['squad_id']);
    
        // Verifica si el escuadrón existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Crea una nueva instancia de la entidad Game
        $game = new Game();
        // Setea la fecha y hora del juego
        $game->setDatetime(new \DateTime($data['datetime']));
        // Setea el escuadrón asociado al juego
        $game->setSquad($squad);
    
        // Verifica si se ha proporcionado la ubicación del juego
        if (isset($data['location'])) {
            $game->setLocation($data['location']);
        }
    
        // Guarda el juego en la base de datos
        $entityManager->persist($game);
        $entityManager->flush();
    
        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'Game created successfully'], Response::HTTP_CREATED);
    }
    
    #[Route('/{id}', name: 'game_get', methods: ['GET'])]
    public function getGameById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtiene el repositorio de la entidad Game
        $gameRepository = $entityManager->getRepository(Game::class);

        // Busca el juego por su ID
        $game = $gameRepository->find($id);

        // Verifica si el juego existe
        if (!$game) {
            return new JsonResponse(['error' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        // Formatea los datos del juego para la respuesta
        $formattedGame = [
            'id' => $game->getId(),
            'datetime' => $game->getDatetime() ? $game->getDatetime()->format('Y-m-d H:i:s') : null,
            'location' => $game->getLocation(),
            // Puedes incluir otros campos si lo deseas
        ];

        // Devuelve una respuesta JSON con el juego encontrado
        return new JsonResponse($formattedGame);
    }
}
