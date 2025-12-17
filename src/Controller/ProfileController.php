<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventImage;
use App\Form\ProfileFormType;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    #[Route('', name: 'app_profile')]
    public function index(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();
        $events = $eventRepository->findBy(['createdBy' => $user], ['dateStart' => 'DESC']);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'events' => $events,
            'feedCities' => $eventRepository->findByFollowedCities($user, 10),
            'feedUsers' => $eventRepository->findByFollowedUsers($user, 10),
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('plainPassword')->getData();

            if ($newPassword) {
                if (!$currentPassword) {
                    $this->addFlash('error', 'Vous devez saisir votre mot de passe actuel pour le modifier.');
                    return $this->render('profile/edit.html.twig', [
                        'form' => $form,
                    ]);
                }

                if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                    return $this->render('profile/edit.html.twig', [
                        'form' => $form,
                    ]);
                }

                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/events', name: 'app_profile_events')]
    public function events(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();
        $events = $eventRepository->findBy(['createdBy' => $user], ['dateStart' => 'DESC']);

        return $this->render('profile/events.html.twig', [
            'events' => $events,
        ]);
    }

    // Créer un événement
    #[Route('/events/new', name: 'app_profile_event_new')]
    public function newEvent(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreatedBy($this->getUser());
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setUpdatedAt(new \DateTimeImmutable());

            // Gestion des images
            $imageFiles = $form->get('images')->getData();
            if ($imageFiles) {
                $position = 0;
                foreach ($imageFiles as $imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('events_images_directory'),
                            $newFilename
                        );

                        $eventImage = new EventImage();
                        $eventImage->setUrl($newFilename);
                        $eventImage->setPosition($position);
                        $eventImage->setCreatedAt(new \DateTimeImmutable());
                        $eventImage->setEvent($event);

                        $em->persist($eventImage);
                        $position++;
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload d\'une image.');
                    }
                }
            }

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_profile_events');
        }

        return $this->render('profile/event_new.html.twig', [
            'form' => $form,
        ]);
    }

    // Modifier un événement
    #[Route('/events/{id}/edit', name: 'app_profile_event_edit', requirements: ['id' => '\d+'])]
    public function editEvent(
        Event $event,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        // Vérifie que l'utilisateur est bien le propriétaire
        if ($event->getCreatedBy() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cet événement.');
            return $this->redirectToRoute('app_profile_events');
        }

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des nouvelles images
            $imageFiles = $form->get('images')->getData();
            if ($imageFiles) {
                $position = count($event->getImages());
                foreach ($imageFiles as $imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('events_images_directory'),
                            $newFilename
                        );

                        $eventImage = new EventImage();
                        $eventImage->setUrl($newFilename);
                        $eventImage->setPosition($position);
                        $eventImage->setCreatedAt(new \DateTimeImmutable());
                        $eventImage->setEvent($event);

                        $em->persist($eventImage);
                        $position++;
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload d\'une image.');
                    }
                }
            }

            $event->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Événement modifié avec succès !');
            return $this->redirectToRoute('app_profile_events');
        }

        return $this->render('profile/event_edit.html.twig', [
            'form' => $form,
            'event' => $event,
        ]);
    }

    #[Route('/events/{id}/delete', name: 'app_profile_event_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEvent(
        Event $event,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($event->getCreatedBy() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cet événement.');
            return $this->redirectToRoute('app_profile_events');
        }

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            // Supprime les fichiers images
            foreach ($event->getImages() as $image) {
                $imagePath = $this->getParameter('events_images_directory') . '/' . $image->getUrl();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $em->remove($event);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_profile_events');
    }

    // Supprimer une image d'un événement
    #[Route('/events/image/{id}/delete', name: 'app_profile_event_image_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEventImage(
        EventImage $eventImage,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $event = $eventImage->getEvent();

        if ($event->getCreatedBy() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette image.');
            return $this->redirectToRoute('app_profile_events');
        }

        if ($this->isCsrfTokenValid('delete_image' . $eventImage->getId(), $request->request->get('_token'))) {
            $imagePath = $this->getParameter('events_images_directory') . '/' . $eventImage->getUrl();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $em->remove($eventImage);
            $em->flush();
            $this->addFlash('success', 'Image supprimée avec succès !');
        }

        return $this->redirectToRoute('app_profile_event_edit', ['id' => $event->getId()]);
    }
}
