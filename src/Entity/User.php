<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'createdBy')]
    private Collection $events;

    /**
     * @var Collection<int, UserFollow>
     */
    #[ORM\OneToMany(targetEntity: UserFollow::class, mappedBy: 'follower')]
    private Collection $followers;

    /**
     * @var Collection<int, UserFollow>
     */
    #[ORM\OneToMany(targetEntity: UserFollow::class, mappedBy: 'followed')]
    private Collection $following;

    /**
     * @var Collection<int, UserCity>
     */
    #[ORM\OneToMany(targetEntity: UserCity::class, mappedBy: 'user')]
    private Collection $followedCities;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->followedCities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCreatedBy($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCreatedBy() === $this) {
                $event->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserFollow>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(UserFollow $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->setFollower($this);
        }

        return $this;
    }

    public function removeFollower(UserFollow $follower): static
    {
        if ($this->followers->removeElement($follower)) {
            // set the owning side to null (unless already changed)
            if ($follower->getFollower() === $this) {
                $follower->setFollower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserFollow>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(UserFollow $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->setFollowed($this);
        }

        return $this;
    }

    public function removeFollowing(UserFollow $following): static
    {
        if ($this->following->removeElement($following)) {
            // set the owning side to null (unless already changed)
            if ($following->getFollowed() === $this) {
                $following->setFollowed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserCity>
     */
    public function getFollowedCities(): Collection
    {
        return $this->followedCities;
    }

    public function addFollowedCity(UserCity $followedCity): static
    {
        if (!$this->followedCities->contains($followedCity)) {
            $this->followedCities->add($followedCity);
            $followedCity->setUser($this);
        }

        return $this;
    }

    public function removeFollowedCity(UserCity $followedCity): static
    {
        if ($this->followedCities->removeElement($followedCity)) {
            // set the owning side to null (unless already changed)
            if ($followedCity->getUser() === $this) {
                $followedCity->setUser(null);
            }
        }

        return $this;
    }
}
