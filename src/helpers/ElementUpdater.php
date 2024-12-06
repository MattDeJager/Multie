<?php

namespace boost\multie\helpers;

use Craft;

class ElementUpdater
{
    public static function update($ids, $properties, $getElementCallback, $saveElementCallback): void
    {
        foreach ($ids as $id) {
            $element = $getElementCallback($id);
            if (!$element) {
                $type = static::getElementType($element);
                Craft::error("{$type} not found: {$id}", __METHOD__);
                continue;
            }
            foreach ($properties as $property) {
                if (property_exists($element, $property['handle'])) {
                    $element->{$property['handle']} = $property['value'];
                }
            }
            try {
                $saveElementCallback($element);
            } catch (\Throwable $e) {
                $type = static::getElementType($element);
                Craft::error("Error saving {$type}: {$e->getMessage()}", __METHOD__);
            }
        }
    }

    private static function getElementType($element): string
    {
        if ($element) {
            $class = get_class($element);
            return (new \ReflectionClass($class))->getShortName(); // Return the short name of the class
        }
        return 'Unknown Element';
    }
}
