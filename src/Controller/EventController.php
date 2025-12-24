<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event')]
final class EventController extends AbstractController
{
    // Liste des événements avec filtres
    #[Route('', name: 'app_event_index')]
    public function index(
        Request $request,
        EventRepository $eventRepository,
        CategoryRepository $categoryRepository,
        DepartmentRepository $departmentRepository,
        CityRepository $cityRepository
    ): Response {
        // Récupère les filtres depuis la requête
        $filters = [
            'city' => $request->query->get('city'),
            'department' => $request->query->get('department'),
            'category' => $request->query->get('category'),
            'dateFrom' => $request->query->get('date_from'),
            'dateTo' => $request->query->get('date_to'),
            'freeOnly' => $request->query->getBoolean('free_only'),
            'search' => $request->query->get('search'),
        ];

        $events = $eventRepository->findWithFilters(
            $filters['city'] ? (int) $filters['city'] : null,
            $filters['department'] ? (int) $filters['department'] : null,
            $filters['category'] ? (int) $filters['category'] : null,
            $filters['dateFrom'],
            $filters['dateTo'],
            $filters['freeOnly'],
            $filters['search']
        );

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
            'departments' => $departmentRepository->findBy([], ['name' => 'ASC']),
            'cities' => $cityRepository->findBy([], ['name' => 'ASC']),
            'currentFilters' => $filters,
        ]);
    }

    // Détails d'un événement
    #[Route('/{id}', name: 'app_event_show', requirements: ['id' => '\d+'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    // Créer un événement
    #[Route('/new', name: 'app_event_new', priority: 1)]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreatedBy($this->getUser());
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_profile_events');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Modifier un événement
    #[Route('/{id}/edit', name: 'app_event_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Événement modifié avec succès !');
            return $this->redirectToRoute('app_profile_events');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form,
            'event' => $event,
        ]);
    }

    // Supprimer un événement
    #[Route('/{id}/delete', name: 'app_event_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        if ($event->getCreatedBy() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cet événement.');
            return $this->redirectToRoute('app_profile_events');
        }

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $em->remove($event);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_profile_events');
    }
}
