<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 100.00], nullable: false)]
    private string $balance = "0.0";

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Transactions>
     */
    #[ORM\OneToMany(targetEntity: Transactions::class, mappedBy: 'userId', orphanRemoval: true)]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Transactions>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

	/**
	 * Deduct the bet amount from the user's balance.
	 *
	 * @param float $amount
	 * @throws DomainException If the user has insufficient balance.
	 */
	public function deductBalance(string $amount): void
	{
		if ($this->balance < $amount) {
			throw new DomainException('Insufficient balance.');
		}
		$this->balance = bcsub($this->balance, $amount, 2);
	}

	/**
	 * @param float $amount
	 */
	public function addBalance(string $amount): void
	{
		$this->balance = bcadd($this->balance, $amount, 2);
	}


	public function addTransaction(Transactions $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }
}
