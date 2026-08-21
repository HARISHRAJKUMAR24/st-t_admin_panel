<style>
/* =========================================================
   FIX: Search dropdown hidden by section below hero
========================================================= */
.hero {
  position: relative;
  z-index: 20;
  overflow: visible !important;
}

.hero-main,
.hero-content {
  overflow: visible !important;
}

/* Force every section that comes AFTER the hero to stay behind it */
.hero ~ section {
  position: relative;
  z-index: 1;
}

</style>
<section class="hero" id="hero">
  <div class="hero-gradient"></div>
  <div class="bottom-clouds" id="bottomClouds">
    <img src="<?= SITE_URL; ?>assets/images/hero_image_cloud.png" alt="">
  </div>

  <div class="hero-main">
    <div class="hero-content" id="heroContent">
      <div class="hero-badge">
        <span class="badge-icon"><svg viewBox="0 0 16 16">
            <circle cx="8" cy="8" r="6" fill="#fff6c7" />
          </svg></span>
        <span class="badge-text">Crafted Journeys Since 2009</span>
        <span class="badge-icon"><svg viewBox="0 0 16 16">
            <circle cx="8" cy="8" r="6" fill="#fff6c7" />
          </svg></span>
      </div>
      <div class="hero-title">
        <span>Travel</span><span>Beyond</span><span>the</span><span class="italic">Ordinary</span>
      </div>
      <p class="hero-sub">Handpicked destinations, curated itineraries, and local expertise so every
        journey feels like it was made just for you.</p>
      
      <!-- Search Bar Container -->
      <div class="search-container" id="searchContainer">
        <div class="search-bar">
          <i class="bi bi-search search-icon"></i>
          <input 
            type="text" 
            placeholder="Search destinations, tours…" 
            id="searchInput" 
            autocomplete="off"
            spellcheck="false">
          <button class="search-clear-btn" id="searchClearBtn" style="display: none;" aria-label="Clear search">
            <i class="bi bi-x-lg"></i>
          </button>
          <button class="search-btn" aria-label="Search" id="searchBtn">
            <i class="bi bi-arrow-right"></i>
          </button>
        </div>
        
        <!-- Search Dropdown -->
        <div class="search-dropdown" id="searchDropdown">
          <div class="search-dropdown-header">
            <span class="search-dropdown-title">Search Results</span>
            <span class="search-dropdown-count" id="searchCount"></span>
          </div>
          <div class="search-dropdown-results" id="searchResults">
            <!-- Results will be injected here -->
          </div>
          <div class="search-dropdown-empty" id="searchEmpty" style="display: none;">
            <div class="search-empty-icon">
              <i class="bi bi-search"></i>
            </div>
            <p>No tours found for "<span id="searchEmptyQuery"></span>"</p>
            <span>Try searching for a destination or tour type</span>
          </div>
          <div class="search-dropdown-loading" id="searchLoading" style="display: none;">
            <div class="search-spinner"></div>
            <span>Searching tours...</span>
          </div>
          <div class="search-dropdown-footer">
            <a href="<?= SITE_URL; ?>tours.php" class="search-view-all">
              <span>View All Tours</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      
    </div>
  </div>

</section>