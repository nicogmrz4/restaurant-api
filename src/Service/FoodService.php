<?php

namespace App\Service;

use App\Entity\Food;
use App\Entity\FoodPriceHistory;
use App\Repository\FoodPriceHistoryRepository;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;

class FoodService
{
    public function __construct(private EntityManagerInterface $em, private FoodRepository $foodRepo,
    private FoodPriceHistoryRepository $foodPriceHistoryRepo) {}

    public function save(Food $food) {
        $foodPriceHistory = new FoodPriceHistory();
        $foodPriceHistory->setPrice($food->getCurrentPrice());
        // $foodPriceHistory->setPeriodFrom(new \DateTimeImmutable());
        $foodPriceHistory->setIsCurrent(true);
        $food->addPriceHistory($foodPriceHistory);

        $this->em->persist($foodPriceHistory);
        $this->em->persist($food);

        $this->em->flush();
    }

    public function update(Food $updatedFood, int $id): void {
        $food = $this->foodRepo->findOneBy(['id' => $id]);
        $updatedFoodPirce = $updatedFood->getCurrentPrice();

        if ($food->getCurrentPrice() != $updatedFoodPirce && !is_null($updatedFoodPirce)) {
            $newPriceHistory = $this->newPriceHistory($updatedFood->getCurrentPrice());
            $food->addPriceHistory($newPriceHistory);

            $this->em->persist($newPriceHistory);
            $this->em->flush();
            
            return;
        }
    }

    private function newPriceHistory(float $pirce): FoodPriceHistory {
        $this->updateLastPriceHistory();

        $newPriceHistory = new FoodPriceHistory;
        $newPriceHistory->setPrice($pirce);
        $newPriceHistory->setIsCurrent(true);
        
        return $newPriceHistory;
    }

    private function updateLastPriceHistory() {
        $lastPriceHistory = $this->foodPriceHistoryRepo->findOneBy(['isCurrent' => true]);
        $lastPriceHistory->setIsCurrent(false);
        $lastPriceHistory->setPeriodTo(new \DateTimeImmutable());

        $this->em->persist($lastPriceHistory);
        $this->em->flush();
    }
}
