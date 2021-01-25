<?php

/**
 * DO NOT EDIT!
 * This file was automatically generated via bin/generate-validator-spec.php.
 */

namespace AmpProject\Validator\Spec\Tag;

use AmpProject\Validator\Spec\SpecRule;
use AmpProject\Validator\Spec\Tag;

final class Ul extends Tag
{
    const SPEC = [
        SpecRule::TAG_NAME => Element::UL,
        SpecRule::HTML_FORMAT => [
            Format::AMP,
            Format::AMP4ADS,
            Format::AMP4EMAIL,
        ],
    ];
}
