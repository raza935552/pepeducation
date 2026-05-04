<?php

return [
    'home_url' => 'https://biolinxlabs.com',
    'shop_url' => 'https://biolinxlabs.com',
    'name'     => 'BioLinx Labs',

    // Default UTM tags appended to all outbound links
    'utm' => [
        'source'   => 'professorpeptides',
        'medium'   => 'referral',
        'campaign' => 'buy-cta',
    ],

    // Map of professorpeptides peptide slug => biolinxlabs.com product URL
    // Peptides not in this map fall back to home_url
    'product_map' => [
        'bpc-157'           => 'https://biolinxlabs.com/products/bpc-157-5-mg',
        'tb-500'            => 'https://biolinxlabs.com/products/tb-500-10-mg',
        'wolverine-stack'   => 'https://biolinxlabs.com/products/bpc-157-tb-500-blend-20-mg',
        'ghk-cu'            => 'https://biolinxlabs.com/products/ghk-cu-50-mg',
        'cjc-1295-dac'      => 'https://biolinxlabs.com/products/cjc-1295-dac-5-mg',
        'igf-1-lr3'         => 'https://biolinxlabs.com/products/igf-1-lr3-1-mg',
        'ipamorelin'        => 'https://biolinxlabs.com/products/ipamorelin-5-mg',
        'pt-141'            => 'https://biolinxlabs.com/products/pt-141-10-mg',
        'melanotan-ii'      => 'https://biolinxlabs.com/products/melanotan-ii-10-mg',
        'mots-c'            => 'https://biolinxlabs.com/products/mots-c-10-mg',
        'nad-plus'          => 'https://biolinxlabs.com/products/nad-500-mg',
        'tesamorelin'       => 'https://biolinxlabs.com/products/tesamorelin-10-mg',
        'thymosin-alpha-1'  => 'https://biolinxlabs.com/products/thymosin-alpha-1-ta-1-10-mg',
        'cagrilintide'      => 'https://biolinxlabs.com/products/cagrilintide-5-mg',
        'semaglutide'       => 'https://biolinxlabs.com/products/glp-1-semaglutide-10-mg',
        'tirzepatide'       => 'https://biolinxlabs.com/products/glp-1gip-tirzepatide-10-mg',
        'retatrutide'       => 'https://biolinxlabs.com/products/glp-3-rt-tri-agonist-retatrutide-10-mg',
    ],
];
