<?php

declare(strict_types=1);

namespace App\Dto\Account;

class AccountCreateResponse
{
	public function __construct(
		public readonly string $username,
		public readonly string $email,
		public readonly int $user_id,
		public readonly float $balance
	) {
	}
}
