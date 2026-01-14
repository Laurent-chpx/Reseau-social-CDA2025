<?php


namespace App\Twig;

use App\Entity\City;
use App\Entity\User;
use App\Repository\UserCityRepository;
use App\Repository\UserFollowRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FollowExtension extends AbstractExtension
{
    public function __construct(
        private Security             $security,
        private UserFollowRepository $userFollowRepository,
        private UserCityRepository   $userCityRepository
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_following_user', [$this, 'isFollowingUser']),
            new TwigFunction('is_following_city', [$this, 'isFollowingCity']),
        ];
    }

    public function isFollowingUser(User $user): bool
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        return $this->userFollowRepository->findFollow($currentUser, $user) !== null;
    }

    public function isFollowingCity(City $city): bool
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        return $this->userCityRepository->findFollow($currentUser, $city) !== null;
    }
}
