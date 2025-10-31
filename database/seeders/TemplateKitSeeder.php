<?php

namespace Database\Seeders;

use App\Models\TemplateKit;
use Illuminate\Database\Seeder;

class TemplateKitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templateKits = [
            [
                'name' => 'Modern Business Template',
                'description' => 'A comprehensive business template with landing pages, contact forms, and portfolio sections.',
                'category' => 'Business',
                'author' => 'DesignPro',
                'version' => '1.0.0',
                'thumbnail' => 'https://example.com/thumbnails/modern-business.jpg',
                'tags' => ['business', 'modern', 'corporate', 'professional'],
                'files' => ['index.html', 'about.html', 'contact.html', 'styles.css', 'script.js'],
                'price' => 29.99,
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce Store Kit',
                'description' => 'Complete e-commerce template with product listings, shopping cart, and checkout pages.',
                'category' => 'E-commerce',
                'author' => 'ShopMaster',
                'version' => '2.1.0',
                'thumbnail' => 'https://example.com/thumbnails/ecommerce-store.jpg',
                'tags' => ['ecommerce', 'shop', 'store', 'products'],
                'files' => ['shop.html', 'product.html', 'cart.html', 'checkout.html'],
                'price' => 49.99,
                'is_active' => true,
            ],
            [
                'name' => 'Creative Portfolio',
                'description' => 'Showcase your work with this elegant portfolio template featuring galleries and project pages.',
                'category' => 'Portfolio',
                'author' => 'CreativeStudio',
                'version' => '1.5.0',
                'thumbnail' => 'https://example.com/thumbnails/creative-portfolio.jpg',
                'tags' => ['portfolio', 'creative', 'gallery', 'projects'],
                'files' => ['portfolio.html', 'gallery.html', 'project-detail.html'],
                'price' => 19.99,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant & Cafe',
                'description' => 'Delicious template for restaurants, cafes, and food businesses with menu and reservation features.',
                'category' => 'Food & Beverage',
                'author' => 'FoodieDesigns',
                'version' => '1.2.0',
                'thumbnail' => 'https://example.com/thumbnails/restaurant-cafe.jpg',
                'tags' => ['restaurant', 'food', 'menu', 'cafe'],
                'files' => ['home.html', 'menu.html', 'reservations.html', 'contact.html'],
                'price' => 24.99,
                'is_active' => true,
            ],
            [
                'name' => 'Tech Startup Landing',
                'description' => 'Modern landing page for tech startups and SaaS products with feature showcase.',
                'category' => 'Landing Page',
                'author' => 'TechLaunch',
                'version' => '1.0.0',
                'thumbnail' => 'https://example.com/thumbnails/tech-startup.jpg',
                'tags' => ['startup', 'tech', 'saas', 'landing'],
                'files' => ['landing.html', 'features.html', 'pricing.html'],
                'price' => 34.99,
                'is_active' => true,
            ],
            [
                'name' => 'Blog & Magazine',
                'description' => 'Content-rich blog and magazine template with article layouts and category pages.',
                'category' => 'Blog',
                'author' => 'ContentCraft',
                'version' => '2.0.0',
                'thumbnail' => 'https://example.com/thumbnails/blog-magazine.jpg',
                'tags' => ['blog', 'magazine', 'articles', 'news'],
                'files' => ['blog-home.html', 'article.html', 'category.html', 'author.html'],
                'price' => 15.99,
                'is_active' => false,
            ],
        ];

        foreach ($templateKits as $kit) {
            TemplateKit::create($kit);
        }
    }
}
