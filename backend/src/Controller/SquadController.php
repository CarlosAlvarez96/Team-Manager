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
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Obtiene todas las escuadras
        $squads = $squadRepository->findAll();

        // Formatea los datos de las escuadras para la respuesta
        $formattedSquads = [];
        foreach ($squads as $squad) {
            $formattedSquads[] = [
                'id' => $squad->getId(),
                'name' => $squad->getName(),
                // Puedes incluir otros campos si lo deseas
            ];
        }

        // Devuelve una respuesta JSON con todas las escuadras
        return new JsonResponse($formattedSquads);
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

        $entityManager->persist($squad);
        $entityManager->flush();

        // Devuelve la respuesta JSON con el ID del escuadrón creado
        return new JsonResponse(['message' => 'Squad created successfully', 'squadId' => $squad->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'squad_get', methods: ['GET'])]
    public function getSquadById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Busca la escuadra por su ID
        $squad = $squadRepository->find($id);

        // Verifica si la escuadra existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        // Formatea los datos de la escuadra para la respuesta
        $formattedSquad = [
            'id' => $squad->getId(),
            'name' => $squad->getName(),
            // Puedes incluir otros campos si lo deseas
        ];

        // Devuelve una respuesta JSON con la escuadra encontrada
        return new JsonResponse($formattedSquad);
    }
    
    #[Route('/{id}', name: 'squad_update', methods: ['PUT'])]
    public function updateSquad(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Busca la escuadra por su ID
        $squad = $squadRepository->find($id);

        // Verifica si la escuadra existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        // Obtiene los datos del cuerpo de la solicitud
        $data = json_decode($request->getContent(), true);

        // Verifica si se ha proporcionado el nombre de la escuadra
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Squad name is required'], Response::HTTP_BAD_REQUEST);
        }

        // Actualiza el nombre de la escuadra
        $squad->setName($data['name']);

        // Guarda los cambios en la base de datos
        $entityManager->flush();

        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'Squad updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'squad_delete', methods: ['DELETE'])]
    public function deleteSquad(int $id, EntityManagerInterface $entityManager): Response
    {
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Busca la escuadra por su ID
        $squad = $squadRepository->find($id);

        // Verifica si la escuadra existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        // Elimina la escuadra de la base de datos
        $entityManager->remove($squad);
        $entityManager->flush();

        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'Squad deleted successfully'], Response::HTTP_OK);
    }
    #[Route('/{id}/addUser/{userId}', name: 'squad_add_user', methods: ['POST'])]
    public function addUserToSquad(int $id, int $userId, EntityManagerInterface $entityManager): Response
    {
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Busca la escuadra por su ID
        $squad = $squadRepository->find($id);

        // Verifica si la escuadra existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        // Busca el usuario por su ID
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        // Verifica si el usuario existe
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Asocia el usuario con la escuadra
        $squad->addUser($user);

        // Guarda los cambios en la base de datos
        $entityManager->flush();

        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'User added to squad successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}/removeUser/{userId}', name: 'squad_remove_user', methods: ['POST'])]
    public function removeUserFromSquad(int $id, int $userId, EntityManagerInterface $entityManager): Response
    {
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Busca la escuadra por su ID
        $squad = $squadRepository->find($id);

        // Verifica si la escuadra existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        // Busca el usuario por su ID
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        // Verifica si el usuario existe
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Desasocia el usuario de la escuadra
        $squad->removeUser($user);

        // Guarda los cambios en la base de datos
        $entityManager->flush();

        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'User removed from squad successfully'], Response::HTTP_OK);
    }
    #[Route('/squads/{userId}', name: 'user_squads', methods: ['GET'])]
    public function getUserSquads(int $userId, SquadRepository $squadRepository, LoggerInterface $logger): JsonResponse
    {
        // Log para depuración
        $logger->info("Fetching squads for user ID: " . $userId);

        try {
            // Buscar los escuadrones asociados al usuario usando el método personalizado
            $squads = $squadRepository->findByUserId($userId);

            

            // Formatear los datos de los escuadrones para la respuesta
            $formattedSquads = [];
            foreach ($squads as $squad) {
                $formattedSquads[] = [
                    'id' => $squad->getId(),
                    'name' => $squad->getName(),
                    // Puedes incluir otros campos si lo deseas
                ];
            }

            // Devolver una respuesta JSON con los escuadrones asociados al usuario
            return new JsonResponse($formattedSquads);
        } catch (\Exception $e) {
            // Log del error
            $logger->error("Error fetching squads: " . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

}
