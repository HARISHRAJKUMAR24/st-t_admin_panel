<?php
// Fetch featured tour packages
// Get 4 tours from database
$featuredTours = getTourPackages($pdo, 4);
?>
<style>
/* =========================
   FEATURED TOUR CAROUSEL
========================= */

.featured-tour-slider-wrapper {
    position: relative;
    width: 100%;
    padding: 0;
    overflow: visible;
}


/* Slider */
.featured-tour-slider {
    display: flex;
    gap: 24px;
    width: 100%;

    overflow-x: auto;
    overflow-y: hidden;

    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;

    scrollbar-width: none;
}

.featured-tour-slider::-webkit-scrollbar {
    display: none;
}


/* Tour cards */
.featured-tour-slide {
    flex: 0 0 calc((100% - 48px) / 3);
    min-width: 0;

    scroll-snap-align: start;
}


/* =========================
   ARROWS
========================= */

.featured-tour-arrow {
    position: absolute;

    top: 50%;
    transform: translateY(-50%);

    width: 54px;
    height: 54px;

    border-radius: 50%;
    border: 1px solid #e5e5e5;

    background: #fff;
    color: #222;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 18px;

    cursor: pointer;

    z-index: 30;

    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);

    transition:
        background 0.25s ease,
        color 0.25s ease,
        transform 0.25s ease,
        box-shadow 0.25s ease;
}


/* Left arrow */
.featured-tour-prev {
    left: 0;
    transform: translate(-50%, -50%);
}


/* Right arrow */
.featured-tour-next {
    right: 0;
    transform: translate(50%, -50%);
}


/* Hover */
.featured-tour-arrow:hover {
    background: #111;
    color: #fff;
    border-color: #111;

    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
}


/* =========================
   TABLET
========================= */

@media (max-width: 991px) {

    .featured-tour-slide {
        flex: 0 0 calc((100% - 24px) / 2);
    }

    .featured-tour-arrow {
        width: 48px;
        height: 48px;
    }

}


/* =========================
   MOBILE
========================= */

@media (max-width: 576px) {

    .featured-tour-slide {
        flex: 0 0 100%;
    }

    .featured-tour-arrow {
        width: 44px;
        height: 44px;
    }

}
</style>
<section class="featured-tours-section" id="featuredTours" style="padding-top:5px">
  <div class="featured-container">

    <div class="row align-items-end g-4 mb-5">
      <div class="col-lg-7">
        <div class="featured-badge">
          <span class="star">✤</span>
          <span>Featured Tours</span>
          <span class="star">✤</span>
        </div>

        <h2 class="featured-title">
          Tours <span class="italic">Crafted</span> for
          Every Traveller
        </h2>
      </div>

      <div class="col-lg-5">
        <div class="featured-intro">
          <p>
            Each itinerary blends hidden gems with landmark
            experiences, guided by people who live and breathe
            these places.
          </p>

          <a href="<?= SITE_URL; ?>tours.php" class="featured-view-btn">
            <span>View All Tours</span>
            <span class="arrow">
              <i class="bi bi-arrow-up-right"></i>
            </span>
          </a>
        </div>
      </div>
    </div>


    <!-- Tour Carousel -->
    <div class="featured-tour-slider-wrapper">

      <!-- Left Arrow -->
      <button type="button"
        class="featured-tour-arrow featured-tour-prev"
        aria-label="Previous tours">
        <i class="bi bi-arrow-left"></i>
      </button>


      <div class="featured-tour-slider">

        <?php if (!empty($featuredTours)): ?>

          <?php foreach ($featuredTours as $tour): ?>

            <?php
            // Main image
            if (!empty($tour['main_image'])) {

              if (strpos($tour['main_image'], 'http') === 0) {
                $mainImage = $tour['main_image'];
              } else {
                $mainImage = ADMIN_URL . $tour['main_image'];
              }
            } else {
              $mainImage = SITE_URL . 'assets/images/default-tour.jpg';
            }

            // Tour ID
            $tourId = $tour['id']
              ?? $tour['package_id']
              ?? '';

            // Tour link
            $tourLink = !empty($tourId)
              ? SITE_URL . 'tour.php?id=' . $tourId
              : '#';
            ?>

            <div class="featured-tour-slide">

              <a href="<?= htmlspecialchars($tourLink); ?>"
                class="featured-tour-card d-block text-decoration-none">

                <div class="tour-image">

                  <img
                    src="<?= htmlspecialchars($mainImage); ?>"
                    alt="<?= htmlspecialchars($tour['package_name']); ?>"
                    loading="lazy">

                  <span class="tour-category">
                    <?= htmlspecialchars($tour['package_type'] ?? 'Tour'); ?>
                  </span>

                </div>


                <div class="tour-body">

                  <h3 class="tour-title">
                    <?= htmlspecialchars($tour['package_name']); ?>
                  </h3>

                  <p class="tour-description">
                    <?= htmlspecialchars($tour['short_description'] ?? ''); ?>
                  </p>

                  <div class="tour-divider"></div>

                  <div class="tour-bottom">

                    <div class="tour-price">

                      <?php if (!empty($tour['price'])): ?>

                        <?= formatTourPrice($tour['price'], '₹'); ?>
                        <small>/person</small>

                      <?php else: ?>

                        Contact for Price

                      <?php endif; ?>

                    </div>

                    <span class="tour-duration">
                      <?= getTourDuration($tour['days_count'] ?? 0); ?>
                    </span>

                  </div>

                </div>

              </a>

            </div>

          <?php endforeach; ?>

        <?php else: ?>

          <div class="no-tours">
            No tour packages available.
          </div>

        <?php endif; ?>

      </div>


      <!-- Right Arrow -->
      <button type="button"
        class="featured-tour-arrow featured-tour-next"
        aria-label="Next tours">
        <i class="bi bi-arrow-right"></i>
      </button>

    </div>

  </div>
</section>
<script>
  document.addEventListener('DOMContentLoaded', function() {

    const slider = document.querySelector('.featured-tour-slider');
    const prevBtn = document.querySelector('.featured-tour-prev');
    const nextBtn = document.querySelector('.featured-tour-next');

    if (!slider || !prevBtn || !nextBtn) {
      return;
    }

    function getScrollAmount() {
      const slide = slider.querySelector('.featured-tour-slide');

      if (!slide) {
        return 0;
      }

      const gap = 24;

      return slide.offsetWidth + gap;
    }

    nextBtn.addEventListener('click', function() {

      slider.scrollBy({
        left: getScrollAmount(),
        behavior: 'smooth'
      });

    });

    prevBtn.addEventListener('click', function() {

      slider.scrollBy({
        left: -getScrollAmount(),
        behavior: 'smooth'
      });

    });

  });
</script>