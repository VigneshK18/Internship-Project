<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            'Laptops' => [
                ['Ultrabook 14 (Intel Core i5, 16 GB, 512 GB SSD)', 58990, 12, 'ultrabook-14', "Thin and light 14-inch laptop with a full-HD IPS display, backlit keyboard and up to 12 hours of battery life.\nWeighs just 1.3 kg - ideal for college and travel."],
                ['Gaming Laptop 15.6 (RTX 4060, 16 GB, 1 TB SSD)', 94990, 5, 'gaming-laptop-15', "144 Hz display, dedicated RTX 4060 graphics and a dual-fan cooling system.\nRuns modern games and creative software smoothly."],
                ['Student Laptop 15 (Ryzen 5, 8 GB, 512 GB SSD)', 42490, 20, 'student-laptop-15', "Reliable everyday laptop for assignments, coding and online classes.\nIncludes a webcam privacy shutter and fast charging."],
            ],
            'Accessories' => [
                ['Wireless Mouse', 799, 42, 'wireless-mouse', 'Silent clicks, 2.4 GHz nano receiver and up to 12 months of battery life.'],
                ['Mechanical Keyboard (Hot-swappable)', 3499, 4, 'mechanical-keyboard', 'Tactile switches, white backlight and a detachable USB-C cable. Switches can be replaced without soldering.'],
                ['USB-C Hub 7-in-1', 1899, 0, 'usb-c-hub', 'HDMI 4K, three USB 3.0 ports, SD and microSD card readers and 100 W pass-through charging.'],
                ['Webcam 1080p with Dual Microphones', 2499, 11, 'webcam-1080p', 'Full-HD video calls with automatic light correction, dual noise-reducing microphones and a privacy cover.'],
                ['Laptop Backpack 15.6"', 1599, 25, 'laptop-backpack', 'Water-resistant backpack with a padded laptop sleeve, USB charging port and anti-theft back pocket.'],
            ],
            'Audio' => [
                ['Noise-cancelling Headphones', 7499, 15, 'anc-headphones', 'Active noise cancellation, 30-hour playback and quick charge: 10 minutes gives 5 hours of music.'],
                ['Bluetooth Speaker (Waterproof)', 2299, 3, 'bluetooth-speaker', 'IPX7 waterproof speaker with deep bass, 12-hour battery and stereo pairing.'],
                ['True Wireless Earbuds', 1999, 30, 'wireless-earbuds', 'Low-latency game mode, touch controls and 36 hours of total playback with the charging case.'],
            ],
            'Displays' => [
                ['27" QHD IPS Monitor', 15999, 9, 'monitor-27-qhd', '2560 x 1440 resolution, 75 Hz refresh rate, slim bezels and an eye-care flicker-free panel.'],
                ['24" Full HD Monitor', 8999, 14, 'monitor-24-fhd', 'Full-HD IPS panel with HDMI and VGA inputs, wall-mount support and low blue-light mode.'],
            ],
            'Storage' => [
                ['1 TB Portable SSD', 6999, 21, 'portable-ssd-1tb', 'Read speeds up to 1,050 MB/s over USB-C. Shock resistant and smaller than a credit card.'],
                ['128 GB USB 3.2 Pen Drive', 899, 60, 'pen-drive-128gb', 'Metal body pen drive with transfer speeds up to 150 MB/s.'],
                ['2 TB External Hard Drive', 5499, 8, 'external-hdd-2tb', 'Plug-and-play backup drive with automatic backup software and USB 3.0.'],
            ],
            'Networking' => [
                ['Dual-band Wi-Fi 6 Router', 3999, 10, 'wifi6-router', 'AX1800 dual-band router with four antennas, parental controls and easy app setup.'],
                ['Wi-Fi USB Adapter (AC600)', 699, 35, 'wifi-usb-adapter', 'Adds dual-band Wi-Fi to any desktop or laptop. Plug and play on Windows and Linux.'],
            ],
            'Wearables' => [
                ['Smartwatch with AMOLED Display', 4999, 7, 'smartwatch', 'Bluetooth calling, heart-rate and SpO2 tracking, 100+ sports modes and 7-day battery.'],
                ['Fitness Band', 2199, 18, 'fitness-band', 'Step, sleep and heart-rate tracking with a 14-day battery and water resistance.'],
            ],
        ];

        foreach ($catalogue as $categoryName => $products) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'description' => "{$categoryName} at great prices."]
            );

            foreach ($products as [$name, $price, $stock, $image, $description]) {
                Product::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'stock' => $stock,
                        'image' => "images/products/{$image}.png",
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
