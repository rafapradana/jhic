<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - JHIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-6">
                    <h1 class="text-2xl font-semibold text-gray-900">JHIC Products</h1>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Total:</span>
                        <span id="total-products" class="text-sm font-medium text-gray-900">0 products</span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">Status:</span>
                    <span id="connection-status" class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                        Checking...
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading State -->
        <div id="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading products...</span>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error loading products</h3>
                    <p id="error-message" class="mt-1 text-sm text-red-700"></p>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div id="products-container" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="products-grid">
                <!-- Products will be inserted here by JavaScript -->
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding some products to the database.</p>
        </div>
    </main>

    <script>
        // Fetch and display products
        async function loadProducts() {
            try {
                const response = await fetch('/api/products');
                const data = await response.json();
                
                // Update connection status
                document.getElementById('connection-status').textContent = 'Connected';
                document.getElementById('connection-status').className = 'px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700';
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                
                if (data.products && data.products.length > 0) {
                    // Show products container
                    document.getElementById('products-container').classList.remove('hidden');
                    
                    // Update total count in header
                    document.getElementById('total-products').textContent = `${data.total_products} products`;
                    
                    // Render products
                    renderProducts(data.products);
                } else {
                    // Show empty state
                    document.getElementById('empty-state').classList.remove('hidden');
                }
                
            } catch (error) {
                console.error('Error loading products:', error);
                
                // Update connection status
                document.getElementById('connection-status').textContent = 'Error';
                document.getElementById('connection-status').className = 'px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700';
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                
                // Show error
                document.getElementById('error').classList.remove('hidden');
                document.getElementById('error-message').textContent = error.message;
            }
        }
        
        function renderProducts(products) {
            const grid = document.getElementById('products-grid');
            grid.innerHTML = '';
            
            products.forEach(product => {
                const productCard = `
                    <div class="bg-white border border-gray-200 rounded-lg p-5 hover:border-gray-300 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-base font-medium text-gray-900 leading-tight">${product.name}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded ${product.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'}">
                                ${product.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">${product.description}</p>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Price</span>
                                <span class="text-base font-semibold text-gray-900">Rp ${parseFloat(product.price).toLocaleString('id-ID')}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Stock</span>
                                <span class="text-sm font-medium ${product.stock > 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${product.stock} units
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Category</span>
                                <span class="text-sm font-medium text-gray-700">${product.category}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="flex justify-between text-xs text-gray-400">
                                <span>ID: ${product.id}</span>
                                <span>${new Date(product.created_at).toLocaleDateString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                `;
                grid.innerHTML += productCard;
            });
        }
        
        // Load products when page loads
        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>
</body>
</html>