<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - JHIC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Responsive filter behavior */
        @media (max-width: 1024px) {
            #filters-section {
                display: none !important;
            }
            #filters-section.show-mobile {
                display: block !important;
            }
        }
        
        /* Smooth transitions for filter toggles */
        #filters-section {
            transition: all 0.3s ease-in-out;
        }
        
        /* Filter indicator animations */
        .filter-indicator {
            animation: fadeIn 0.2s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-4 sm:py-6 gap-4">
                <!-- Title -->
                <div class="flex-shrink-0">
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">JHIC Product Management</h1>
                </div>
                
                <!-- Search input - responsive width -->
                <div class="flex-1 max-w-full sm:max-w-md lg:max-w-lg xl:max-w-xl sm:mx-4 lg:mx-6">
                    <div class="relative">
                        <input type="text" 
                               id="search-input" 
                               placeholder="Search products..." 
                               class="w-full px-4 py-2.5 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <!-- Heroicons: magnifying-glass -->
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Add Product Button -->
                <div class="flex-shrink-0">
                    <button id="add-product-btn" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <!-- Plus icon for mobile -->
                        <svg class="h-4 w-4 sm:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span class="hidden sm:inline">Add Product</span>
                        <span class="sm:hidden">Add</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error State -->
        <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Heroicons: x-circle -->
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error loading products</h3>
                    <p id="error-message" class="mt-1 text-sm text-red-700"></p>
                </div>
            </div>
        </div>

        <!-- Results Summary & Active Filters -->
        <div id="results-summary" class="hidden mb-6">
            <!-- Results Count -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-semibold text-gray-900" id="results-count">0 products found</h2>
                    <button id="toggle-filters" class="lg:hidden inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m0 12h9.75m-9.75 0a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 18H7.5" />
                        </svg>
                        <span id="filter-toggle-text">Show Filters</span>
                    </button>
                </div>
                
                <!-- Clear All Filters (when filters are active) -->
                <button id="clear-all-filters" class="hidden text-sm text-red-600 hover:text-red-700 font-medium transition-colors">
                    Clear All Filters
                </button>
            </div>
            
            <!-- Active Filter Badges -->
            <div id="active-filters" class="hidden">
                <div class="flex flex-wrap gap-2" id="filter-badges">
                    <!-- Filter badges will be inserted here -->
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div id="filters-section" class="hidden bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-4">
            <!-- Compact Filter Row -->
            <div class="flex flex-wrap items-end gap-3">
                <!-- Price Range -->
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Price</label>
                    <div class="flex gap-1">
                        <input type="number" id="min-price" placeholder="Min" 
                               class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <span class="text-xs text-gray-400 self-center">-</span>
                        <input type="number" id="max-price" placeholder="Max" 
                               class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Stock Range -->
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Stock</label>
                    <div class="flex gap-1">
                        <input type="number" id="min-stock" placeholder="Min" 
                               class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <span class="text-xs text-gray-400 self-center">-</span>
                        <input type="number" id="max-stock" placeholder="Max" 
                               class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Category -->
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                    <select id="category-filter" 
                            class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="">All Categories</option>
                        <!-- Categories will be populated dynamically -->
                    </select>
                </div>

                <!-- Status -->
                <div class="flex-1 min-w-[100px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select id="status-filter" 
                            class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort By</label>
                    <select id="sort-filter" 
                            class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="updated_at_desc">Last Updated</option>
                        <option value="created_at_desc">Newest First</option>
                        <option value="created_at_asc">Oldest First</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                        <option value="price_asc">Price Low-High</option>
                        <option value="price_desc">Price High-Low</option>
                        <option value="stock_asc">Stock Low-High</option>
                        <option value="stock_desc">Stock High-Low</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button id="clear-filters" 
                            class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-500 transition-all">
                        Clear
                    </button>
                    <button id="apply-filters" 
                            class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading products...</span>
        </div>

        <!-- Products Grid -->
        <div id="products-container" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="products-grid">
                <!-- Products will be inserted here by JavaScript -->
            </div>
            
            <!-- Pagination Container -->
            <div id="pagination-container" class="mt-12 justify-center hidden">
                <!-- Pagination will be inserted here by JavaScript -->
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <!-- Heroicons: archive-box -->
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding some products to the database.</p>
        </div>
    </main>

    <script>
        // Global variables for pagination, search, and filters
        let currentPage = 1;
        let currentSearch = '';
        let totalPages = 1;
        let isLoading = false;
        let currentFilters = {
            minPrice: '',
            maxPrice: '',
            minStock: '',
            maxStock: '',
            category: '',
            status: '',
            sortBy: 'updated_at_desc'
        };

        // Fetch and display products
        async function loadProducts(page = 1, search = '', filters = {}) {
            if (isLoading) return;
            isLoading = true;
            
            try {
                // Show loading
                document.getElementById('loading').classList.remove('hidden');
                document.getElementById('error').classList.add('hidden');
                document.getElementById('empty-state').classList.add('hidden');
                document.getElementById('products-container').classList.add('hidden');
                
                // Build URL with search, pagination, and filter parameters
                const url = new URL('/api/products', window.location.origin);
                if (search) url.searchParams.append('search', search);
                url.searchParams.append('page', page);
                url.searchParams.append('per_page', 12); // 12 products per page
                
                // Add filter parameters
                if (filters.minPrice) url.searchParams.append('min_price', filters.minPrice);
                if (filters.maxPrice) url.searchParams.append('max_price', filters.maxPrice);
                if (filters.minStock) url.searchParams.append('min_stock', filters.minStock);
                if (filters.maxStock) url.searchParams.append('max_stock', filters.maxStock);
                if (filters.category) url.searchParams.append('category', filters.category);
                if (filters.status !== '') url.searchParams.append('is_active', filters.status);
                if (filters.sortBy) url.searchParams.append('sort_by', filters.sortBy);
                
                const response = await fetch(url);
                const data = await response.json();
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                isLoading = false;
                
                if (data.success) {
                    if (data.pagination) {
                        // Update global state
                        currentPage = data.pagination.current_page;
                        currentSearch = search;
                        totalPages = data.pagination.last_page;
                        
                        // Update filter indicators with total count
                        updateFilterIndicators(filters, data.pagination.total);
                        
                        if (data.data && data.data.length > 0) {
                            // Show products container
                            document.getElementById('products-container').classList.remove('hidden');
                            
                            // Render products
                            renderProducts(data.data);
                            
                            // Render pagination
                            renderPagination(data.pagination);
                        } else {
                            // Show empty state
                            document.getElementById('empty-state').classList.remove('hidden');
                            
                            // Hide pagination
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer) {
                paginationContainer.classList.add('hidden');
                paginationContainer.classList.remove('flex');
            }
                        }
                    } else {
                        // Fallback for non-paginated response
                        currentPage = 1;
                        currentSearch = search;
                        totalPages = 1;
                        
                        // Update filter indicators with data length
                        updateFilterIndicators(filters, data.data ? data.data.length : 0);
                        
                        if (data.data && data.data.length > 0) {
                            // Show products container
                            document.getElementById('products-container').classList.remove('hidden');
                            
                            // Render products
                            renderProducts(data.data);
                            
                            // Hide pagination
                            const paginationContainer = document.getElementById('pagination-container');
                            if (paginationContainer) {
                                paginationContainer.classList.add('hidden');
                            }
                        } else {
                            // Show empty state
                            document.getElementById('empty-state').classList.remove('hidden');
                        }
                    }
                } else {
                    // Show empty state
                    document.getElementById('empty-state').classList.remove('hidden');
                    
                    // Hide pagination
                    const paginationContainer = document.getElementById('pagination-container');
                    if (paginationContainer) {
                        paginationContainer.classList.add('hidden');
                        paginationContainer.classList.remove('flex');
                    }
                }
                
            } catch (error) {
                console.error('Error loading products:', error);
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                isLoading = false;
                
                // Show error
                document.getElementById('error').classList.remove('hidden');
                document.getElementById('error-message').textContent = error.message;
            }
        }
        
        function renderProducts(products) {
            const grid = document.getElementById('products-grid');
            
            if (products.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 text-lg mb-2">No products found</div>
                        <div class="text-gray-500 text-sm">Start by adding your first product</div>
                    </div>
                `;
                return;
            }
            
            grid.innerHTML = products.map(product => `
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200 group">
                    <!-- Header with title and actions -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight truncate group-hover:text-blue-600 transition-colors">${product.name}</h3>
                            <div class="flex items-center mt-2 gap-2 flex-wrap">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full ${product.category ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-gray-50 text-gray-500 border border-gray-200'}">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                    </svg>
                                    ${product.category || 'No Category'}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full ${product.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'}">
                                    ${product.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1 ml-4">
                            <button onclick="editProduct(${product.id})" 
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" 
                                    title="Edit Product">
                                <!-- Heroicons: pencil -->
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                            <button onclick="deleteProduct(${product.id}, '${product.name.replace(/'/g, "\\'")}')"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                    title="Delete Product">
                                <!-- Heroicons: trash -->
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-600 text-sm mb-5 line-clamp-2 leading-relaxed">${product.description || 'No description available'}</p>
                    
                    <!-- Price and Stock -->
                    <div class="space-y-4 mb-5">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Price</span>
                            <span class="text-lg font-bold text-gray-900">Rp ${parseFloat(product.price).toLocaleString('id-ID')}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Stock</span>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full ${product.stock > 10 ? 'bg-green-400' : product.stock > 0 ? 'bg-yellow-400' : 'bg-red-400'}"></div>
                                <span class="text-sm font-semibold ${product.stock > 10 ? 'text-green-600' : product.stock > 0 ? 'text-yellow-600' : 'text-red-600'}">
                                    ${product.stock} units
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timestamps -->
                    <div class="pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="space-y-1">
                                <span class="text-gray-400 font-medium uppercase tracking-wide">Created</span>
                                <div class="text-gray-700 font-semibold">
                                    ${new Date(product.created_at).toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric'
                                    })}
                                </div>
                                <div class="text-gray-500">
                                    ${new Date(product.created_at).toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-gray-400 font-medium uppercase tracking-wide">Updated</span>
                                <div class="text-gray-700 font-semibold">
                                    ${new Date(product.updated_at).toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric'
                                    })}
                                </div>
                                <div class="text-gray-500">
                                    ${new Date(product.updated_at).toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Pagination rendering function
        function renderPagination(pagination) {
            const container = document.getElementById('pagination-container');
            if (!container) {
                // Create pagination container if it doesn't exist
                const paginationDiv = document.createElement('div');
                paginationDiv.id = 'pagination-container';
                paginationDiv.className = 'mt-8 flex justify-center';
                document.getElementById('products-container').appendChild(paginationDiv);
            }
            
            const paginationContainer = document.getElementById('pagination-container');
            
            if (pagination.last_page <= 1) {
                paginationContainer.classList.add('hidden');
                paginationContainer.classList.remove('flex');
                return;
            }
            
            paginationContainer.classList.remove('hidden');
            paginationContainer.classList.add('flex');
            
            let paginationHTML = '<div class="flex items-center space-x-2">';
            
            // Previous button
            if (pagination.current_page > 1) {
                paginationHTML += `
                    <button onclick="loadProducts(${pagination.current_page - 1}, '${currentSearch}', currentFilters)" 
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Previous
                    </button>
                `;
            }
            
            // Page numbers
            const startPage = Math.max(1, pagination.current_page - 2);
            const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.current_page) {
                    paginationHTML += `
                        <button class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                            ${i}
                        </button>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="loadProducts(${i}, '${currentSearch}', currentFilters)" 
                                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            ${i}
                        </button>
                    `;
                }
            }
            
            // Next button
            if (pagination.current_page < pagination.last_page) {
                paginationHTML += `
                    <button onclick="loadProducts(${pagination.current_page + 1}, '${currentSearch}', currentFilters)" 
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Next
                    </button>
                `;
            }
            
            paginationHTML += '</div>';
            paginationContainer.innerHTML = paginationHTML;
        }

        // Filter functionality
        function getFiltersFromForm() {
            return {
                minPrice: document.getElementById('min-price').value,
                maxPrice: document.getElementById('max-price').value,
                minStock: document.getElementById('min-stock').value,
                maxStock: document.getElementById('max-stock').value,
                category: document.getElementById('category-filter').value,
                status: document.getElementById('status-filter').value,
                sortBy: document.getElementById('sort-filter').value
            };
        }

        function applyFilters() {
            currentFilters = getFiltersFromForm();
            loadProducts(1, currentSearch, currentFilters); // Reset to page 1 when filtering
        }

        function clearFilters() {
            document.getElementById('min-price').value = '';
            document.getElementById('max-price').value = '';
            document.getElementById('min-stock').value = '';
            document.getElementById('max-stock').value = '';
            document.getElementById('category-filter').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('sort-filter').value = 'updated_at_desc';
            
            currentFilters = {
                minPrice: '',
                maxPrice: '',
                minStock: '',
                maxStock: '',
                category: '',
                status: '',
                sortBy: 'updated_at_desc'
            };
            
            loadProducts(1, currentSearch, currentFilters);
        }

        // Update filter indicators and count
        function updateFilterIndicators(filters, totalResults = 0) {
            const indicatorsContainer = document.getElementById('active-filters');
            const countElement = document.getElementById('results-count');
            const clearAllBtn = document.getElementById('clear-all-filters');
            
            // Update results count
            if (countElement) {
                countElement.textContent = `${totalResults} products found`;
            }
            
            // Clear existing indicators
            indicatorsContainer.innerHTML = '';
            
            let hasActiveFilters = false;
            
            // Price range filter
            if (filters.minPrice || filters.maxPrice) {
                hasActiveFilters = true;
                const priceText = filters.minPrice && filters.maxPrice 
                    ? `Price: Rp ${parseInt(filters.minPrice).toLocaleString('id-ID')} - Rp ${parseInt(filters.maxPrice).toLocaleString('id-ID')}`
                    : filters.minPrice 
                        ? `Price: ≥ Rp ${parseInt(filters.minPrice).toLocaleString('id-ID')}`
                        : `Price: ≤ Rp ${parseInt(filters.maxPrice).toLocaleString('id-ID')}`;
                        
                indicatorsContainer.innerHTML += `
                    <span class="filter-indicator inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded-full">
                        ${priceText}
                        <button onclick="clearPriceFilter()" class="ml-1 hover:bg-blue-200 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                `;
            }
            
            // Stock range filter
            if (filters.minStock || filters.maxStock) {
                hasActiveFilters = true;
                const stockText = filters.minStock && filters.maxStock 
                    ? `Stock: ${filters.minStock} - ${filters.maxStock} units`
                    : filters.minStock 
                        ? `Stock: ≥ ${filters.minStock} units`
                        : `Stock: ≤ ${filters.maxStock} units`;
                        
                indicatorsContainer.innerHTML += `
                    <span class="filter-indicator inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded-full">
                        ${stockText}
                        <button onclick="clearStockFilter()" class="ml-1 hover:bg-green-200 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                `;
            }
            
            // Category filter
            if (filters.category) {
                hasActiveFilters = true;
                indicatorsContainer.innerHTML += `
                    <span class="filter-indicator inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200 rounded-full">
                        Category: ${filters.category}
                        <button onclick="clearCategoryFilter()" class="ml-1 hover:bg-purple-200 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                `;
            }
            
            // Status filter
            if (filters.status) {
                hasActiveFilters = true;
                const statusText = filters.status === '1' ? 'Active' : 'Inactive';
                const statusColor = filters.status === '1' ? 'green' : 'red';
                indicatorsContainer.innerHTML += `
                    <span class="filter-indicator inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-${statusColor}-50 text-${statusColor}-700 border border-${statusColor}-200 rounded-full">
                        Status: ${statusText}
                        <button onclick="clearStatusFilter()" class="ml-1 hover:bg-${statusColor}-200 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                `;
            }

            // Sort filter (only show if not default)
            if (filters.sortBy && filters.sortBy !== 'updated_at_desc') {
                hasActiveFilters = true;
                const sortLabels = {
                    'updated_at_desc': 'Last Updated',
                    'created_at_desc': 'Newest First',
                    'created_at_asc': 'Oldest First',
                    'name_asc': 'Name A-Z',
                    'name_desc': 'Name Z-A',
                    'price_asc': 'Price Low-High',
                    'price_desc': 'Price High-Low',
                    'stock_asc': 'Stock Low-High',
                    'stock_desc': 'Stock High-Low'
                };
                const sortText = sortLabels[filters.sortBy] || filters.sortBy;
                indicatorsContainer.innerHTML += `
                    <span class="filter-indicator inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 rounded-full">
                        Sort: ${sortText}
                        <button onclick="clearSortFilter()" class="ml-1 hover:bg-orange-200 rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                `;
            }
            
            // Show/hide clear all button
            if (clearAllBtn) {
                clearAllBtn.style.display = hasActiveFilters ? 'inline-flex' : 'none';
            }
        }

        // Individual filter clear functions
        function clearPriceFilter() {
            document.getElementById('min-price').value = '';
            document.getElementById('max-price').value = '';
            applyFilters();
        }

        function clearStockFilter() {
            document.getElementById('min-stock').value = '';
            document.getElementById('max-stock').value = '';
            applyFilters();
        }

        function clearCategoryFilter() {
            document.getElementById('category-filter').value = '';
            applyFilters();
        }

        function clearStatusFilter() {
            document.getElementById('status-filter').value = '';
            applyFilters();
        }

        function clearSortFilter() {
            document.getElementById('sort-filter').value = 'updated_at_desc';
            applyFilters();
        }

        // Toggle filters visibility on mobile
        function toggleFilters() {
            const filtersSection = document.getElementById('filters-section');
            const toggleBtn = document.getElementById('toggle-filters');
            const toggleText = document.getElementById('filter-toggle-text');
            const toggleSvg = toggleBtn.querySelector('svg');
            
            // Check if we're on mobile
            const isMobile = window.innerWidth < 1024;
            
            if (isMobile) {
                // Mobile behavior - toggle show-mobile class
                const isVisible = filtersSection.classList.contains('show-mobile');
                
                if (isVisible) {
                    filtersSection.classList.remove('show-mobile');
                    toggleText.textContent = 'Show Filters';
                    toggleSvg.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m0 12h9.75m-9.75 0a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 18H7.5" />
                    `;
                } else {
                    filtersSection.classList.add('show-mobile');
                    toggleText.textContent = 'Show Filters';
                    toggleSvg.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    `;
                }
            } else {
                // Desktop behavior - toggle hidden class
                const isHidden = filtersSection.classList.contains('hidden');
                
                if (isHidden) {
                    filtersSection.classList.remove('hidden');
                    toggleText.textContent = 'Show Filters';
                    toggleSvg.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    `;
                } else {
                    filtersSection.classList.add('hidden');
                    toggleText.textContent = 'Show Filters';
                    toggleSvg.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m0 12h9.75m-9.75 0a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 18H7.5" />
                    `;
                }
            }
        }

        // Load unique categories for filter dropdown
        async function loadCategories() {
            try {
                const response = await fetch('/api/products-categories');
                const data = await response.json();
                
                if (data.success && data.data) {
                    const categorySelect = document.getElementById('category-filter');
                    const currentValue = categorySelect.value;
                    
                    // Clear existing options except "All Categories"
                    categorySelect.innerHTML = '<option value="">All Categories</option>';
                    
                    // Add category options
                    data.data.forEach(category => {
                        if (category) { // Only add non-empty categories
                            const option = document.createElement('option');
                            option.value = category;
                            option.textContent = category;
                            categorySelect.appendChild(option);
                        }
                    });
                    
                    // Restore previous selection
                    categorySelect.value = currentValue;
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }
        
        // Search functionality
        let searchTimeout;
        document.getElementById('search-input').addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.trim();
                currentSearch = searchTerm;
                loadProducts(1, searchTerm, currentFilters); // Reset to page 1 when searching
            }, 300); // Debounce search for 300ms
        });
        
        // Load products when page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Show filters section
            document.getElementById('filters-section').classList.remove('hidden');
            
            // Load categories and products
            loadCategories();
            loadProducts();
            
            // Add filter event listeners
            document.getElementById('apply-filters').addEventListener('click', applyFilters);
            document.getElementById('clear-filters').addEventListener('click', clearFilters);
        });
    </script>

    <!-- Product Form Modal -->
    <div id="product-modal" class="hidden fixed inset-0 overflow-y-auto h-full w-full z-50 p-4">
        <div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl border border-gray-200">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-lg font-medium text-gray-900">Add Product</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <!-- Heroicons: x-mark -->
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="product-form">
                    <input type="hidden" id="product-id" value="">
                    
                    <div class="mb-4">
                        <label for="product-name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="product-name" name="name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="product-description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <input type="number" id="product-price" name="price" required min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-stock" class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" id="product-stock" name="stock" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="product-category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="product-category" name="category" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Fashion">Fashion</option>
                            <option value="Home & Garden">Home & Garden</option>
                            <option value="Sports & Outdoors">Sports & Outdoors</option>
                            <option value="Books">Books</option>
                            <option value="Health & Beauty">Health & Beauty</option>
                            <option value="Automotive">Automotive</option>
                            <option value="Food & Beverages">Food & Beverages</option>
                            <option value="Toys & Games">Toys & Games</option>
                            <option value="Office Supplies">Office Supplies</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label for="product-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="product-status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-btn" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" id="submit-btn"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md flex items-center justify-center">
                            <span id="submit-text">Save</span>
                            <svg id="submit-spinner" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 overflow-y-auto h-full w-full z-50 p-4">
        <div class="relative w-full max-w-sm bg-white rounded-lg shadow-xl border border-gray-200">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <!-- Heroicons: exclamation-triangle -->
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Product</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete "<span id="delete-product-name"></span>"? This action cannot be undone.</p>
                
                <div class="flex justify-center space-x-3">
                    <button id="cancel-delete" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancel
                    </button>
                    <button id="confirm-delete"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal management
        const productModal = document.getElementById('product-modal');
        const deleteModal = document.getElementById('delete-modal');
        const productForm = document.getElementById('product-form');
        
        let currentProductId = null;
        let deleteProductId = null;

        // Show/hide modals
        function showModal(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'items-center', 'justify-center');
        }

        function hideModal(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'items-center', 'justify-center');
        }

        // Event listeners for modal controls
        document.getElementById('add-product-btn').addEventListener('click', () => {
            resetForm();
            document.getElementById('modal-title').textContent = 'Add Product';
            showModal(productModal);
        });

        document.getElementById('close-modal').addEventListener('click', () => {
            hideModal(productModal);
        });

        document.getElementById('cancel-btn').addEventListener('click', () => {
            hideModal(productModal);
        });

        document.getElementById('cancel-delete').addEventListener('click', () => {
            hideModal(deleteModal);
        });

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === productModal) {
                hideModal(productModal);
            }
            if (e.target === deleteModal) {
                hideModal(deleteModal);
            }
        });

        // Reset form
        function resetForm() {
            productForm.reset();
            document.getElementById('product-id').value = '';
            currentProductId = null;
        }

        // Form submission
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitSpinner = document.getElementById('submit-spinner');
            
            submitBtn.disabled = true;
            submitText.textContent = 'Saving...';
            submitSpinner.classList.remove('hidden');
            
            const formData = new FormData(productForm);
            const data = Object.fromEntries(formData);
            
            // Debug: log the data being sent
            console.log('Form data being sent:', data);
            
            try {
                const url = currentProductId ? `/api/products/${currentProductId}` : '/api/products';
                const method = currentProductId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    hideModal(productModal);
                    loadProducts(currentPage, currentSearch, currentFilters); // Reload products with current state
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'An error occurred', 'error');
                }
            } catch (error) {
                console.error('Error saving product:', error);
                showNotification('Failed to save product', 'error');
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                submitText.textContent = 'Save';
                submitSpinner.classList.add('hidden');
            }
        });

        // Edit product
        function editProduct(id) {
            fetch(`/api/products/${id}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const product = result.data;
                        currentProductId = id;
                        
                        document.getElementById('product-id').value = id;
                        document.getElementById('product-name').value = product.name;
                        document.getElementById('product-description').value = product.description || '';
                        document.getElementById('product-price').value = product.price;
                        document.getElementById('product-stock').value = product.stock;
                        document.getElementById('product-category').value = product.category || '';
                        document.getElementById('product-status').value = product.is_active ? 'active' : 'inactive';
                        
                        document.getElementById('modal-title').textContent = 'Edit Product';
                        showModal(productModal);
                    }
                })
                .catch(error => {
                    console.error('Error loading product:', error);
                    showNotification('Failed to load product', 'error');
                });
        }

        // Delete product
        function deleteProduct(id, name) {
            deleteProductId = id;
            document.getElementById('delete-product-name').textContent = name;
            showModal(deleteModal);
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            try {
                const response = await fetch(`/api/products/${deleteProductId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    hideModal(deleteModal);
                    loadProducts(currentPage, currentSearch, currentFilters); // Reload products with current state
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'Failed to delete product', 'error');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showNotification('Failed to delete product', 'error');
            }
        });

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // Event listeners for filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filters button
            const toggleFiltersBtn = document.getElementById('toggle-filters');
            if (toggleFiltersBtn) {
                toggleFiltersBtn.addEventListener('click', toggleFilters);
            }

            // Clear all filters button
            const clearAllFiltersBtn = document.getElementById('clear-all-filters');
            if (clearAllFiltersBtn) {
                clearAllFiltersBtn.addEventListener('click', function() {
                    clearFilters();
                });
            }

            // Initialize filter indicators on page load
            updateFilterIndicators(currentFilters, 0);
        });
    </script>
</body>
</html>