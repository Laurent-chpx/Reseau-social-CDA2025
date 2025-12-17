<?php

namespace App\Entity;

use App\Repository\PromoteRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromoteRequestRepository::class)]
class PromoteRequest
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // Formules disponibles
    public const FORMULA_7_DAYS = '7_days';
    public const FORMULA_15_DAYS = '15_days';
    public const FORMULA_30_DAYS = '30_days';

    public const FORMULAS = [
        self::FORMULA_7_DAYS => [
            'label' => '7 jours',
            'days' => 7,
            'price' => 29.00,
            'description' => 'Idéal pour un événement ponctuel',
            'features' => [
                'Badge sponsorisé',
                'Placement prioritaire',
                'Visibilité régionale',
                'Statistiques basiques',
            ],
        ],
        self::FORMULA_15_DAYS => [
            'label' => '15 jours',
            'days' => 15,
            'price' => 49.00,
            'description' => 'Le meilleur rapport qualité/prix',
            'features' => [
                'Badge sponsorisé',
                'Placement prioritaire',
                'Visibilité régionale',
                'Statistiques détaillées',
                'Support prioritaire',
            ],
        ],
        self::FORMULA_30_DAYS => [
            'label' => '30 jours',
            'days' => 30,
            'price' => 89.00,
            'description' => 'Maximum de visibilité',
            'features' => [
                'Badge sponsorisé',
                'Placement prioritaire',
                'Visibilité régionale',
                'Statistiques détaillées',
                'Support prioritaire',
                'Post sur nos réseaux',
            ],
        ],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'promoteRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column(length: 20)]
    private ?string $formula = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateStart = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateEnd = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminNote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(string $formula): static
    {
        $this->formula = $formula;

        // Calcule automatiquement le prix et les dates
        if (isset(self::FORMULAS[$formula])) {
            $this->totalPrice = self::FORMULAS[$formula]['price'];

            if ($this->dateStart) {
                $days = self::FORMULAS[$formula]['days'];
                $this->dateEnd = $this->dateStart->modify('+' . ($days - 1) . ' days');
            }
        }

        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeImmutable $dateStart): static
    {
        $this->dateStart = $dateStart;

        // Recalcule la date de fin si une formule est déjà sélectionnée
        if ($this->formula && isset(self::FORMULAS[$this->formula])) {
            $days = self::FORMULAS[$this->formula]['days'];
            $this->dateEnd = $dateStart->modify('+' . ($days - 1) . ' days');
        }

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeImmutable $dateEnd): static
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
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

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;
        return $this;
    }

    public function getAdminNote(): ?string
    {
        return $this->adminNote;
    }

    public function setAdminNote(?string $adminNote): static
    {
        $this->adminNote = $adminNote;
        return $this;
    }

    public function getDurationInDays(): int
    {
        if ($this->formula && isset(self::FORMULAS[$this->formula])) {
            return self::FORMULAS[$this->formula]['days'];
        }
        return 0;
    }

    public function getFormulaLabel(): string
    {
        if ($this->formula && isset(self::FORMULAS[$this->formula])) {
            return self::FORMULAS[$this->formula]['label'];
        }
        return 'Inconnu';
    }

    public function getFormulaDetails(): array
    {
        if ($this->formula && isset(self::FORMULAS[$this->formula])) {
            return self::FORMULAS[$this->formula];
        }
        return [];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_APPROVED => 'Approuvée',
            self::STATUS_REJECTED => 'Refusée',
            default => 'Inconnu',
        };
    }

    public function __toString(): string
    {
        return 'Demande #' . $this->id . ' - ' . $this->event?->getTitle();
    }
}
