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

final class AmpPanZoom extends Tag
{
    /**
     * ID of the tag.
     *
     * @var string
     */
    const ID = 'AMP-PAN-ZOOM';

    /**
     * Array of spec rules.
     *
     * @var array
     */
    const SPEC = [
        SpecRule::TAG_NAME => Extension::PAN_ZOOM,
        SpecRule::ATTRS => [
            Attribute::DISABLE_DOUBLE_TAP => [
                SpecRule::VALUE => [
                    '',
                ],
            ],
            Attribute::INITIAL_SCALE => [
                SpecRule::VALUE_REGEX => '[0-9]+(\.[0-9]+)?',
            ],
            Attribute::INITIAL_X => [
                SpecRule::VALUE_REGEX => '[0-9]+',
            ],
            Attribute::INITIAL_Y => [
                SpecRule::VALUE_REGEX => '[0-9]+',
            ],
            Attribute::MAX_SCALE => [
                SpecRule::VALUE_REGEX => '[0-9]+(\.[0-9]+)?',
            ],
            Attribute::RESET_ON_RESIZE => [
                SpecRule::VALUE => [
                    '',
                ],
            ],
        ],
        SpecRule::ATTR_LISTS => [
            AttributeList\ExtendedAmpGlobal::ID,
        ],
        SpecRule::AMP_LAYOUT => [
            SpecRule::SUPPORTED_LAYOUTS => [
                Layout::FILL,
                Layout::FIXED,
                Layout::FIXED_HEIGHT,
                Layout::RESPONSIVE,
            ],
        ],
        SpecRule::HTML_FORMAT => [
            Format::AMP,
        ],
        SpecRule::REQUIRES_EXTENSION => [
            Extension::PAN_ZOOM,
        ],
    ];
}
