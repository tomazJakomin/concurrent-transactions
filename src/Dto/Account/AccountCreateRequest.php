<?php

declare(strict_types=1);

namespace App\Dto\Account;

use Symfony\Component\Validator\Constraints as Assert;

class AccountCreateRequest
{
	public function __construct(
		#[Assert\NotBlank(message: 'Username is required')]
		private readonly string $username,
		#[Assert\NotBlank(message: 'Email is required')]
		#[Assert\Email(message: 'Invalid email format')]
		private readonly string $email,
		#[Assert\NotBlank(message: 'Initial balance is required')]
		#[Assert\GreaterThan(value: 0, message: 'Initial balance must be greater than 0')]
		private readonly float $initial_balance
	) {
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function getInitialBalance(): string
	{
		return number_format($this->initial_balance, 2, '.', '');
	}

	public function getEmail(): string
	{
		return $this->email;
	}
}
