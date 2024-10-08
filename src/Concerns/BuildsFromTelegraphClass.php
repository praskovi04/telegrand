<?php

namespace Praskovi04\Telegrand\Concerns;

use Praskovi04\Telegrand\Telegrand;
use ReflectionClass;

trait BuildsFromTelegraphClass
{
    public static function makeFrom(Telegrand $telegraph): self
    {
        $newInstance = new self();

        $reflection = new ReflectionClass($telegraph);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->name;
            $property->setAccessible(true);

            if ($property->isInitialized($telegraph)) {
                $newInstance->$propertyName = $property->getValue($telegraph);
            }
        }

        return $newInstance;
    }
}
