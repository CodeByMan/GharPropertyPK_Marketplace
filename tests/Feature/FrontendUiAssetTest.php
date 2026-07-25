<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendUiAssetTest extends TestCase
{
    public function test_required_ui_assets_are_packaged_with_the_application(): void
    {
        $assets = [
            'images/gharproperty/hero-section.jpg',
            'images/gharproperty/auth/login-property.webp',
            'images/gharproperty/auth/register-property.webp',
            'images/gharproperty/default-user-avatar.svg',
            'images/gharproperty/property-placeholder.svg',
            'images/gharproperty/properties/property-01.webp',
            'images/gharproperty/properties/property-02.webp',
            'images/gharproperty/properties/property-03.webp',
            'images/gharproperty/properties/property-04.webp',
            'images/gharproperty/properties/property-05.webp',
            'images/gharproperty/properties/property-06.webp',
            'images/gharproperty/properties/property-07.webp',
            'images/gharproperty/properties/property-08.webp',
            'images/gharproperty/properties/property-09.webp',
        ];

        foreach ($assets as $asset) {
            $path = public_path($asset);

            $this->assertFileExists($path, "Missing frontend UI asset: {$asset}");
            $this->assertGreaterThan(500, filesize($path), "Frontend UI asset is unexpectedly empty: {$asset}");
        }
    }
}
