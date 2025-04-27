<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Betting\BetRequest;
use App\Dto\Betting\BetResponse;
use App\Exception\Betting\BettingException;
use App\Repository\UsersRepository;
use App\Service\BettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class BetController extends AbstractController
{

	private const DEFAULT_LOCK_WAIT_SECONDS = 20;

	private const DEFAULT_LOCK_CHECK_INTERVAL_MICROSECONDS = 100000;

	#[Route('api/process-transaction', name: 'app_bet', methods: ['POST'])]
	public function __invoke(
		#[MapRequestPayload] BetRequest $betRequest,
		BettingService $bettingService,
		UsersRepository $usersRepository,
		SerializerInterface $serializer
	): JsonResponse {
		try {
			$bettingService->placeBet(
				$betRequest,
				self::DEFAULT_LOCK_WAIT_SECONDS,
				self::DEFAULT_LOCK_CHECK_INTERVAL_MICROSECONDS
			);

			$userBalance = $usersRepository->getUserBalance($betRequest->getUserId());
		} catch (BettingException $exception) {
			return $this->json([
				'message'        => $exception->getMessage(),
				'transaction_id' => $betRequest->getTransactionId(),
				'status'         => 'error',
			],
				JsonResponse::HTTP_BAD_REQUEST);
		}

		$betResponse = new BetResponse(
			'success',
			'Transaction processed successfully',
			$betRequest->getTransactionId(),
			(float) $userBalance
		);

		return JsonResponse::fromJsonString($serializer->serialize($betResponse, 'json'));
	}
}
