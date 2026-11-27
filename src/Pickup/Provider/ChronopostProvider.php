<?php

declare(strict_types=1);

namespace Waaz\SyliusChronopostPlugin\Pickup\Provider;

use Waaz\SyliusChronopostPlugin\Api\SoapClientInterface;
use Setono\SyliusPickupPointPlugin\Attribute\AsProvider;
use Setono\SyliusPickupPointPlugin\DTO\Address;
use Setono\SyliusPickupPointPlugin\DTO\PickupPoint;
use Setono\SyliusPickupPointPlugin\Provider\Provider;
use stdClass;

#[AsProvider(code: 'chronopost', name: 'Chronopost')]
final class ChronopostProvider extends Provider
{
    public function __construct(
        private SoapClientInterface $soapClient
    )
    {
    }

    public function transform(stdClass $point): PickupPoint
    {
        $pickupPoint = new PickupPoint();
        $pickupPoint->provider = $this->getCode();
        $pickupPoint->id = (string) $point->identifiantChronopostPointA2PAS;
        $pickupPoint->name = $point->nomEnseigne;
        $pickupPoint->address = $point->adresse1;
        $pickupPoint->zipCode = $point->codePostal;
        $pickupPoint->city = $point->localite;
        $pickupPoint->country = 'FR';
        $pickupPoint->latitude = (string) $point->coordGeoLatitude;
        $pickupPoint->longitude = (string) $point->coordGeoLongitude;

        return $pickupPoint;
    }

    public function findPickupPoints(Address $address): array
    {
        if (null === $address->postalCode) {
            return [];
        }

        $pickupPoints = [];
        $points = $this->soapClient->findPickupPoints($address->postalCode);

        foreach ($points as $point) {
            $pickupPoints[] = $this->transform($point);
        }

        return $pickupPoints;
    }

    public function findPickupPoint(string $id, array $metadata = []): ?PickupPoint
    {
        $point = $this->soapClient->findPickupPoint($id);

        if ($point) {
            return $this->transform($point);
        }

        return null;
    }

}
