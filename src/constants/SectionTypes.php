<?php

namespace boost\multie\constants;

class SectionTypes
{
    // Define constants for section types
    public const SINGLE = 'single';
    public const STRUCTURE = 'structure';
    public const CHANNEL = 'channel';

    // Method to get all section types
    public static function getSectionTypes(): array
    {
        return [
            self::SINGLE,
            self::STRUCTURE,
            self::CHANNEL,
        ];
    }
}