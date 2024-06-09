<?php

namespace App\Controller;

use App\Entity\Squad;
use App\Repository\SquadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;


use App\Entity\User;
#[Route('/squad', name: 'app_squad')]
class SquadController extends AbstractController
{
    #[Route('/all', name: 'squad_all', methods: ['GET'])]
    public function getAllSquads(EntityManagerInterface $entityManager): JsonResponse
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squads = $squadRepository->findAll();

        $formattedSquads = [];
        foreach ($squads as $squad) {
            $formattedSquads[] = [
                'id' => $squad->getId(),
                'name' => $squad->getName(),
            ];
        }

        return new JsonResponse($formattedSquads);
    }
   

    #[Route('/{id}/updateMoney', name: 'squad_update_money', methods: ['POST'])]
    public function updateSquadMoney(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['money'])) {
            return new JsonResponse(['error' => 'Money value is required'], Response::HTTP_BAD_REQUEST);
        }

        $squad->setMoney($data['money']);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Squad money updated successfully'], Response::HTTP_OK);
    }


    #[Route('/create/{userId}', name: 'squad_create', methods: ['POST'])]
    public function createSquad(int $userId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Squad name is required'], Response::HTTP_BAD_REQUEST);
        }

        $squad = new Squad();
        $squad->setName($data['name']);

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        $squad->addUser($user);
        $squad->setManager($user);
        $squad->setMoney(0);
        
        $entityManager->persist($squad);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Squad created successfully', 'squadId' => $squad->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'squad_get', methods: ['GET'])]
    public function getSquadById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $formattedSquad = [
            'id' => $squad->getId(),
            'name' => $squad->getName(),
            'manager' => $squad->getManager(),
            'money' => $squad->getMoney()
        ];

        return new JsonResponse($formattedSquad);
    }
    
    #[Route('/{id}', name: 'squad_update', methods: ['PUT'])]
    public function updateSquad(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Squad name is required'], Response::HTTP_BAD_REQUEST);
        }

        $squad->setName($data['name']);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Squad updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'squad_delete', methods: ['DELETE'])]
    public function deleteSquad(int $id, EntityManagerInterface $entityManager): Response
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($squad);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Squad deleted successfully'], Response::HTTP_OK);
    }
    #[Route('/{id}/addUser/{userId}', name: 'squad_add_user', methods: ['POST'])]
    public function addUserToSquad(int $id, int $userId, EntityManagerInterface $entityManager): Response
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $squad->addUser($user);

        $entityManager->flush();

        return new JsonResponse(['message' => 'User added to squad successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}/removeUser/{userId}', name: 'squad_remove_user', methods: ['DELETE'])]
    public function removeUserFromSquad(int $id, int $userId, EntityManagerInterface $entityManager): Response
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $squad->removeUser($user);

        $entityManager->flush();

        return new JsonResponse(['message' => 'User removed from squad successfully'], Response::HTTP_OK);
    }
    #[Route('/squads/{userId}', name: 'user_squads', methods: ['GET'])]
    public function getUserSquads(int $userId, SquadRepository $squadRepository, LoggerInterface $logger): JsonResponse
    {
        $logger->info("Fetching squads for user ID: " . $userId);

        try {
            $squads = $squadRepository->findByUserId($userId);

            

            $formattedSquads = [];
            foreach ($squads as $squad) {
                $formattedSquads[] = [
                    'id' => $squad->getId(),
                    'name' => $squad->getName(),
                    'manager' => $squad->getManager()
                ];
            }

            return new JsonResponse($formattedSquads);
        } catch (\Exception $e) {
            $logger->error("Error fetching squads: " . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

}
