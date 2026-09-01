<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => '<p>Java Royale Fashion is a New Zealand-based fashion brand offering quality fashion and personalised customer support design services. We work with skilled designers and trusted production partners in Indonesia to create and carefully select pieces for the New Zealand market.</p><p>From ready-to-wear to custom designs, we combine Indonesian craftsmanship with contemporary style, focusing on quality, individuality, and thoughtful design.</p>',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'content' => '<p>Have a question about a product or an order? Reach us using the details on this page, or via WhatsApp for the fastest response.</p>',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<p>This placeholder Privacy Policy should be replaced with your actual policy before going live. It should explain what customer data is collected at checkout (name, email, phone, shipping address) and how it is used and stored.</p>',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms & Conditions',
                'content' => '<p>This placeholder Terms & Conditions page should be replaced with your actual terms before going live, covering orders, payment, shipping, and returns.</p>',
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
