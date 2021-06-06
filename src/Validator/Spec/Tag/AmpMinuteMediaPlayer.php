<?php

/**
 * DO NOT EDIT!
 * This file was automatically generated via bin/generate-validator-spec.php.
 */

namespace AmpProject\Validator\Spec\Tag;

use AmpProject\Attribute;
use AmpProject\Extension;
use AmpProject\Format;
use AmpProject\Layout;
use AmpProject\Validator\Spec\AttributeList;
use AmpProject\Validator\Spec\SpecRule;
use AmpProject\Validator\Spec\Tag;

final class AmpMinuteMediaPlayer extends Tag
{
    /**
     * ID of the tag.
     *
     * @var string
     */
    const ID = 'AMP-MINUTE-MEDIA-PLAYER';

    /**
     * Array of spec rules.
     *
     * @var array
     */
    const SPEC = [
        SpecRule::TAG_NAME => Extension::MINUTE_MEDIA_PLAYER,
        SpecRule::ATTRS => [
            Attribute::AUTOPLAY => [],
            Attribute::DATA_CONTENT_ID => [],
            Attribute::DATA_CONTENT_TYPE => [
                SpecRule::MANDATORY => true,
                SpecRule::VALUE => [
                    'curated',
                    'semantic',
                    'specific',
                ],
            ],
            Attribute::DATA_MINIMUM_DATE_FACTOR => [],
            Attribute::DATA_SCANNED_ELEMENT => [],
            Attribute::DATA_SCANNED_ELEMENT_TYPE => [
                SpecRule::VALUE => [
                    'className',
                    'id',
                    'tag',
                ],
            ],
            Attribute::DATA_SCOPED_KEYWORDS => [],
            Attribute::DATA_TAGS => [],
            Attribute::DOCK => [
                SpecRule::REQUIRES_EXTENSION => [
                    Extension::VIDEO_DOCKING,
                ],
            ],
        ],
        SpecRule::ATTR_LISTS => [
            AttributeList\ExtendedAmpGlobal::ID,
        ],
        SpecRule::SPEC_URL => 'https://amp.dev/documentation/components/amp-minute-media-player/',
        SpecRule::AMP_LAYOUT => [
            SpecRule::SUPPORTED_LAYOUTS => [
                Layout::FILL,
                Layout::FIXED,
                Layout::FIXED_HEIGHT,
                Layout::FLEX_ITEM,
                Layout::RESPONSIVE,
            ],
        ],
        SpecRule::HTML_FORMAT => [
            Format::AMP,
        ],
        SpecRule::REQUIRES_EXTENSION => [
            Extension::MINUTE_MEDIA_PLAYER,
        ],
    ];
}
