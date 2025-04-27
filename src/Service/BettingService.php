<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Betting\BetRequest;
use App\Exception\Betting\BettingException;
use App\Exception\Betting\DuplicatedTransactionException;
use App\Factory\TransactionFactory;
use App\Repository\TransactionsRepository;
use App\Repository\UsersRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Exception;
use Psr\Log\LoggerInterface;

class BettingService
{

	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly TransactionsRepository $transactionsRepository,
		private readonly UsersRepository $usersRepository,
		private readonly TransactionFactory $transactionFactory,
		private readonly LoggerInterface $logger
	) {
	}

	/**
	 * @param BetRequest $betRequest
	 * @param float      $maxLockWaitSeconds
	 * @param int        $lockCheckIntervalMicroseconds
	 *
	 * @return void
	 *
	 * @throws BettingException
	 */
	public function placeBet(
		BetRequest $betRequest,
		float $maxLockWaitSeconds,
		int $lockCheckIntervalMicroseconds
	): void {
		$lockAcquired = false;
		$startTime = microtime(true);
		while (!$lockAcquired && (microtime(true) - $startTime) < $maxLockWaitSeconds) {
			try {
				$this->entityManager->beginTransaction();

				$this->checkIfTransactionExists($betRequest->getTransactionId());

				// Attempt to retrieve the user with a pessimistic write lock
				$user = $this->usersRepository
					->find($betRequest->getUserId(), LockMode::PESSIMISTIC_WRITE);

				if ($user) {
					$lockAcquired = true;

					$user->deductBalance(number_format($betRequest->getBetAmount(), 2));

					// added to simulate long running process
					sleep(5);
					$this->entityManager->persist($user);

					// recheck if the transaction was not persisted before user locked
					$this->checkIfTransactionExists($betRequest->getTransactionId());

					$newTransaction = $this->transactionFactory->createFrom($betRequest, $user);

					$this->entityManager->persist($newTransaction);
					$this->entityManager->flush();

					$this->entityManager->commit();

					$this->logger->info(
						sprintf(
							'Successfully processed bet transaction with ID "%s" for user %d.',
							$betRequest->getTransactionId(),
							$betRequest->getUserId()
						)
					);

					return;
				}

				$this->entityManager->rollback();

				throw new BettingException("User not found {$betRequest->getUserId()}");
			} catch (DuplicatedTransactionException $e) {
				throw new BettingException(
					$e->getMessage(), 0, $e
				);
			} catch (DomainException $e) {
				$this->entityManager->rollback();
				$this->logger->warning(
					sprintf('Could not acquire lock for user %d immediately. Waiting...', $betRequest->getUserId())
				);
				throw new BettingException($e->getMessage(), 0, $e);
			} catch (BettingException $e) {
				throw $e;
			} catch (Exception $e) {
				$this->entityManager->rollback();
				$this->logger->warning(
					sprintf('Could not acquire lock for user %d immediately. Waiting...', $betRequest->getUserId())
				);
				usleep($lockCheckIntervalMicroseconds);
			}
		}

		$this->logger->warning(sprintf('Timeout waiting for lock on user %d.', $betRequest->getUserId()));

		throw new BettingException("Timeout waiting for lock on user {$betRequest->getUserId()}");
	}

	/**
	 * @throws DuplicatedTransactionException
	 */
	private function checkIfTransactionExists(string $transactionId): void
	{
		// Check if the transaction ID already exists
		$transaction = $this->transactionsRepository
			->findOneBy(['transactionId' => $transactionId]);

		if ($transaction) {
			$this->entityManager->rollback();

			$this->logger->warning(
				sprintf('Transaction with ID "%s" has already been processed.', $transactionId)
			);

			throw new DuplicatedTransactionException(
				"Transaction with ID {$transactionId} has already been processed."
			);
		}
	}
}
