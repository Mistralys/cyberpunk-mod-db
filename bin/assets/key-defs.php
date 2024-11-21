<?php

declare(strict_types=1);

namespace CPMDB\Assets;

const KEY_COMMENTS = 'comments';
const KEY_LINKED_MODS = 'linkedMods';
const KEY_SEE_ALSO = 'seeAlso';
const KEY_TAGS = 'tags';
const KEY_AUTHORS = 'authors';
const KEY_ITEM_CATEGORIES = 'itemCategories';
const KEY_MOD = 'mod';
const KEY_URL = 'url';
const KEY_ATELIER = 'atelier';
const KEY_ATELIER_NAME = 'atelierName';
const KEY_SEARCH_TERMS = 'searchTweaks';
const KEY_CAT_TAGS = 'tags';
const KEY_CAT_ITEMS = 'items';
const KEY_SEE_ALSO_LABEL = 'label';
const KEY_SEE_ALSO_URL = 'url';
const KEY_CAT_LABEL = 'label';
const KEY_ITEM_NAME = 'name';
const KEY_ITEM_CODE = 'code';
const KEY_ITEM_TAGS = 'tags';
const KEY_TAGS_ALIASES = 'aliases';
const KEY_TAGS_CATEGORY = 'category';

const KEY_ATELIERS_NAME = 'name';
const KEY_ATELIERS_AUTHORS = 'authors';
const KEY_ATELIERS_URL = 'url';

const KEYS_ORDER = array(
    KEY_MOD => '',
    KEY_URL => '',
    KEY_ATELIER => '',
    KEY_ATELIER_NAME => '',
    KEY_AUTHORS => array(),
    KEY_TAGS => array(),
    KEY_LINKED_MODS => array(),
    KEY_SEE_ALSO => array(),
    KEY_COMMENTS => '',
    KEY_SEARCH_TERMS => '',
    KEY_ITEM_CATEGORIES => array()
);
