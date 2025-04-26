<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Account\AccountCreateRequest;
use App\Dto\Account\AccountCreateResponse;
use App\Service\AccountCreationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AccountController extends AbstractController
{
	#[Route('/api/create-account', name: 'app_user', methods: ['POST'])]
	public function createAccount(
		#[MapRequestPayload] AccountCreateRequest $userCreateRequest,
		ValidatorInterface $validator,
		AccountCreationService $accountCreationService,
		SerializerInterface $serializer
	): JsonResponse {
		$errors = $validator->validate($userCreateRequest);

		if (count($errors) > 0) {

			$errorsString = (string)$errors;

			return new JsonResponse($errorsString, JsonResponse::HTTP_BAD_REQUEST);
		}

		try {
			$user = $accountCreationService->createAccount($userCreateRequest);
		} catch (Exception $exception) {
			return $this->json([
				'message' => $exception->getMessage(),
			],
				JsonResponse::HTTP_BAD_REQUEST);
		}

		$accountResponse = new AccountCreateResponse(
			$user->getUsername(),
			$user->getEmail(),
			$user->getId(),
			(float)$user->getBalance()
		);

		return JsonResponse::fromJsonString($serializer->serialize($accountResponse, 'json'));
	}
}
