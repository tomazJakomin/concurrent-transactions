<?php

declare(strict_types=1);

namespace App\Dto\Betting;

class BetResponse
{
public function __construct(
	public readonly string $status,
	public readonly string $message,
	public readonly string $transaction_id,
	public readonly float $user_balance
)
{
}
}
