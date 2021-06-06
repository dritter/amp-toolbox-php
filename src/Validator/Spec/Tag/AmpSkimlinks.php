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

final class AmpSkimlinks extends Tag
{
    /**
     * ID of the tag.
     *
     * @var string
     */
    const ID = 'AMP-SKIMLINKS';

    /**
     * Array of spec rules.
     *
     * @var array
     */
    const SPEC = [
        SpecRule::TAG_NAME => Extension::SKIMLINKS,
        SpecRule::ATTRS => [
            Attribute::CUSTOM_REDIRECT_DOMAIN => [],
            Attribute::CUSTOM_TRACKING_ID => [
                SpecRule::VALUE_REGEX_CASEI => '^.{0,50}$',
            ],
            Attribute::EXCLUDED_DOMAINS => [],
            Attribute::LINK_SELECTOR => [],
            Attribute::PUBLISHER_CODE => [
                SpecRule::MANDATORY => true,
                SpecRule::VALUE_REGEX_CASEI => '^[0-9]+X[0-9]+$',
            ],
            Attribute::TRACKING => [
                SpecRule::VALUE => [
                    'false',
                    'true',
                ],
            ],
        ],
        SpecRule::ATTR_LISTS => [
            AttributeList\ExtendedAmpGlobal::ID,
        ],
        SpecRule::AMP_LAYOUT => [
            SpecRule::SUPPORTED_LAYOUTS => [
                Layout::NODISPLAY,
            ],
        ],
        SpecRule::HTML_FORMAT => [
            Format::AMP,
        ],
        SpecRule::REQUIRES_EXTENSION => [
            Extension::SKIMLINKS,
        ],
    ];
}
