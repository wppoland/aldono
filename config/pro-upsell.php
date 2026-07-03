<?php
/**
 * PRO upsell content, generated from the plogins.com registry by
 * scripts/gen-pro-upsell.mjs. The admin upsell renders this; curate the
 * feature list to fit this plugin's settings screen (do not invent features).
 *
 * @package plogins-addons-pro
 */

defined('ABSPATH') || exit;

return [
    'name'       => 'Add-Ons Pro',
    'url'        => 'https://plogins.com/plogins-addons-pro/pricing/',
    'sellable'   => true,
    'price_from' => 49,
    'currency'   => 'EUR',
    'price_pln'  => 215,
    'lead'       => [
        'en' => 'The features below ship in the current PRO 0.6.0 release.',
        'pl' => 'Poniższe funkcje są dostępne w bieżącym wydaniu PRO 0.6.0.',
    ],
    'features'   => [
        [
            'en' => ['title' => 'Conditional logic', 'desc' => 'Show or hide product options depending on the customer\'s earlier choices in the product data panel.'],
            'pl' => ['title' => 'Logika warunkowa', 'desc' => 'Pokazuj lub ukrywaj opcje produktu zależnie od wcześniejszych wyborów klienta w panelu danych produktu.'],
        ],
        [
            'en' => ['title' => 'Front-end hiding', 'desc' => 'Inactive options are hidden on the product page and their inputs disabled.'],
            'pl' => ['title' => 'Ukrywanie na froncie', 'desc' => 'Nieaktywne opcje są ukrywane na stronie produktu, a ich pola wyłączone.'],
        ],
        [
            'en' => ['title' => 'Server-side validation', 'desc' => 'Required fields on inactive conditional options are skipped when adding to cart.'],
            'pl' => ['title' => 'Walidacja po stronie serwera', 'desc' => 'Wymagane pola nieaktywnych opcji warunkowych są pomijane przy dodawaniu do koszyka.'],
        ],
        [
            'en' => ['title' => 'File uploads', 'desc' => 'A file field with size limits, allowed extensions, cart validation and order item metadata.'],
            'pl' => ['title' => 'Wysyłanie plików', 'desc' => 'Pole uploadu pliku z limitem rozmiaru, listą dozwolonych rozszerzeń, walidacją koszyka i zapisem w zamówieniu.'],
        ],
        [
            'en' => ['title' => 'Quantity-based options', 'desc' => 'A separate add-on quantity multiplies the selected option price, with min/max limits and cart/order metadata.'],
            'pl' => ['title' => 'Opcje oparte o ilość', 'desc' => 'Osobna ilość add-onu mnoży cenę wybranej opcji, z limitami min/max i zapisem w koszyku oraz zamówieniu.'],
        ],
        [
            'en' => ['title' => 'Swatches', 'desc' => 'Colour or label swatches instead of a plain select list.'],
            'pl' => ['title' => 'Swatche', 'desc' => 'Wizualne swatche kolorów lub etykiet zamiast prostej listy.'],
        ],
        [
            'en' => ['title' => 'Per-option inventory', 'desc' => 'Stock limits for individual add-ons, with sold-out hiding and order-complete deduction.'],
            'pl' => ['title' => 'Magazyn per opcja', 'desc' => 'Limit dostępności dla pojedynczego add-onu z ukrywaniem wyprzedanych opcji.'],
        ],
        [
            'en' => ['title' => 'Per-character text pricing', 'desc' => 'Charge text personalisation by typed character, with optional space exclusion.'],
            'pl' => ['title' => 'Cena tekstu za znak', 'desc' => 'Doliczaj opłatę za wpisany tekst personalizacji, z opcją nieuwzględniania spacji.'],
        ],
    ],
];
