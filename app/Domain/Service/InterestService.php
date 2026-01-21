<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\InterestRepository;
use App\Domain\DTO\InterestDTO;

final class InterestService
{
    public function __construct(
        private InterestRepository $repo
    ) {}

    public function list(int $userId): array
    {
        $interests = $this->repo->byUserId($userId);

        // Конвертируем в DTO
        return array_map(
            fn($data) => InterestDTO::fromArray($data),
            $interests
        );
    }

    public function add(InterestDTO $dto): bool
    {
        if (!$dto->validate()) {
            return false;
        }

        $id = $this->repo->create($dto->userId, $dto->title);
        return $id > 0;
    }

    public function update(InterestDTO $dto): bool
    {
        if (!$dto->validate() || $dto->id <= 0) {
            return false;
        }

        $this->repo->update($dto->id, $dto->userId, $dto->title);
        return true;
    }

    public function delete(InterestDTO $dto): bool
    {
        if ($dto->id <= 0) {
            return false;
        }

        $this->repo->delete($dto->id, $dto->userId);
        return true;
    }

    public function deleteAdmin(int $interestId): bool
    {
        if ($interestId <= 0) {
            return false;
        }

        $this->repo->deleteByIdAdmin($interestId);
        return true;
    }

    public function getUserInterests(int $userId): array
    {
        return $this->repo->byUserId($userId);
    }
}