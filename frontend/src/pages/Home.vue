<template>
  <div class="min-h-screen">
    <!-- Navbar -->
    <nav class="navbar sticky top-0 z-50">
      <div class="max-w-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <!-- Logo -->
          <div class="flex items-center group">
            <img src="/logo.png" alt="SpnrCo Logo" class="h-12 w-12 mr-3 rounded-full transition-transform duration-300 group-hover:scale-110" />
            <h1 class="text-2xl font-bold text-gradient">SpnrCo</h1>
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-8">
            <a href="#home" class="navbar-link">Home</a>
            <a href="#shops" class="navbar-link">Laundry Shops</a>
            <a href="#services" class="navbar-link">Services</a>
            <a href="#contact" class="navbar-link">Contact</a>
          </div>

          <!-- Desktop Buttons -->
          <div class="hidden md:flex items-center space-x-4">
            <router-link to="/login" class="navbar-link">Login</router-link>
            <router-link to="/register" class="btn-primary">Book Now</router-link>
          </div>

          <!-- Mobile Menu Button -->
          <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="navbar-link p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t py-4 mobile-menu">
          <a href="#home" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Home</a>
          <a href="#shops" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Laundry Shops</a>
          <a href="#services" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Services</a>
          <a href="#contact" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Contact</a>
          <router-link to="/login" @click="mobileMenuOpen = false" class="block px-4 py-3 navbar-link hover:bg-spnr-blue-50">Login</router-link>
          <router-link to="/register" @click="mobileMenuOpen = false" class="block mx-4 my-2 btn-primary text-center">Book Now</router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section (desktop or compact) -->
    <section id="home" class="hero-section">
      <div class="max-w-7xl mx-auto px-4 py-12 lg:py-32">
        <transition name="hero-fade" mode="out-in">
          <template v-if="!isCompact">
            <div key="desktop" class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
              <!-- Left Column -->
              <div class="space-y-8 hero-content">
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight">
                  <span class="text-gradient">Professional Laundry Services</span>
                  <span class="text-gradient block mt-3">At Your Doorstep</span>
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed max-w-xl">
                  Experience convenience like never before. Book premium laundry services from trusted professionals and get your clothes picked up, cleaned, and delivered right to your door.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                  <router-link to="/register" class="btn-primary text-lg px-10 py-4">
                    Get Started →
                  </router-link>
                  <a href="#services" class="btn-secondary text-lg px-10 py-4">
                    View Services
                  </a>
                </div>
              </div>

              <!-- Right Column -->
              <div class="flex justify-center lg:justify-end hero-image">
                <div class="relative w-full max-w-lg">
                  <div class="absolute -top-4 -left-4 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                  <div class="absolute -bottom-8 right-4 w-72 h-72 bg-cyan-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                  <div class="relative">
                    <img src="/home-logo.jpg" alt="SpnrCo Laundry & Delivery App" class="w-full h-auto relative z-10 rounded-full shadow-2xl" />
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- Compact hero: preserves UI elements and transitions but uses smaller sizes -->
          <template v-else>
            <div key="compact" class="grid grid-cols-2   md:grid-cols-2 gap-8 items-center">
              <!-- Left: compact text with logo to the right -->
              <div class="space-y-6">
                <div class="flex items-start justify-between">
                  <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight">
                      <span class="text-gradient">Professional Laundry Services</span>
                      <span class="text-gradient block mt-2">At Your Doorstep</span>
                    </h1>
                    <p class="text-xs text-gray-600 leading-relaxed max-w-md">
                      Book premium laundry services from trusted professionals. Pick up, clean, and deliver right to your door — fast and easy.
                    </p>
                  </div>
                  
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-3">
                  <router-link to="/register" class="btn-primary px-6 py-3 text-sm">
                    Get Started →
                  </router-link>
                  <a href="#services" class="btn-secondary px-6 py-3 text-sm">
                    View Services
                  </a>
                </div>
              </div>

              <!-- Right: compact image with same blobs/animation -->
              <div class="flex justify-center md:justify-end">
                <div class="relative w-64 max-w-xs">
                  <div class="absolute -top-3 -left-3 w-40 h-40 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                  <div class="absolute -bottom-6 right-2 w-40 h-40 bg-cyan-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                  <div class="relative">
                    <img src="/home-logo.jpg" alt="SpnrCo Laundry & Delivery App" class="w-full h-auto relative z-10 rounded-full shadow-2xl" />
                  </div>
                </div>
              </div>
            </div>
          </template>
        </transition>
      </div>
    </section>

    <!-- Why Choose Section -->
    <section :class="isCompact ? 'py-12 px-4 bg-white ' : 'py-10 px-4 bg-white'">
      <div class="max-w-7xl mx-auto">
        <div :class="isCompact ? 'text-center mb-8' : 'text-center mb-20'">
          
          <h2 :class="isCompact ? 'text-2xl font-extrabold text-gray-900 mb-3 mt-2' : 'text-5xl font-extrabold text-gray-900 mb-6 mt-3'">Why Choose SpnrCo?</h2>
          <p :class="isCompact ? 'text-sm text-gray-600 max-w-2xl mx-auto' : 'text-xl text-gray-600 max-w-2xl mx-auto'">Your trusted partner for hassle-free laundry solutions</p>
        </div>

        <div v-if="isCompact" class="relative flex items-center justify-center">
          <button 
            @click="scrollFeatures('left')" 
            class="absolute left-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll left">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <div ref="featuresContainer" class="flex gap-3 overflow-x-auto pb-3 py-3 px-4 snap-x snap-mandatory scroll-smooth hide-scrollbar " style="max-width: 100%;">
          <!-- Feature 1 -->
          <div class="feature-card group flex-shrink-0 w-56 snap-center border border-blue-300">
            <div class="w-12 h-12  rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-6 h-6 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Door-to-Door Service</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Free pickup and delivery at your preferred time. No more trips to the laundromat.</p>
          </div>

          <!-- Feature 2 -->
          <div class="feature-card group flex-shrink-0 w-56 snap-center border border-blue-300">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-100 to-cyan-200 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-6 h-6 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Professional Quality</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Verified professionals using premium detergents and equipment for spotless results.</p>
          </div>

          <!-- Feature 3 -->
          <div class="feature-card group flex-shrink-0 w-56 snap-center border border-blue-300">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-6 h-6 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Real-Time Tracking</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Track your order status from pickup to delivery with live updates.</p>
          </div>
          </div>

          <button 
            @click="scrollFeatures('right')" 
            class="absolute right-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll right">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <div v-else class="grid grid-cols-3 gap-8">
          <div class="feature-card group border border-blue-300">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-10 h-10 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Door-to-Door Service</h3>
            <p class="text-gray-600 leading-relaxed">Free pickup and delivery at your preferred time. No more trips to the laundromat.</p>
          </div>

          <div class="feature-card group border border-blue-300">
            <div class="w-20 h-20 bg-gradient-to-br from-cyan-100 to-cyan-200 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-10 h-10 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Professional Quality</h3>
            <p class="text-gray-600 leading-relaxed">Verified professionals using premium detergents and equipment for spotless results.</p>
          </div>

          <div class="feature-card group border border-blue-300">
            <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-10 h-10 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Real-Time Tracking</h3>
            <p class="text-gray-600 leading-relaxed">Track your order status from pickup to delivery with live updates.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Shops Section -->
    <section id="shops" :class="isCompact ? 'py-12 px-4 bg-white' : 'py-20 px-4 bg-white'">
      <div class="max-w-7xl mx-auto">
        <div :class="isCompact ? 'text-center mb-8' : 'text-center mb-16'">
          <h2 :class="isCompact ? 'text-2xl font-bold text-gray-900 mb-2' : 'text-4xl font-bold text-gray-900 mb-4'">Featured Laundry Shops</h2>
          <p :class="isCompact ? 'text-sm text-gray-600' : 'text-lg text-gray-600'">Trusted partners delivering exceptional service</p>
        </div>

        <div v-if="loadingShops" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
        </div>

        <div v-else-if="isCompact" class="relative flex items-center justify-center">
          <button 
            @click="scrollShops('left')" 
            class="absolute left-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll left">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <div ref="shopsContainer" class="flex gap-4 overflow-x-auto pb-4 px-10 snap-x snap-mandatory scroll-smooth hide-scrollbar" style="max-width: 100%;">
            <div v-for="shop in shops" :key="shop.id" class="bg-white rounded-xl lg:shadow-lg shadow-md overflow-hidden hover:shadow-2xl transition-shadow flex-shrink-0 w-64 snap-center border border-blue-300 flex flex-col">
              <div class="w-full h-32 bg-gray-200">
                <img 
                  v-if="shop.photo_path" 
                  :src="shop.photo_path" 
                  :alt="shop.shop_name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-24 h-24 text-spnr-blue-900 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
              </div>

              <div class="p-3 flex flex-col flex-grow">
                <h3 class="text-base font-bold text-gray-900 mb-1">{{ shop.shop_name }}</h3>
                <p class="text-xs text-gray-600 mb-2 line-clamp-2 min-h-[2rem]">{{ shop.description || 'Professional laundry services with quick turnaround' }}</p>
                
                <div class="space-y-1.5 mb-3">
                  <div class="flex items-start">
                    <svg class="w-3 h-3 mr-1.5 text-spnr-blue-900 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-xs text-gray-700">{{ shop.address }}</span>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-3 h-3 mr-1.5 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="text-xs text-gray-700">{{ shop.contact_number }}</span>
                  </div>
                </div>

                <router-link to="/register" class="block w-full bg-spnr-blue-900 text-white text-center py-2 rounded-lg hover:bg-spnr-blue-800 transition-colors font-semibold text-xs mt-auto">
                  Book Service
                </router-link>
              </div>
            </div>
          </div>

          <button 
            @click="scrollShops('right')" 
            class="absolute right-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll right">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 ">
          <div v-for="shop in shops" :key="shop.id" class="bg-white rounded-xl lg:shadow-lg shadow-md overflow-hidden hover:shadow-2xl transition-shadow border border-blue-300 flex flex-col">
            <div class="w-full h-56 bg-gray-200">
              <img 
                v-if="shop.photo_path" 
                :src="shop.photo_path" 
                :alt="shop.shop_name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-24 h-24 text-spnr-blue-900 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
            </div>

            <div class="p-6 flex flex-col flex-grow">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ shop.shop_name }}</h3>
              <p class="text-gray-600 mb-4 line-clamp-2 min-h-[3rem]">{{ shop.description || 'Professional laundry services with quick turnaround' }}</p>
              
              <div class="space-y-3 mb-6">
                <div class="flex items-start">
                  <svg class="w-5 h-5 mr-3 text-spnr-blue-900 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="text-sm text-gray-700">{{ shop.address }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 mr-3 text-spnr-blue-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  <span class="text-sm text-gray-700">{{ shop.contact_number }}</span>
                </div>
              </div>

              <router-link to="/register" class="block w-full bg-spnr-blue-900 text-white text-center py-3 rounded-lg hover:bg-spnr-blue-800 transition-colors font-semibold mt-auto">
                Book Service
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>
    

    <!-- Stats Section -->
    <section :class="isCompact ? 'stats-section py-12 px-4' : 'stats-section py-10 px-4' ">
      <div class="max-w-7xl mx-auto">
        <div v-if="isCompact" class="relative flex items-center justify-center">
          <!-- Left Arrow -->
          <button 
            @click="scrollStats('left')" 
            class="absolute left-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll left">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <!-- Stats Container -->
          <div ref="statsContainer" class="e flex gap-4 overflow-x-auto pb-4 px-10 snap-x snap-mandatory scroll-smooth hide-scrollbar" style="max-width: 100%;">
            <!-- Stat 1 -->
            <div class="stat-card text-center flex-shrink-0 w-32 snap-center border border-blue-300">
              <h3 class="text-3xl font-extrabold text-gradient mb-2">{{ stats.shops }}+</h3>
              <p class="text-gray-600 font-semibold text-xs">Shop Partners</p>
            </div>

            <!-- Stat 2 -->
            <div class="stat-card text-center flex-shrink-0 w-32 snap-center border border-blue-300">
              <h3 class="text-3xl font-extrabold text-gradient mb-2">{{ stats.services }}+</h3>
              <p class="text-gray-600 font-semibold text-xs">Services Made</p>
            </div>

            <!-- Stat 3 -->
            <div class="stat-card text-center flex-shrink-0 w-32 snap-center border border-blue-300">
              <h3 class="text-3xl font-extrabold text-gradient mb-2">24+</h3>
              <p class="text-gray-600 font-semibold text-xs">Service Areas</p>
            </div>

            <!-- Stat 4 -->
            <div class="stat-card text-center flex-shrink-0 w-32 snap-center border border-blue-300">
              <h3 class="text-3xl font-extrabold text-gradient mb-2">{{ stats.orders }}+</h3>
              <p class="text-gray-600 font-semibold text-xs">Happy Customers</p>
            </div>
          </div>

          <!-- Right Arrow -->
          <button 
            @click="scrollStats('right')" 
            class="absolute right-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll right">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Desktop Grid -->
        <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-8 ">
          <!-- Stat 1 -->
          <div class="stat-card text-center border border-blue-300">
            <h3 class="text-6xl font-extrabold text-gradient mb-3">{{ stats.shops }}+</h3>
            <p class="text-gray-600 font-semibold text-lg">Shop Partners</p>
          </div>

          <!-- Stat 2 -->
          <div class="stat-card text-center border border-blue-300">
            <h3 class="text-6xl font-extrabold text-gradient mb-3">{{ stats.services }}+</h3>
            <p class="text-gray-600 font-semibold text-lg">Verified Washers</p>
          </div>

          <!-- Stat 3 -->
          <div class="stat-card text-center border border-blue-300">
            <h3 class="text-6xl font-extrabold text-gradient mb-3">24+</h3>
            <p class="text-gray-600 font-semibold text-lg">Service Areas</p>
          </div>

          <!-- Stat 4 -->
          <div class="stat-card text-center border border-blue-300">
            <h3 class="text-6xl font-extrabold text-gradient mb-3">{{ stats.orders }}+</h3>
            <p class="text-gray-600 font-semibold text-lg">Happy Customers</p>
          </div>
        </div>
      </div>
    </section>


    <!-- Services Section -->
    <section id="services" :class="isCompact ? 'services-section py-12 px-4' : 'services-section py-20 px-4'" style="position: relative; z-index: 1;">
      <div class="max-w-7xl mx-auto">
        <div :class="isCompact ? 'text-center mb-8' : 'text-center mb-16'">
          <h2 :class="isCompact ? 'text-2xl font-bold text-gray-900 mb-2' : 'text-4xl font-bold text-gray-900 mb-4'">Our Services</h2>
          <p :class="isCompact ? 'text-sm text-gray-600' : 'text-lg text-gray-600'">Professional laundry solutions for every need</p>
        </div>

        <!-- Loading State -->
        <div v-if="loadingCategories" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-spnr-blue-900"></div>
          <p class="mt-4 text-gray-600">Loading services...</p>
        </div>

        <!-- Compact View -->
        <div v-else-if="isCompact" class="relative flex items-center justify-center">
          <button 
            @click="scrollServices('left')" 
            class="absolute left-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll left">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <div ref="servicesContainer" class="flex gap-3 overflow-x-auto pb-4 px-10 snap-x snap-mandatory scroll-smooth hide-scrollbar" style="max-width: 100%;">
            <router-link 
              v-for="category in categoriesWithServices.slice(0, 8)" 
              :key="category.id" 
              :to="`/category/${category.id}/services`" 
              class="bg-white rounded-lg shadow-md text-center hover:shadow-lg transition-shadow flex-shrink-0 w-36 snap-center p-3 border border-blue-300 cursor-pointer flex flex-col no-underline">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/>
                </svg>
              </div>
              <h3 class="text-xs font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
              <p class="text-xs text-gray-600 mb-1 line-clamp-2">{{ category.description || 'Professional laundry service' }}</p>
              <p class="text-xs text-spnr-blue-900 font-medium mt-auto">{{ category.services_count }} services</p>
            </router-link>
          </div>

          <button 
            @click="scrollServices('right')" 
            class="absolute right-0 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
            aria-label="Scroll right">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Desktop View -->
        <div v-else class="grid grid-cols-4 gap-6" style="position: relative; z-index: 2;">
          <router-link 
            v-for="category in categoriesWithServices.slice(0, 8)" 
            :key="category.id" 
            :to="`/category/${category.id}/services`" 
            class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow border border-blue-300 cursor-pointer flex flex-col no-underline"
            style="position: relative; z-index: 3;">
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ category.name }}</h3>
            <p class="text-sm text-gray-600 mb-2">{{ category.description || 'Professional laundry service' }}</p>
            <p class="text-sm text-spnr-blue-900 font-semibold mt-auto">{{ category.services_count }} services available</p>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" :class="isCompact ? 'contact-section py-12 px-4' : 'contact-section py-20 px-4'">
      <div class="max-w-7xl mx-auto">
        <div :class="isCompact ? 'text-center mb-8' : 'text-center mb-16'">
          <h2 :class="isCompact ? 'text-2xl font-bold text-gray-900 mb-2' : 'text-4xl font-bold text-gray-900 mb-4'">Get In Touch</h2>
          <p :class="isCompact ? 'text-sm text-gray-600' : 'text-lg text-gray-600'">Have questions? We're here to help!</p>
        </div>

        <!-- Compact View with Slider -->
        <div v-if="isCompact" class="relative flex items-center justify-center max-w-4xl mx-auto">
          <!-- Left Arrow -->
          <button @click="scrollContact('left')" class="absolute left-0 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <!-- Scrollable Container -->
          <div ref="contactContainer" class="flex gap-4 overflow-x-auto px-10 pb-4 snap-x snap-mandatory scroll-smooth hide-scrollbar">
            <div class="flex-shrink-0 w-40 snap-center text-center">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <h3 class="text-xs font-semibold text-gray-900 mb-1">Email</h3>
              <p class="text-xs text-gray-600">supportMinsu@spnrco.com</p>
            </div>

            <div class="flex-shrink-0 w-40 snap-center text-center ">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </div>
              <h3 class="text-xs font-semibold text-gray-900 mb-1">Phone</h3>
              <p class="text-xs text-gray-600">+639-50221-4827</p>
            </div>

            <div class="flex-shrink-0 w-40 snap-center text-center">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <h3 class="text-xs font-semibold text-gray-900 mb-1">Support Hours</h3>
              <p class="text-xs text-gray-600">24/7 Available</p>
            </div>
          </div>

          <!-- Right Arrow -->
          <button @click="scrollContact('right')" class="absolute right-0 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Desktop View -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
          <div class="text-center border border-blue-300 p-8 rounded-lg shadow-lg">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
            <p class="text-gray-600">supportMinsu@spnrco.com</p>
          </div>

          <div class="text-center border border-blue-300 p-8 rounded-lg shadow-lg">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Phone</h3>
            <p class="text-gray-600">+639-50221-4827</p>
          </div>

          <div class="text-center border border-blue-300 p-8 rounded-lg shadow-lg">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-spnr-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Support Hours</h3>
            <p class="text-gray-600">24/7 Available</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer :class="isCompact ? 'bg-gray-900 text-white py-8 px-4' : 'bg-gray-900 text-white py-12 px-4'">
      <div class="max-w-7xl mx-auto">
        <!-- Compact View with Slider -->
        <div v-if="isCompact" class="relative flex items-center justify-center mb-6">
          <!-- Left Arrow -->
          <button @click="scrollFooter('left')" class="absolute left-0 z-10 bg-gray-800 rounded-full p-2 shadow-lg hover:bg-gray-700">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <!-- Scrollable Container -->
          <div ref="footerContainer" class="flex gap-4 overflow-x-auto px-10 pb-4 snap-x snap-mandatory scroll-smooth hide-scrollbar">
            <div class="flex-shrink-0 w-48 snap-center">
              <div class="flex items-center mb-2">
                <img src="/logo.png" alt="SpnrCo Logo" class="h-6 w-6 mr-2" />
                <h3 class="text-base font-bold">SpnrCo</h3>
              </div>
              <p class="text-gray-400 text-xs">Professional Laundry Booking & Delivery System</p>
            </div>

            <div class="flex-shrink-0 w-40 snap-center">
              <h4 class="font-semibold mb-2 text-sm">Quick Links</h4>
              <ul class="space-y-1 text-xs text-gray-400">
                <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
                <li><a href="#shops" class="hover:text-white transition-colors">Shops</a></li>
                <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
              </ul>
            </div>

            <div class="flex-shrink-0 w-40 snap-center">
              <h4 class="font-semibold mb-2 text-sm">Services</h4>
              <ul class="space-y-1 text-xs text-gray-400">
                <li>Wash & Fold</li>
                <li>Dry Cleaning</li>
                <li>Express Service</li>
                <li>Pickup & Delivery</li>
              </ul>
            </div>

            <div class="flex-shrink-0 w-40 snap-center">
              <h4 class="font-semibold mb-2 text-sm">Connect</h4>
              <div class="flex space-x-2">
                <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-spnr-blue-900 transition-colors">
                  <span class="text-xs font-bold">fb</span>
                </a>
                <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-spnr-blue-900 transition-colors">
                  <span class="text-xs font-bold">tw</span>
                </a>
                <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-spnr-blue-900 transition-colors">
                  <span class="text-xs font-bold">in</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Right Arrow -->
          <button @click="scrollFooter('right')" class="absolute right-0 z-10 bg-gray-800 rounded-full p-2 shadow-lg hover:bg-gray-700">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Desktop View -->
        <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div>
            <div class="flex items-center mb-4">
              <img src="/logo.png" alt="SpnrCo Logo" class="h-10 w-10 mr-3" />
              <h3 class="text-2xl font-bold">SpnrCo</h3>
            </div>
            <p class="text-gray-400 text-sm">Professional Laundry Booking & Delivery App</p>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
              <li><a href="#shops" class="hover:text-white transition-colors">Shops</a></li>
              <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
              <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Services</h4>
            <ul class="space-y-2 text-sm text-gray-400">
              <li>Wash & Fold</li>
              <li>Dry Cleaning</li>
              <li>Express Service</li>
              <li>Pickup & Delivery</li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Connect</h4>
            <div class="flex space-x-4">
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-spnr-blue-900 transition-colors">
                <span class="text-sm font-bold">fb</span>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-spnr-blue-900 transition-colors">
                <span class="text-sm font-bold">tw</span>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-spnr-blue-900 transition-colors">
                <span class="text-sm font-bold">in</span>
              </a>
            </div>
          </div>
        </div>

        <div :class="isCompact ? 'border-t border-gray-800 pt-4 text-center' : 'border-t border-gray-800 pt-8 text-center'">
          <p :class="isCompact ? 'text-xs text-gray-400' : 'text-sm text-gray-400'">&copy; 2025 SpnrCo. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()
const loadingShops = ref(true)
const shops = ref([])
const categories = ref([])
const loadingCategories = ref(true)
const mobileMenuOpen = ref(false)
const statsContainer = ref(null)
const featuresContainer = ref(null)
const shopsContainer = ref(null)
const servicesContainer = ref(null)
const contactContainer = ref(null)
const footerContainer = ref(null)
const stats = ref({
  shops: 0,
  services: 0,
  orders: 0
})

// auto-compact flag for widths < 1024px
const isCompact = ref(typeof window !== 'undefined' ? window.innerWidth < 1024 : false)
const updateCompact = () => {
  if (typeof window !== 'undefined') isCompact.value = window.innerWidth < 1024
}

// Filter categories that have services
const categoriesWithServices = computed(() => {
  return categories.value.filter(cat => cat.services_count > 0)
})

const viewCategoryServices = (categoryId) => {
  console.log('Clicking category:', categoryId)
  router.push(`/category/${categoryId}/services`)
}

const scrollStats = (direction) => {
  if (!statsContainer.value) return
  const scrollAmount = 150
  const currentScroll = statsContainer.value.scrollLeft
  statsContainer.value.scrollLeft = direction === 'left' 
    ? currentScroll - scrollAmount 
    : currentScroll + scrollAmount
}

const scrollFeatures = (direction) => {
  if (!featuresContainer.value) return
  const scrollAmount = 240
  const currentScroll = featuresContainer.value.scrollLeft
  featuresContainer.value.scrollLeft = direction === 'left' 
    ? currentScroll - scrollAmount 
    : currentScroll + scrollAmount
}

const scrollShops = (direction) => {
  if (!shopsContainer.value) return
  const scrollAmount = 280
  const currentScroll = shopsContainer.value.scrollLeft
  shopsContainer.value.scrollLeft = direction === 'left' 
    ? currentScroll - scrollAmount 
    : currentScroll + scrollAmount
}

const scrollServices = (direction) => {
  if (!servicesContainer.value) return
  const scrollAmount = 150
  const currentScroll = servicesContainer.value.scrollLeft
  servicesContainer.value.scrollLeft = direction === 'left' 
    ? currentScroll - scrollAmount 
    : currentScroll + scrollAmount
}

const scrollContact = (direction) => {
  if (!contactContainer.value) return
  const scrollAmount = 180
  const currentScroll = contactContainer.value.scrollLeft
  contactContainer.value.scrollLeft = direction === 'left' 
    ? currentScroll - scrollAmount 
    : currentScroll + scrollAmount
}

const scrollFooter = (direction) => {
  if (!footerContainer.value) return
  const scrollAmount = 180
  const currentScroll = footerContainer.value.scrollLeft
  footerContainer.value.scrollLeft = direction === 'left' 
    ? currentScroll - scrollAmount 
    : currentScroll + scrollAmount
}

const fetchPublicShops = async () => {
  try {
    const response = await api.get('/shops')
    // Support both raw arrays and Laravel-style wrappers: { data: [...] }
    let payload = response.data
    if (payload && payload.data) payload = payload.data
    const shopsArray = Array.isArray(payload) ? payload : []
    console.log('fetchPublicShops payload length:', shopsArray.length)
    shops.value = shopsArray.slice(0, 6)
    stats.value.shops = shopsArray.length
    stats.value.services = shopsArray.reduce((total, shop) => total + (shop.services_count || 0), 0)
    stats.value.orders = shopsArray.length * 15
  } catch (error) {
    console.error('Error fetching shops:', error)
  } finally {
    loadingShops.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    let payload = response.data
    if (payload && payload.data) payload = payload.data
    categories.value = Array.isArray(payload) ? payload : []
  } catch (error) {
    console.error('Error fetching categories:', error)
  } finally {
    loadingCategories.value = false
  }
}

onMounted(() => {
  fetchPublicShops()
  fetchCategories()
  updateCompact()
  if (typeof window !== 'undefined') window.addEventListener('resize', updateCompact)
})

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') window.removeEventListener('resize', updateCompact)
})
</script>

<style scoped>
/* Smooth fade + slight lift transition for hero branch switching */
.hero-fade-enter-active, .hero-fade-leave-active {
  transition: opacity 280ms cubic-bezier(.2,.9,.3,1), transform 280ms cubic-bezier(.2,.9,.3,1);
}
.hero-fade-enter-from, .hero-fade-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(.99);
}
.hero-fade-enter-to, .hero-fade-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* Keep blob animations visible in compact but slightly reduced */
.hero-image .animate-blob { animation: blob 8s infinite; }
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(8px, -6px) scale(1.05); }
  66% { transform: translate(-6px, 8px) scale(0.95); }
  100% { transform: translate(0px, 0px) scale(1); }
}

/* Hide scrollbar for stats slider */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Smooth horizontal scroll for feature cards (mobile/tablet) */
.snap-x { scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
.snap-center { scroll-snap-align: center; }
.overflow-x-auto::-webkit-scrollbar { height: 6px; }
.overflow-x-auto::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 3px; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 3px; }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
</style>
