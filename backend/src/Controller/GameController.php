<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
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
        $gameRepository = $entityManager->getRepository(Game::class);

        $games = $gameRepository->findAll();

        $formattedGames = [];
        foreach ($games as $game) {
            $formattedGames[] = [
                'id' => $game->getId(),
                'datetime' => $game->getDatetime() ? $game->getDatetime()->format('Y-m-d H:i:s') : null,
                'location' => $game->getLocation(),
            ];
        }

        return new JsonResponse($formattedGames);
    }

    #[Route('/create', name: 'game_create', methods: ['POST'])]
    public function createGame(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['datetime'])) {
            return new JsonResponse(['error' => 'Game datetime is required'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!isset($data['squad_id'])) {
            return new JsonResponse(['error' => 'Squad ID is required'], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['team1'])) {
            return new JsonResponse(['error' => 'Squad ID is required'], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['team2'])) {
            return new JsonResponse(['error' => 'Squad ID is required'], Response::HTTP_BAD_REQUEST);
        }
    
        $squadRepository = $entityManager->getRepository(Squad::class);
    
        $squad = $squadRepository->find($data['squad_id']);
    
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }
    
        $game = new Game();
        $game->setDatetime(new \DateTime($data['datetime']));
        $game->setSquad($squad);
        $game->setTeam1($data['team1']);
        $game->setTeam2($data['team2']);
        $game->setPrice($data['price']);
    
        if (isset($data['location'])) {
            $game->setLocation($data['location']);
        }
    
        $entityManager->persist($game);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Game created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{squadId}', name: 'games_by_squad', methods: ['GET'])]
    public function getGamesBySquadId(int $squadId, EntityManagerInterface $entityManager): JsonResponse
    {
        $gameRepository = $entityManager->getRepository(Game::class);
    
        $games = $gameRepository->findAllBySquadId($squadId);
    
        $formattedGames = [];
        foreach ($games as $game) {
            $team1Ids = explode(',', $game->getTeam1());
            $team2Ids = explode(',', $game->getTeam2());
    
            $team1Usernames = [];
            foreach ($team1Ids as $userId) {
                $user = $entityManager->getRepository(User::class)->find($userId);
                if ($user) {
                    $team1Usernames[] = $user->getUsername();
                }
            }
    
            $team2Usernames = [];
            foreach ($team2Ids as $userId) {
                $user = $entityManager->getRepository(User::class)->find($userId);
                if ($user) {
                    $team2Usernames[] = $user->getUsername();
                }
            }
    
            $formattedGames[] = [
                'id' => $game->getId(),
                'datetime' => $game->getDatetime() ? $game->getDatetime()->format('Y-m-d H:i:s') : null,
                'location' => $game->getLocation(),
                'price' => $game->getPrice(),
                'team1' => $team1Usernames, 
                'team2' => $team2Usernames, 
            ];
        }
    
        return new JsonResponse($formattedGames);
    }
}
