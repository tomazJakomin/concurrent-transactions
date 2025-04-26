<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Betting\BetRequest;
use App\Entity\Transactions;
use App\Entity\Users;
use DateTimeImmutable;

class TransactionFactory
{
	public function createFrom(BetRequest $betRequest, Users $users): Transactions
	{
		return (new Transactions())
			->setUser($users)
			->setBetAmount((string) $betRequest->getBetAmount())
			->setGameType($betRequest->getGameType()->value)
			->setStatus("processed")
			->setTransactionId($betRequest->getTransactionId())
			->setCreatedAt(new DateTimeImmutable());
	}
}
