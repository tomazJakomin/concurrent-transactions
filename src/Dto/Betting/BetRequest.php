<?php

declare(strict_types=1);

namespace App\Dto\Betting;

use App\Enum\Betting\GameType;
use Symfony\Component\Validator\Constraints as Assert;

class BetRequest
{
	public function __construct(
		#[Assert\NotBlank(message: 'User ID is required')]
		private readonly int $user_id,
		#[Assert\NotBlank(message: 'Bet amount is required')]
		#[Assert\GreaterThan(value: 0, message: 'Bet amount must be greater than 0')]
		private readonly float $bet_amount,
		#[Assert\NotBlank(message: 'Game type is required')]
		private readonly GameType $game_type,
		#[Assert\NotBlank(message: 'Transaction ID is required')]
		private readonly string $transaction_id
	) {
	}

	public function getUserId(): int
	{
		return $this->user_id;
	}

	public function getBetAmount(): float
	{
		return $this->bet_amount;
	}

	public function getGameType(): GameType
	{
		return $this->game_type;
	}

	public function getTransactionId(): string
	{
		return $this->transaction_id;
	}
}
