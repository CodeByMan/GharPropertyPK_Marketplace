<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingFilterValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_public_listing_filters_are_accepted(): void
    {
        $this->get(route('listing.index', [
            'priceFrom' => 100000,
            'priceTo' => 500000,
            'beds' => 3,
            'baths' => 2,
            'areaFrom' => 50,
            'areaTo' => 300,
        ]))->assertOk();
    }

    public function test_upper_bound_filters_can_be_used_without_lower_bounds(): void
    {
        $this->get(route('listing.index', [
            'priceTo' => 500000,
            'areaTo' => 300,
        ]))->assertOk();
    }

    public function test_array_filter_values_are_rejected(): void
    {
        $this->from(route('listing.index'))
            ->get(route('listing.index').'?beds[]=1')
            ->assertSessionHasErrors('beds');
    }

    public function test_negative_and_excessive_filter_values_are_rejected(): void
    {
        $this->from(route('listing.index'))
            ->get(route('listing.index', [
                'priceFrom' => -1,
                'beds' => 21,
                'baths' => -1,
                'areaTo' => 70000,
            ]))
            ->assertSessionHasErrors(['priceFrom', 'beds', 'baths', 'areaTo']);
    }

    public function test_upper_ranges_cannot_be_lower_than_lower_ranges(): void
    {
        $this->from(route('listing.index'))
            ->get(route('listing.index', [
                'priceFrom' => 500000,
                'priceTo' => 100000,
                'areaFrom' => 300,
                'areaTo' => 100,
            ]))
            ->assertSessionHasErrors(['priceTo', 'areaTo']);
    }
}
