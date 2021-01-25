<?php

/**
 * DO NOT EDIT!
 * This file was automatically generated via bin/generate-validator-spec.php.
 */

namespace AmpProject\Validator\Spec\Tag;

use AmpProject\Validator\Spec\SpecRule;
use AmpProject\Validator\Spec\Tag;

final class AmpOoyalaPlayer extends Tag
{
    const SPEC = [
        SpecRule::TAG_NAME => Extension::OOYALA_PLAYER,
        SpecRule::ATTRS => [
            [
                SpecRule::NAME => 'data-embedcode',
                SpecRule::MANDATORY => true,
            ],
            [
                SpecRule::NAME => 'data-pcode',
                SpecRule::MANDATORY => true,
            ],
            [
                SpecRule::NAME => 'data-playerid',
                SpecRule::MANDATORY => true,
            ],
        ],
        SpecRule::ATTR_LISTS => [
            'extended-amp-global',
        ],
        SpecRule::AMP_LAYOUT => [
            'supportedLayouts' => [
                Layout::FILL,
                Layout::FIXED,
                Layout::FLEX_ITEM,
                Layout::RESPONSIVE,
            ],
        ],
        SpecRule::HTML_FORMAT => [
            Format::AMP,
        ],
        SpecRule::REQUIRES_EXTENSION => [
            'amp-ooyala-player',
        ],
    ];
}
