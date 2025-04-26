<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Account\AccountCreateRequest;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Exception;

class AccountCreationService
{
	public function __construct(
		private readonly UsersRepository $usersRepository
	) {
	}

	public function createAccount(AccountCreateRequest $request): Users
	{
		$existingUser = $this->usersRepository->findOneBy(['username' => $request->getUsername()]);

		if ($existingUser) {
			throw new Exception("User already exists");
		}

		$existingUser = $this->usersRepository->findOneBy(['email' => $request->getEmail()]);

		if ($existingUser) {
			throw new Exception("User already exists");
		}

		try {
			$user = new Users();
			$user->setUsername($request->getUsername());
			$user->setEmail($request->getEmail());
			$user->addBalance($request->getInitialBalance());

			$user = $this->usersRepository->saveAndFlush($user);
		} catch (Exception $e) {
			throw new Exception("User can not be created");
		}

		return $user;
	}
}
