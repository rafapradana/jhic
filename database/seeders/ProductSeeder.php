<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); 
        
        // Get categories from Product model
        $categories = Product::getCategories();
        
        // Define product name prefixes by category for more realistic names
        $productPrefixes = [
            'Electronics' => ['Smart', 'Digital', 'Wireless', 'Bluetooth', 'HD', 'Ultra', 'Pro'],
            'Fashion' => ['Premium', 'Classic', 'Modern', 'Vintage', 'Casual', 'Formal', 'Designer'],
            'Home & Garden' => ['Eco', 'Organic', 'Natural', 'Premium', 'Deluxe', 'Essential', 'Multi-purpose'],
            'Sports & Outdoors' => ['Pro', 'Athletic', 'Outdoor', 'Professional', 'Training', 'Performance', 'Adventure'],
            'Books' => ['Complete', 'Essential', 'Advanced', 'Beginner\'s', 'Ultimate', 'Comprehensive', 'Practical'],
            'Health & Beauty' => ['Natural', 'Organic', 'Premium', 'Professional', 'Anti-aging', 'Moisturizing', 'Vitamin'],
            'Automotive' => ['Heavy Duty', 'Premium', 'Professional', 'High Performance', 'Universal', 'OEM', 'Replacement'],
            'Food & Beverages' => ['Organic', 'Premium', 'Fresh', 'Natural', 'Artisan', 'Gourmet', 'Traditional'],
            'Toys & Games' => ['Educational', 'Interactive', 'Creative', 'Fun', 'Learning', 'Adventure', 'Classic'],
            'Office Supplies' => ['Professional', 'Premium', 'Ergonomic', 'Multi-function', 'Heavy Duty', 'Compact', 'Wireless']
        ];
        
        // Generate 200 products
        for ($i = 0; $i < 200; $i++) {
            $category = $faker->randomElement($categories);
            $prefix = $faker->randomElement($productPrefixes[$category]);
            
            // Generate realistic product names based on category
            $productNames = [
                'Electronics' => ['Phone', 'Laptop', 'Headphones', 'Speaker', 'Camera', 'Tablet', 'Monitor', 'Keyboard', 'Mouse', 'Charger'],
                'Fashion' => ['T-Shirt', 'Jeans', 'Dress', 'Jacket', 'Shoes', 'Bag', 'Watch', 'Sunglasses', 'Belt', 'Hat'],
                'Home & Garden' => ['Sofa', 'Table', 'Chair', 'Lamp', 'Curtain', 'Pillow', 'Vase', 'Mirror', 'Clock', 'Plant Pot'],
                'Sports & Outdoors' => ['Running Shoes', 'Yoga Mat', 'Dumbbell', 'Bicycle', 'Backpack', 'Water Bottle', 'Tent', 'Sleeping Bag', 'Helmet', 'Gloves'],
                'Books' => ['Programming Guide', 'Cookbook', 'Novel', 'Biography', 'History Book', 'Science Manual', 'Art Book', 'Dictionary', 'Encyclopedia', 'Textbook'],
                'Health & Beauty' => ['Face Cream', 'Shampoo', 'Lipstick', 'Perfume', 'Soap', 'Lotion', 'Serum', 'Mask', 'Cleanser', 'Toner'],
                'Automotive' => ['Engine Oil', 'Brake Pad', 'Air Filter', 'Spark Plug', 'Battery', 'Tire', 'Wiper', 'Headlight', 'Mirror', 'Seat Cover'],
                'Food & Beverages' => ['Coffee', 'Tea', 'Chocolate', 'Cookies', 'Juice', 'Water', 'Snacks', 'Honey', 'Olive Oil', 'Spices'],
                'Toys & Games' => ['Puzzle', 'Board Game', 'Action Figure', 'Doll', 'Building Blocks', 'Car Toy', 'Ball', 'Robot', 'Art Set', 'Musical Toy'],
                'Office Supplies' => ['Pen', 'Notebook', 'Stapler', 'Calculator', 'Folder', 'Paper', 'Marker', 'Scissors', 'Tape', 'Organizer']
            ];
            
            $productName = $faker->randomElement($productNames[$category]);
            $fullProductName = $prefix . ' ' . $productName;
            
            // Generate realistic descriptions
            $descriptions = [
                "High-quality {$productName} with excellent durability and performance. Perfect for daily use and professional applications.",
                "Premium {$productName} designed with modern technology and user-friendly features. Ideal for both beginners and experts.",
                "Innovative {$productName} that combines style and functionality. Made with the finest materials for long-lasting use.",
                "Professional-grade {$productName} with advanced features and superior build quality. Trusted by professionals worldwide.",
                "Eco-friendly {$productName} made from sustainable materials. Perfect choice for environmentally conscious consumers."
            ];
            
            Product::create([
                'name' => $fullProductName,
                'description' => $faker->randomElement($descriptions),
                'price' => $faker->randomFloat(2, 10, 5000), // Price between 10 and 5000 with 2 decimal places
                'stock' => $faker->numberBetween(0, 1000), // Stock between 0 and 1000
                'category' => $category,
                'is_active' => $faker->boolean(85) // 85% chance of being active
            ]);
        }
    }
}
