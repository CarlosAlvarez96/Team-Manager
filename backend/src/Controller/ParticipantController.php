<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'app_participant')]
class ParticipantController extends AbstractController
{
    #[Route('/all', name: 'participant_all', methods: ['GET'])]
    public function getAllParticipants(ParticipantRepository $participantRepository): JsonResponse
    {
        // Obtener todos los participantes desde el repositorio
        $participants = $participantRepository->findAll();

        // Formatear los datos de los participantes para la respuesta
        $formattedParticipants = [];
        foreach ($participants as $participant) {
            $formattedParticipants[] = [
                'id' => $participant->getId(),
                // Incluir otros campos si es necesario
            ];
        }

        // Devolver una respuesta JSON con todos los participantes
        return new JsonResponse($formattedParticipants);
    }

    #[Route('/create', name: 'participant_create', methods: ['POST'])]
    public function createParticipant(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode($request->getContent(), true);

        // Crear una nueva instancia de la entidad Participant y establecer sus propiedades
        $participant = new Participant();
        // Establecer las propiedades del participante según los datos recibidos
        
        // Guardar el participante en la base de datos
        $entityManager->persist($participant);
        $entityManager->flush();

        // Devolver una respuesta de éxito
        return new JsonResponse(['message' => 'Participant created successfully'], Response::HTTP_CREATED);
    }
}
