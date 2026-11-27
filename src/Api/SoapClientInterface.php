<?php

declare(strict_types=1);

namespace Waaz\SyliusChronopostPlugin\Api;

interface SoapClientInterface
{
    public function createShipment(array $requestData);
    public function findPickupPoints(string $zipcode);
    public function findPickupPoint(string $zipcode);
}
