<?php

/**
 * DO NOT EDIT!
 * This file was automatically generated via bin/generate-validator-spec.php.
 */

namespace AmpProject\Validator\Spec\Section;

use AmpProject\Extension;
use AmpProject\Internal;
use AmpProject\Tag as Element;

final class DescendantTagList
{
    const AMP_MEGA_MENU_ALLOWED_DESCENDANTS = [
        Element::A,
        Extension::AD,
        Extension::CAROUSEL,
        Extension::EMBED,
        Extension::IMG,
        Extension::LIGHTBOX,
        Extension::LIST_,
        Extension::VIDEO,
        Element::B,
        Element::BR,
        Element::BUTTON,
        Element::COL,
        Element::COLGROUP,
        Element::DIV,
        Element::EM,
        Element::FIELDSET,
        Element::FORM,
        Element::H1,
        Element::H2,
        Element::H3,
        Element::H4,
        Element::H5,
        Element::H6,
        Element::I,
        Element::INPUT,
        Element::LABEL,
        Element::LI,
        Element::MARK,
        Element::NAV,
        Element::OL,
        Element::OPTION,
        Element::P,
        Element::PATH,
        Element::SECTION,
        Element::SELECT,
        Element::SPAN,
        Element::STRIKE,
        Element::STRONG,
        Element::SUB,
        Element::SUP,
        Element::SVG,
        Element::TABLE,
        Element::TBODY,
        Element::TD,
        Element::TEMPLATE,
        Element::TH,
        Element::TIME,
        Element::TITLE,
        Element::TR,
        Element::U,
        Element::UL,
        Element::USE_,
    ];

    const AMP_NESTED_MENU_ALLOWED_DESCENDANTS = [
        Element::A,
        Extension::ACCORDION,
        Extension::IMG,
        Extension::LIST_,
        Element::B,
        Element::BR,
        Element::BUTTON,
        Element::COL,
        Element::COLGROUP,
        Element::DIV,
        Element::EM,
        Element::FIELDSET,
        Element::FORM,
        Element::H1,
        Element::H2,
        Element::H3,
        Element::H4,
        Element::H5,
        Element::H6,
        Element::I,
        Element::INPUT,
        Element::LABEL,
        Element::LI,
        Element::MARK,
        Element::NAV,
        Element::OL,
        Element::OPTION,
        Element::P,
        Element::PATH,
        Element::SECTION,
        Element::SELECT,
        Element::SPAN,
        Element::STRIKE,
        Element::STRONG,
        Element::SUB,
        Element::SUP,
        Element::SVG,
        Element::TABLE,
        Element::TBODY,
        Element::TD,
        Element::TEMPLATE,
        Element::TH,
        Element::TIME,
        Element::TITLE,
        Element::TR,
        Element::U,
        Element::UL,
        Element::USE_,
    ];

    const AMP_STORY_PLAYER_ALLOWED_DESCENDANTS = [
        Element::A,
        Element::SPAN,
        Internal::SIZER,
        Element::IMG,
    ];

    const AMP_STORY_BOOKEND_ALLOWED_DESCENDANTS = [
        Element::SCRIPT,
    ];

    const AMP_STORY_CTA_LAYER_ALLOWED_DESCENDANTS = [
        Element::A,
        Element::ABBR,
        Element::ADDRESS,
        Extension::CALL_TRACKING,
        Extension::DATE_COUNTDOWN,
        Extension::FIT_TEXT,
        Extension::FONT,
        Extension::IMG,
        Extension::TIMEAGO,
        Element::B,
        Element::BDI,
        Element::BDO,
        Element::BLOCKQUOTE,
        Element::BR,
        Element::BUTTON,
        Element::CAPTION,
        Element::CITE,
        Element::CIRCLE,
        Element::CLIPPATH,
        Element::CODE,
        Element::DATA,
        Element::DEFS,
        Element::DEL,
        Element::DESC,
        Element::DFN,
        Element::DIV,
        Element::ELLIPSE,
        Element::EM,
        Element::FECOLORMATRIX,
        Element::FECOMPOSITE,
        Element::FEFLOOD,
        Element::FEGAUSSIANBLUR,
        Element::FEMERGE,
        Element::FEMERGENODE,
        Element::FEOFFSET,
        Element::FIGCAPTION,
        Element::FIGURE,
        Element::FILTER,
        Element::FOOTER,
        Element::G,
        Element::GLYPH,
        Element::GLYPHREF,
        Element::H1,
        Element::H2,
        Element::H3,
        Element::H4,
        Element::H5,
        Element::H6,
        Element::HEADER,
        Element::HGROUP,
        Element::HKERN,
        Element::HR,
        Element::I,
        Element::IMG,
        Internal::SIZER,
        Element::IMAGE,
        Element::INS,
        Element::KBD,
        Element::LI,
        Element::LINE,
        Element::LINEARGRADIENT,
        Element::MAIN,
        Element::MARKER,
        Element::MARK,
        Element::MASK,
        Element::METADATA,
        Element::NAV,
        Element::NOSCRIPT,
        Element::OL,
        Element::P,
        Element::PATH,
        Element::PATTERN,
        Element::PRE,
        Element::POLYGON,
        Element::POLYLINE,
        Element::RADIALGRADIENT,
        Element::Q,
        Element::RECT,
        Element::RP,
        Element::RT,
        Element::RTC,
        Element::RUBY,
        Element::S,
        Element::SAMP,
        Element::SECTION,
        Element::SMALL,
        Element::SOLIDCOLOR,
        Element::SPAN,
        Element::STOP,
        Element::STRONG,
        Element::SUB,
        Element::SUP,
        Element::SVG,
        Element::SWITCH_,
        Element::SYMBOL,
        Element::TEXT,
        Element::TEXTPATH,
        Element::TREF,
        Element::TSPAN,
        Element::TITLE,
        Element::TIME,
        Element::TR,
        Element::U,
        Element::UL,
        Element::USE_,
        Element::VAR_,
        Element::VIEW,
        Element::VKERN,
        Element::WBR,
    ];

    const AMP_STORY_GRID_LAYER_ALLOWED_DESCENDANTS = [
        Element::A,
        Element::ABBR,
        Element::ADDRESS,
        Extension::ANALYTICS,
        Extension::AUDIO,
        Extension::DATE_COUNTDOWN,
        Extension::EXPERIMENT,
        Extension::FIT_TEXT,
        Extension::FONT,
        Extension::GIST,
        Extension::IMG,
        Extension::INSTALL_SERVICEWORKER,
        Extension::LIST_,
        Extension::LIVE_LIST,
        Extension::PIXEL,
        Extension::STATE,
        Extension::STORY_360,
        Extension::STORY_AUTO_ANALYTICS,
        Extension::STORY_INTERACTIVE_BINARY_POLL,
        Extension::STORY_INTERACTIVE_POLL,
        Extension::STORY_INTERACTIVE_QUIZ,
        Extension::STORY_INTERACTIVE_RESULTS,
        Extension::STORY_PANNING_MEDIA,
        Extension::TIMEAGO,
        Extension::TWITTER,
        Extension::VIDEO,
        Element::ARTICLE,
        Element::ASIDE,
        Element::B,
        Element::BDI,
        Element::BDO,
        Element::BLOCKQUOTE,
        Element::BR,
        Element::CAPTION,
        Element::CIRCLE,
        Element::CITE,
        Element::CLIPPATH,
        Element::CODE,
        Element::COL,
        Element::COLGROUP,
        Element::DATA,
        Element::DD,
        Element::DEFS,
        Element::DEL,
        Element::DESC,
        Element::DFN,
        Element::DIV,
        Element::DL,
        Element::DT,
        Element::ELLIPSE,
        Element::EM,
        Element::FECOLORMATRIX,
        Element::FECOMPOSITE,
        Element::FEFLOOD,
        Element::FEGAUSSIANBLUR,
        Element::FEMERGE,
        Element::FEMERGENODE,
        Element::FEOFFSET,
        Element::FIGCAPTION,
        Element::FIGURE,
        Element::FILTER,
        Element::FOOTER,
        Element::G,
        Element::GLYPH,
        Element::GLYPHREF,
        Element::H1,
        Element::H2,
        Element::H3,
        Element::H4,
        Element::H5,
        Element::H6,
        Element::HEADER,
        Element::HGROUP,
        Element::HKERN,
        Element::HR,
        Element::I,
        Element::IMAGE,
        Element::IMG,
        Internal::SIZER,
        Element::INS,
        Element::KBD,
        Element::LI,
        Element::LINE,
        Element::LINEARGRADIENT,
        Element::MAIN,
        Element::MARK,
        Element::MARKER,
        Element::MASK,
        Element::METADATA,
        Element::NAV,
        Element::NOSCRIPT,
        Element::OL,
        Element::P,
        Element::PATH,
        Element::PATTERN,
        Element::POLYGON,
        Element::POLYLINE,
        Element::PRE,
        Element::Q,
        Element::RADIALGRADIENT,
        Element::RECT,
        Element::RP,
        Element::RT,
        Element::RTC,
        Element::RUBY,
        Element::S,
        Element::SAMP,
        Element::SCRIPT,
        Element::SECTION,
        Element::SMALL,
        Element::SOLIDCOLOR,
        Element::SOURCE,
        Element::SPAN,
        Element::STOP,
        Element::STRONG,
        Element::SUB,
        Element::SUP,
        Element::SVG,
        Element::SWITCH_,
        Element::SYMBOL,
        Element::TABLE,
        Element::TBODY,
        Element::TD,
        Element::TEMPLATE,
        Element::TEXT,
        Element::TEXTPATH,
        Element::TFOOT,
        Element::TH,
        Element::THEAD,
        Element::TIME,
        Element::TITLE,
        Element::TR,
        Element::TRACK,
        Element::TREF,
        Element::TSPAN,
        Element::U,
        Element::UL,
        Element::USE_,
        Element::VAR_,
        Element::VIEW,
        Element::VKERN,
        Element::WBR,
    ];

    const AMP_STORY_PAGE_ATTACHMENT_ALLOWED_DESCENDANTS = [
        Element::A,
        Element::ABBR,
        Element::ADDRESS,
        Extension::_3D_GLTF,
        Extension::_3Q_PLAYER,
        Extension::ACCORDION,
        Extension::AUDIO,
        Extension::BEOPINION,
        Extension::BODYMOVIN_ANIMATION,
        Extension::BRID_PLAYER,
        Extension::BRIGHTCOVE,
        Extension::BYSIDE_CONTENT,
        Extension::CALL_TRACKING,
        Extension::CAROUSEL,
        Extension::DAILYMOTION,
        Extension::DATE_COUNTDOWN,
        Extension::EMBEDLY_CARD,
        Extension::FACEBOOK,
        Extension::FACEBOOK_COMMENTS,
        Extension::FACEBOOK_LIKE,
        Extension::FACEBOOK_PAGE,
        Extension::FIT_TEXT,
        Extension::FX_COLLECTION,
        Extension::FX_FLYING_CARPET,
        Extension::GFYCAT,
        Extension::GIST,
        Extension::GOOGLE_DOCUMENT_EMBED,
        Extension::HULU,
        Extension::IMA_VIDEO,
        Extension::IMAGE_SLIDER,
        Extension::IMG,
        Extension::IMGUR,
        Extension::INSTAGRAM,
        Extension::IZLESENE,
        Extension::JWPLAYER,
        Extension::KALTURA_PLAYER,
        Extension::LIST_,
        Extension::LIVE_LIST,
        Extension::MATHML,
        Extension::MEGAPHONE,
        Extension::MOWPLAYER,
        Extension::NEXXTV_PLAYER,
        Extension::O2_PLAYER,
        Extension::OOYALA_PLAYER,
        Extension::PAN_ZOOM,
        Extension::PINTEREST,
        Extension::PLAYBUZZ,
        Extension::POWR_PLAYER,
        Extension::REACH_PLAYER,
        Extension::REDDIT,
        Extension::RIDDLE_QUIZ,
        Extension::SOUNDCLOUD,
        Extension::SPRINGBOARD_PLAYER,
        Extension::TIMEAGO,
        Extension::TWITTER,
        Extension::VIDEO,
        Extension::VIDEO_IFRAME,
        Extension::VIMEO,
        Extension::VINE,
        Extension::VIQEO_PLAYER,
        Extension::VK,
        Extension::WISTIA_PLAYER,
        Extension::YOTPO,
        Extension::YOUTUBE,
        Element::ARTICLE,
        Element::ASIDE,
        Element::B,
        Element::BDI,
        Element::BDO,
        Element::BLOCKQUOTE,
        Element::BR,
        Element::BUTTON,
        Element::CAPTION,
        Element::CIRCLE,
        Element::CITE,
        Element::CLIPPATH,
        Element::CODE,
        Element::COL,
        Element::COLGROUP,
        Element::DATA,
        Element::DD,
        Element::DEFS,
        Element::DEL,
        Element::DESC,
        Element::DFN,
        Element::DIV,
        Element::DL,
        Element::DT,
        Element::ELLIPSE,
        Element::EM,
        Element::FECOLORMATRIX,
        Element::FECOMPOSITE,
        Element::FEFLOOD,
        Element::FEGAUSSIANBLUR,
        Element::FEMERGE,
        Element::FEMERGENODE,
        Element::FEOFFSET,
        Element::FIGCAPTION,
        Element::FIGURE,
        Element::FILTER,
        Element::FOOTER,
        Element::G,
        Element::GLYPH,
        Element::GLYPHREF,
        Element::H1,
        Element::H2,
        Element::H3,
        Element::H4,
        Element::H5,
        Element::H6,
        Element::HEADER,
        Element::HGROUP,
        Element::HKERN,
        Element::HR,
        Element::I,
        Element::IMAGE,
        Element::IMG,
        Internal::SIZER,
        Element::INS,
        Element::KBD,
        Element::LI,
        Element::LINE,
        Element::LINEARGRADIENT,
        Element::MAIN,
        Element::MARK,
        Element::MARKER,
        Element::MASK,
        Element::METADATA,
        Element::NAV,
        Element::OL,
        Element::P,
        Element::PATH,
        Element::PATTERN,
        Element::POLYGON,
        Element::POLYLINE,
        Element::PRE,
        Element::Q,
        Element::RADIALGRADIENT,
        Element::RECT,
        Element::RP,
        Element::RT,
        Element::RTC,
        Element::RUBY,
        Element::S,
        Element::SAMP,
        Element::SECTION,
        Element::SMALL,
        Element::SOLIDCOLOR,
        Element::SOURCE,
        Element::SPAN,
        Element::STOP,
        Element::STRONG,
        Element::SUB,
        Element::SUP,
        Element::SVG,
        Element::SWITCH_,
        Element::SYMBOL,
        Element::TABLE,
        Element::TBODY,
        Element::TD,
        Element::TEXT,
        Element::TEXTPATH,
        Element::TFOOT,
        Element::TH,
        Element::THEAD,
        Element::TIME,
        Element::TITLE,
        Element::TR,
        Element::TRACK,
        Element::TREF,
        Element::TSPAN,
        Element::U,
        Element::UL,
        Element::USE_,
        Element::VAR_,
        Element::VIEW,
        Element::VKERN,
        Element::WBR,
    ];
}
