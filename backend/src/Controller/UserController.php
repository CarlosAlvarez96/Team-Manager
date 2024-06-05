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
        // Obtiene el token desde la cabecera de la solicitud
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new JsonResponse(['error' => 'Token not found'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $matches[1];

        // Decodifica el token JWT
        try {
            $decodedToken = $jwtManager->parse($token);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Verifica que el token tenga un campo de email
        if (!isset($decodedToken['email'])) {
            return new JsonResponse(['error' => 'Invalid token payload'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $email = $decodedToken['email'];

        // Busca el usuario en la base de datos utilizando el email
        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Formatea los datos del usuario para la respuesta
        $formattedUser = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        // Devuelve una respuesta JSON con los datos del usuario
        return new JsonResponse($formattedUser);
    }

    #[Route('/all', name: 'user_all', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtiene el repositorio de la entidad User
        $userRepository = $entityManager->getRepository(User::class);

        // Obtiene todos los usuarios
        $users = $userRepository->findAll();

        // Formatea los datos de los usuarios para la respuesta
        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword()
            ];
        }

        // Devuelve una respuesta JSON con todos los usuarios
        return new JsonResponse($formattedUsers);
    }
    #[Route('/{id}', name: 'user_get', methods: ['GET'])]
    public function getUserById(int $id, EntityManagerInterface $entityManager): JsonResponse

    {
        // Obtiene el repositorio de la entidad User
        $userRepository = $entityManager->getRepository(User::class);

        // Busca el usuario por su ID
        $user = $userRepository->find($id);

        // Verifica si el usuario existe
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Formatea los datos del usuario para la respuesta
        $formattedUser = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles() // Puedes incluir otros campos si lo deseas
        ];

        // Devuelve una respuesta JSON con el usuario encontrado
        return new JsonResponse($formattedUser);
    }
    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Obtiene los datos del cuerpo de la solicitud
        $data = json_decode($request->getContent(), true);

        // Verifica si se han proporcionado el correo electrónico, la contraseña y el nombre de usuario
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['username'])) {
            return new JsonResponse(['error' => 'Email, username, and password are required'], Response::HTTP_BAD_REQUEST);
        }

        // Crea una nueva instancia de la entidad User
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);  // Asegúrate de asignar el nombre de usuario

        // Codifica la contraseña
        $encodedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($encodedPassword);

        // Guarda el usuario en la base de datos
        $entityManager->persist($user);
        $entityManager->flush();

        // Crear estadísticas individuales para el usuario
        $stats = new IndividualStats();
        $stats->setPace(0); // Puedes establecer los valores por defecto aquí
        $stats->setShooting(0);
        $stats->setPhysical(0);
        $stats->setDefending(0);
        $stats->setDribbling(0);
        $stats->setPassing(0);
        $stats->setPosition('default'); // o cualquier valor por defecto
        $stats->setUser($user);

        // Guarda las estadísticas individuales en la base de datos
        $entityManager->persist($stats);
        $entityManager->flush();

        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'User registered successfully and individual stats created'], Response::HTTP_CREATED);
    }


    #[Route('/api/getuserinfo', name: 'app_get_user_info', methods: ['POST'])]
    public function getUserInfo(SerializerInterface $serializerInterface, JWTTokenManagerInterface $jwtManagerInterface, TokenStorageInterface $tokenStorageInterface, UserRepository $userRepository): Response
    {
        $decodedToken = $jwtManagerInterface->decode($tokenStorageInterface->getToken());
        // Obtener el username del usuario desde el token decodificado
        $username = $decodedToken['username'];
        // Buscar el usuario en la base de datos usando el username obtenido
        $user = $userRepository->findOneBy(['username' => $username]);
        // Devolver los datos del usuario en la respuesta HTTP
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
        // Obtiene el repositorio de la entidad Squad
        $squadRepository = $entityManager->getRepository(Squad::class);

        // Busca el escuadrón por su ID
        $squad = $squadRepository->find($squadId);

        // Verifica si el escuadrón existe
        if (!$squad) {
            return new JsonResponse(['error' => 'Squad not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtiene los usuarios asociados al escuadrón
        $users = $squad->getUser();

        // Formatea los datos de los usuarios para la respuesta
        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                // Puedes incluir otros campos si lo deseas
            ];
        }

        // Devuelve una respuesta JSON con los usuarios asociados al escuadrón
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
    
}
