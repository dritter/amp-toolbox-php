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
use AmpProject\Protocol;
use AmpProject\Validator\Spec\AttributeList;
use AmpProject\Validator\Spec\SpecRule;
use AmpProject\Validator\Spec\Tag;

final class AmpAutocompleteAmp4email extends Tag
{
    /**
     * ID of the tag.
     *
     * @var string
     */
    const ID = 'AMP-AUTOCOMPLETE (AMP4EMAIL)';

    /**
     * Array of spec rules.
     *
     * @var array
     */
    const SPEC = [
        SpecRule::TAG_NAME => Extension::AUTOCOMPLETE,
        SpecRule::SPEC_NAME => 'AMP-AUTOCOMPLETE (AMP4EMAIL)',
        SpecRule::ATTRS => [
            Attribute::HIGHLIGHT_USER_ENTRY => [],
            Attribute::INLINE => [],
            Attribute::ITEMS => [],
            Attribute::MAX_ITEMS => [],
            Attribute::MIN_CHARACTERS => [],
            Attribute::PREFETCH => [],
            Attribute::QUERY => [
                SpecRule::TRIGGER => [
                    SpecRule::ALSO_REQUIRES_ATTR => [
                        Attribute::SRC,
                    ],
                ],
            ],
            Attribute::SUBMIT_ON_ENTER => [],
            Attribute::SUGGEST_FIRST => [],
            Attribute::SRC => [
                SpecRule::MANDATORY => true,
                SpecRule::DISALLOWED_VALUE_REGEX => '__amp_source_origin|{{|}}',
                SpecRule::VALUE_URL => [
                    SpecRule::PROTOCOL => [
                        Protocol::HTTPS,
                    ],
                    SpecRule::ALLOW_RELATIVE => false,
                ],
            ],
            Attribute::TEMPLATE => [
                SpecRule::VALUE_ONEOF_SET => 'TEMPLATE_IDS',
            ],
        ],
        SpecRule::ATTR_LISTS => [
            AttributeList\ExtendedAmpGlobal::ID,
        ],
        SpecRule::AMP_LAYOUT => [
            SpecRule::SUPPORTED_LAYOUTS => [
                Layout::CONTAINER,
            ],
        ],
        SpecRule::DISALLOWED_ANCESTOR => [
            'AMP-AUTOCOMPLETE',
            'AMP-STATE',
            'TEMPLATE',
        ],
        SpecRule::HTML_FORMAT => [
            Format::AMP4EMAIL,
        ],
        SpecRule::REQUIRES_EXTENSION => [
            Extension::AUTOCOMPLETE,
        ],
    ];
}
