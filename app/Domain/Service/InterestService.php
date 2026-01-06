<?php
namespace App\Domain\Service;


use App\Domain\Repository\InterestRepository;

class InterestService
{
    public function __construct(private InterestRepository $repo) {}

    public function list(int $userId): array
    {
        return $this->repo->byUserId($userId);
    }

    public function add(int $userId, string $title): void
    {
        $this->repo->create($userId, $title);
    }

    public function update(int $userId, int $interestId, string $title): void
    {
        $this->repo->update($interestId, $userId, $title);
    }

    public function delete(int $userId, int $interestId): void
    {
        $this->repo->delete($interestId, $userId);
    }
}
