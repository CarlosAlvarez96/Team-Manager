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
        $participants = $participantRepository->findAll();

        $formattedParticipants = [];
        foreach ($participants as $participant) {
            $formattedParticipants[] = [
                'id' => $participant->getId(),
            ];
        }

        return new JsonResponse($formattedParticipants);
    }

    #[Route('/create', name: 'participant_create', methods: ['POST'])]
    public function createParticipant(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $participant = new Participant();
        
        $entityManager->persist($participant);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Participant created successfully'], Response::HTTP_CREATED);
    }
}
