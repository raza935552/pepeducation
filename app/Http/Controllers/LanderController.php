<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Serves the paid-ad bridge landers (the "Operator Brief" editorial pages).
 *
 * Each lander seasons the shared Meta pixel (via <x-meta-pixel>, which also sets
 * _fbp/_fbc) and its single CTA routes through /go/{slug} (OutboundController),
 * which forwards UTM + the captured fbclid/fbp/fbc (+ email) on to Biolinx so the
 * Purchase CAPI there matches back to the original ad click.
 *
 * Slug => the OutboundLink slug its CTA points at (see database/seeders or the
 * outbound_links table). Both are kept here so the set of valid landers is explicit.
 */
class LanderController extends Controller
{
    /** Public lander slug => CTA outbound-link slug. */
    public const LANDERS = [
        '10-years'            => 'lp-10-years',
        'lying'               => 'lp-lying',
        'coas-worthless'      => 'lp-coas-worthless',
        'suppliers-identical' => 'lp-suppliers-identical',
        'vetted-47'           => 'lp-vetted-47',
        'hunger-fullness'     => 'lp-hunger-fullness',
    ];

    public function show(string $slug): View
    {
        abort_unless(array_key_exists($slug, self::LANDERS), 404);

        return view("landers.{$slug}");
    }
}
