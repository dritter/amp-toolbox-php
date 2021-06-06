<?php

/**
 * DO NOT EDIT!
 * This file was automatically generated via bin/generate-validator-spec.php.
 */

namespace AmpProject\Validator\Spec\Tag;

use AmpProject\Attribute;
use AmpProject\Format;
use AmpProject\Tag as Element;
use AmpProject\Validator\Spec\AttributeList;
use AmpProject\Validator\Spec\SpecRule;
use AmpProject\Validator\Spec\Tag;

final class AmpImgImgTransformed extends Tag
{
    /**
     * ID of the tag.
     *
     * @var string
     */
    const ID = 'amp-img > img (transformed)';

    /**
     * Array of spec rules.
     *
     * @var array
     */
    const SPEC = [
        SpecRule::TAG_NAME => Element::IMG,
        SpecRule::SPEC_NAME => 'amp-img > img (transformed)',
        SpecRule::MANDATORY_PARENT => 'amp-img (transformed)',
        SpecRule::ATTRS => [
            Attribute::ALT => [],
            Attribute::ATTRIBUTION => [],
            Attribute::HEIGHT => [],
            Attribute::OBJECT_FIT => [],
            Attribute::OBJECT_POSITION => [],
            Attribute::REFERRERPOLICY => [],
            Attribute::SIZES => [],
            Attribute::TITLE => [],
            Attribute::WIDTH => [],
            Attribute::CLASS_ => [
                SpecRule::MANDATORY => true,
                SpecRule::VALUE_REGEX => 'i-amphtml-fill-content\s+i-amphtml-replaced-content|i-amphtml-replaced-content\s+i-amphtml-fill-content',
            ],
            Attribute::DECODING => [
                SpecRule::MANDATORY => true,
                SpecRule::VALUE => [
                    'async',
                ],
            ],
            Attribute::LOADING => [
                SpecRule::VALUE => [
                    'lazy',
                    'eager',
                ],
            ],
        ],
        SpecRule::ATTR_LISTS => [
            AttributeList\MandatorySrcOrSrcset::ID,
        ],
        SpecRule::HTML_FORMAT => [
            Format::AMP,
        ],
        SpecRule::ENABLED_BY => [
            Attribute::TRANSFORMED,
        ],
    ];
}
