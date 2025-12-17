<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Entity\UserCity;
use App\Entity\UserFollow;
use App\Repository\UserCityRepository;
use App\Repository\UserFollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/follow')]
#[IsGranted('ROLE_USER')]
class FollowController extends AbstractController
{

    #[Route('/user/{id}', name: 'app_follow_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function followUser(
        User $userToFollow,
        Request $request,
        EntityManagerInterface $em,
        UserFollowRepository $userFollowRepository
    ): Response {
        $currentUser = $this->getUser();

        // On ne peut pas se suivre soi-même
        if ($currentUser === $userToFollow) {
            $this->addFlash('error', 'Vous ne pouvez pas vous suivre vous-même.');
            return $this->redirectBack($request);
        }

        // Vérifie si déjà suivi
        $existingFollow = $userFollowRepository->findFollow($currentUser, $userToFollow);

        if ($existingFollow) {
            $this->addFlash('warning', 'Vous suivez déjà cet utilisateur.');
            return $this->redirectBack($request);
        }

        // Crée le follow
        $follow = new UserFollow();
        $follow->setFollower($currentUser);
        $follow->setFollowed($userToFollow);
        $follow->setCreatedAt(new \DateTimeImmutable());

        $em->persist($follow);
        $em->flush();

        $this->addFlash('success', 'Vous suivez maintenant ' . $userToFollow->getFirstName() . ' ' . $userToFollow->getLastName());
        return $this->redirectBack($request);
    }

    #[Route('/user/{id}/unfollow', name: 'app_unfollow_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function unfollowUser(
        User $userToUnfollow,
        Request $request,
        EntityManagerInterface $em,
        UserFollowRepository $userFollowRepository
    ): Response {
        $currentUser = $this->getUser();

        $existingFollow = $userFollowRepository->findFollow($currentUser, $userToUnfollow);

        if (!$existingFollow) {
            $this->addFlash('warning', 'Vous ne suivez pas cet utilisateur.');
            return $this->redirectBack($request);
        }

        $em->remove($existingFollow);
        $em->flush();

        $this->addFlash('success', 'Vous ne suivez plus ' . $userToUnfollow->getFirstName() . ' ' . $userToUnfollow->getLastName());
        return $this->redirectBack($request);
    }



    #[Route('/city/{id}', name: 'app_follow_city', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function followCity(
        City $city,
        Request $request,
        EntityManagerInterface $em,
        UserCityRepository $userCityRepository
    ): Response {
        $currentUser = $this->getUser();

        // Vérifie si déjà suivi
        $existingFollow = $userCityRepository->findFollow($currentUser, $city);

        if ($existingFollow) {
            $this->addFlash('warning', 'Vous suivez déjà cette ville.');
            return $this->redirectBack($request);
        }

        // Crée le follow
        $follow = new UserCity();
        $follow->setUser($currentUser);
        $follow->setCity($city);
        $follow->setCreatedAt(new \DateTimeImmutable());

        $em->persist($follow);
        $em->flush();

        $this->addFlash('success', 'Vous suivez maintenant la ville de ' . $city->getName());
        return $this->redirectBack($request);
    }

    #[Route('/city/{id}/unfollow', name: 'app_unfollow_city', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function unfollowCity(
        City $city,
        Request $request,
        EntityManagerInterface $em,
        UserCityRepository $userCityRepository
    ): Response {
        $currentUser = $this->getUser();

        $existingFollow = $userCityRepository->findFollow($currentUser, $city);

        if (!$existingFollow) {
            $this->addFlash('warning', 'Vous ne suivez pas cette ville.');
            return $this->redirectBack($request);
        }

        $em->remove($existingFollow);
        $em->flush();

        $this->addFlash('success', 'Vous ne suivez plus la ville de ' . $city->getName());
        return $this->redirectBack($request);
    }

    #[Route('/my-follows', name: 'app_my_follows')]
    public function myFollows(
        UserFollowRepository $userFollowRepository,
        UserCityRepository $userCityRepository
    ): Response {
        $currentUser = $this->getUser();

        return $this->render('follow/my_follows.html.twig', [
            'followedUsers' => $userFollowRepository->findFollowedUsers($currentUser),
            'followedCities' => $userCityRepository->findFollowedCities($currentUser),
        ]);
    }

    private function redirectBack(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_home'));
    }
}
