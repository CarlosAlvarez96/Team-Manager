<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Squad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\IndividualStats;
use App\Repository\IndividualStatsRepository;
use App\Repository\UserRepository;



#[Route('/user', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('/me', name: 'user_me', methods: ['GET'])]
    public function getMe(Request $request, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new JsonResponse(['error' => 'Token not found'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $matches[1];

        try {
            $decodedToken = $jwtManager->parse($token);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!isset($decodedToken['email'])) {
            return new JsonResponse(['error' => 'Invalid token payload'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $email = $decodedToken['email'];

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $formattedUser = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        return new JsonResponse($formattedUser);
    }

    #[Route('/all', name: 'user_all', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $userRepository = $entityManager->getRepository(User::class);

        $users = $userRepository->findAll();

        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword()
            ];
        }

        return new JsonResponse($formattedUsers);
    }

    #[Route('/multiple', name: 'user_get_multiple', methods: ['GET'])]
    public function getUsersByIds(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $playerIds = $request->query->get('ids');

        $playerIdsArray = explode(',', $playerIds);

        $userRepository = $entityManager->getRepository(User::class);

        $users = $userRepository->findBy(['id' => $playerIdsArray]);

        if (count($users) !== count($playerIdsArray)) {
            return new JsonResponse(['error' => 'One or more users not found'], Response::HTTP_NOT_FOUND);
        }

        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername()
            ];
        }

        return new JsonResponse($formattedUsers);
    }
    #[Route('/{id}', name: 'user_get', methods: ['GET'])]
    public function getUserById(int $id, EntityManagerInterface $entityManager): JsonResponse

    {
        $userRepository = $entityManager->getRepository(User::class);

        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $formattedUser = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'username' => $user->getUsername()
        ];

        return new JsonResponse($formattedUser);
    }
    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['username'])) {
            return new JsonResponse(['error' => 'Email, username, and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);

        $encodedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($encodedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $stats = new IndividualStats();
        $stats->setShooting(0);
        $stats->setPhysical(0);
        $stats->setDefending(0);
        $stats->setDribbling(0);
        $stats->setPassing(0);
        $stats->setUser($user);

        $entityManager->persist($stats);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully and individual stats created'], Response::HTTP_CREATED);
    }


    #[Route('/api/getuserinfo', name: 'app_get_user_info', methods: ['POST'])]
    public function getUserInfo(SerializerInterface $serializerInterface, JWTTokenManagerInterface $jwtManagerInterface, TokenStorageInterface $tokenStorageInterface, UserRepository $userRepository): Response
    {
        $decodedToken = $jwtManagerInterface->decode($tokenStorageInterface->getToken());
        $username = $decodedToken['username'];
        $user = $userRepository->findOneBy(['username' => $username]);
        $response =  $serializerInterface->serialize([
            'username' => $user->getUsername(),
            'id' => $user->getId(),
        ], 'json');
 
        return new JsonResponse($response, 200, [
            'Content-Type' => 'application/json',
        ], true);
    }
    #[Route('/squad/{squadId}', name: 'user_get_by_squad', methods: ['GET'])]
    public function getUsersBySquad(int $squadId, EntityManagerInterface $entityManager): JsonResponse
    {
        $squadRepository = $entityManager->getRepository(Squad::class);

        $squad = $squadRepository->find($squadId);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $users = $squad->getUser();

        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
            ];
        }

        return new JsonResponse($formattedUsers);
    }
        
    #[Route('/{id}/addUserByEmail', name: 'squad_add_user_by_email', methods: ['POST'])]
    public function addUserToSquadByEmail(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneByEmail($data['email']);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $squad = $entityManager->getRepository(Squad::class)->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $squad->addUser($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User added to squad successfully'], Response::HTTP_OK);
    }
    #[Route('/{id}/removeUserByEmail', name: 'squad_remove_user_by_email', methods: ['POST'])]
    public function removeUserFromSquadByEmail(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneByEmail($data['email']);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $squad = $entityManager->getRepository(Squad::class)->find($id);

        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], Response::HTTP_NOT_FOUND);
        }

        $squad->removeUser($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User removed from squad successfully'], Response::HTTP_OK);
    }
    #[Route('/deleteAll', name: 'delete_all_users', methods: ['DELETE'])]
    public function deleteAllUsers(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        
        foreach ($users as $user) {
            $entityManager->remove($user);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'All users have been deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete_user_by_id', methods: ['DELETE'])]
    public function deleteUserById(int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User with ID ' . $id . ' has been deleted successfully'], Response::HTTP_OK);
    }
}
