<?php
// =========================================================
// BACKEND-CONNECTED OFFERS SECTION (GLOW FIXED)
// =========================================================

$activeOffers = getActiveOffers($pdo); 
?>

<?php if (!empty($activeOffers)): ?>

<style>
    /* =========================================================
       OFFERS SECTION - EXACT MATCH TO YOUR FRONTEND DESIGN
    ========================================================= */
    .offers-section {
        position: relative;
        z-index: 10;
        width: 100%;
        padding: 40px 0 60px;
        background: transparent;
    }

    .offers-inner {
        position: relative;
        z-index: 5;
        width: min(100%, 1100px);
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }

    .offers-header {
        margin-bottom: 10px;
    }

    .offers-badge {
        display: inline-flex;
        align-items: center;
        gap: 11px;
        padding: 7px 17px;
        border: 1px dashed rgba(23, 23, 23, .25);
        border-radius: 999px;
        color: #514b47;
        font-size: 14px;
        background: rgba(255, 255, 255, .4);
        font-weight: 500;
    }

    .offers-badge span {
        color: #111;
        font-size: 15px;
    }

    .offers-heading {
        font-family: 'DM Sans', Arial, sans-serif;
        margin-top: 22px;
        font-size: clamp(2.4rem, 5vw, 3.6rem);
        line-height: .98;
        letter-spacing: -2.6px;
        font-weight: 600;
        color: #171715;
        margin-bottom: 14px;
    }

    .offers-heading em {
        font-family: "Playfair Display", Georgia, serif;
        font-style: italic;
        font-weight: 500;
        letter-spacing: -1.6px;
    }

    .offers-sub {
        font-size: 14.5px;
        line-height: 1.55;
        color: #8a7d74;
        max-width: 480px;
        margin: 0 auto;
        font-weight: 400;
    }

    /* ---- Curved Route with Dynamic Dots ---- */
    .route {
        position: relative;
        width: min(820px, 75vw);
        height: 115px;
        margin: 30px auto -6px;
    }

    .route svg {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        overflow: visible;
    }

    .route path {
        fill: none;
        stroke: rgba(80, 70, 65, .35);
        stroke-width: 1.3;
        stroke-dasharray: 3 5;
        stroke-linecap: round;
    }

    .route-dot {
        position: absolute;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        background: #9c6871;
        color: #72515a;
        cursor: pointer;
        opacity: .70;
        transform: translate(-50%, 0);
        transition: transform .35s ease, box-shadow .35s ease, background .35s ease, opacity .35s ease;
        z-index: 5;
        box-shadow: 0 0 0 6px rgba(255, 255, 255, .06);
    }

    .route-dot.active {
        opacity: 1;
        background: var(--active-color, #ff4d6d);
        color: white;
        transform: translate(-50%, 0) scale(1.2);
        box-shadow:
            0 0 0 5px color-mix(in srgb, var(--active-color, #ff4d6d) 18%, transparent),
            0 0 18px var(--active-color, #ff4d6d),
            0 0 40px color-mix(in srgb, var(--active-color, #ff4d6d) 50%, transparent);
        animation: activeGlow 1.8s ease-in-out infinite;
    }

    @keyframes activeGlow {
        0%, 100% {
            box-shadow:
                0 0 0 5px color-mix(in srgb, var(--active-color, #ff4d6d) 15%, transparent),
                0 0 15px var(--active-color, #ff4d6d);
        }
        50% {
            box-shadow:
                0 0 0 9px color-mix(in srgb, var(--active-color, #ff4d6d) 8%, transparent),
                0 0 32px var(--active-color, #ff4d6d),
                0 0 50px color-mix(in srgb, var(--active-color, #ff4d6d) 40%, transparent);
        }
    }

    /* ---- Carousel ---- */
    .offers-carousel {
        position: relative;
        width: 100%;
        max-width: 900px;
        margin: 30px auto 0;
        padding: 0 70px;
        z-index: 5;
    }

    .offer-slide {
        display: none;
        width: 100%;
    }

    .offer-slide.active {
        display: block;
    }

    /* ---- Fade Transition ---- */
    .offer-feature-card.content-transition .offer-feature-image img {
        opacity: 0;
        transform: scale(.96);
    }

    .offer-feature-card.content-transition .offer-feature-top,
    .offer-feature-card.content-transition .offer-feature-title,
    .offer-feature-card.content-transition .offer-feature-desc,
    .offer-feature-card.content-transition .offer-feature-meta,
    .offer-feature-card.content-transition .offer-feature-btn {
        opacity: 0;
        transform: translateY(8px);
    }

    /* ---- Main Card ---- */
    .offer-feature-card {
        width: 100%;
        display: grid;
        grid-template-columns: 300px 1fr;
        background: #fff;
        border-radius: 27px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(78, 50, 38, .08);
        position: relative;
    }

    .offer-feature-image {
        width: 300px;
        height: 313px;
        position: relative;
        overflow: hidden;
        background: #f0ebe6;
    }

    .offer-feature-image img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
        transition: transform .7s cubic-bezier(.25, .46, .45, .94), opacity .5s ease;
        will-change: transform, opacity;
    }

    .offer-feature-card:hover .offer-feature-image img {
        transform: scale(1.06);
    }

    .offer-feature-discount {
        position: absolute;
        top: 18px;
        left: 18px;
        padding: 7px 14px;
        background: #1a1a17;
        color: #fff;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(0, 0, 0, .15);
    }

    .offer-feature-content {
        padding: 30px 29px 27px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .offer-feature-top,
    .offer-feature-title,
    .offer-feature-desc,
    .offer-feature-meta,
    .offer-feature-btn {
        transition: opacity .4s ease, transform .4s ease;
    }

    .offer-feature-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }

    .offer-feature-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .10em;
        color: #9b8b80;
        text-transform: uppercase;
    }

    .offer-feature-code {
        padding: 5px 10px;
        border-radius: 50px;
        background: #f6f6f4;
        color: #777;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .08em;
        white-space: nowrap;
    }

    .offer-feature-title {
        margin: 0 0 9px;
        color: #171715;
        font-size: 29px;
        font-weight: 700;
        line-height: 1.08;
        letter-spacing: -1.4px;
    }

    .offer-feature-desc {
        margin: 0 0 19px;
        color: #55504d;
        font-size: 16px;
        line-height: 1.25;
        font-weight: 400;
    }

    .offer-feature-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 18px;
        margin-bottom: 22px;
        color: #444;
        font-size: 12px;
        font-weight: 500;
    }

    .offer-feature-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .offer-feature-btn {
        width: 210px;
        height: 48px;
        border: 0;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 5px 6px 5px 28px;
        background: #171717;
        color: #fff;
        border-radius: 999px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
        margin-top: 6px;
    }

    .offer-feature-btn:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
    }

    .offer-feature-btn-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #fff;
        color: #171717;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* ---- Arrow Buttons ---- */
    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 1px dashed rgba(23, 23, 23, .45);
        background: rgba(255, 255, 255, .55);
        color: #171717;
        font-size: 30px;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s ease, transform .2s ease;
        z-index: 10;
    }

    .slider-btn:hover {
        background: rgba(255, 255, 255, .9);
        transform: translateY(-50%) scale(1.05);
    }

    .offer-carousel-prev { left: 0; }
    .offer-carousel-next { right: 0; }

    /* ---- Footer ---- */
    .offers-footer {
        position: relative;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-top: 45px;
        flex-wrap: wrap;
    }

    .offers-view-all {
        height: 59px;
        padding: 5px 24px 5px 8px;
        border: 0;
        border-radius: 999px;
        background: #fff;
        display: inline-flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 5px 18px rgba(70, 40, 30, .07);
        color: #171717;
    }

    .offers-view-all-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        background: #fedbe5;
        color: #1a1a17;
        flex-shrink: 0;
    }

    .offers-view-all-text {
        margin-left: 4px;
        font-size: 14px;
        font-weight: 600;
    }

    .offers-guarantee {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 17px;
        border: 1px dashed rgba(23, 23, 23, .25);
        border-radius: 999px;
        background: rgba(255, 255, 255, .4);
        font-size: 11px;
        font-weight: 600;
        color: #514b47;
    }

    /* ---- Mobile Responsiveness ---- */
    @media (max-width: 767px) {
        .offers-carousel { padding: 0 42px; }
        .offer-feature-card { grid-template-columns: 1fr; border-radius: 22px; }
        .offer-feature-image { width: 100%; height: 220px; }
        .offer-feature-content { padding: 24px; }
        .offer-feature-title { font-size: 24px; }
        .slider-btn { width: 38px; height: 38px; font-size: 25px; }
        .offer-carousel-prev { left: 2px; }
        .offer-carousel-next { right: 2px; }
    }
</style>

<section class="offers-section">
    <div class="offers-inner">

        <!-- HEADER -->
        <div class="offers-header">
            <span class="offers-badge">
                <span>✦</span>
                <span>Limited Time</span>
                <span>✦</span>
            </span>
            <h2 class="offers-heading">Exclusive <em>Offers</em></h2>
            <p class="offers-sub">Grab these handpicked deals before they expire — available for a limited time only.</p>

            <!-- DYNAMIC CURVED LINE & DOTS -->
            <?php if (count($activeOffers) > 1): ?>
                <div class="route">
                    <svg viewBox="0 0 800 115" preserveAspectRatio="none">
                        <path d="M 10 25 C 150 25, 175 100, 400 100 C 625 100, 650 25, 790 25" />
                    </svg>
                    
                    <?php
                        $dotCount = count($activeOffers);
                        $glowColors = ['#ff4d6d', '#ff8c42', '#00bfff', '#8b5cf6', '#00d68f', '#ff4fd8', '#ffd166', '#00c2a8', '#a855f7', '#ff6b35'];
                        
                        foreach ($activeOffers as $dotIndex => $dotOffer):
                            $frac = $dotCount > 1 ? $dotIndex / ($dotCount - 1) : 0.5;
                            $dotLeft = 1 + (98 * $frac);
                            $dotTop = 16 + (68 * sin(M_PI * $frac));
                            
                            // ✅ FIX: Assign a valid hex color and ensure it's properly set
                            $isActive = $dotIndex === 0;
                            $randomIndex = array_rand($glowColors);
                            $activeColor = $isActive ? $glowColors[$randomIndex] : '';
                    ?>
                        <div
                            class="route-dot <?= $isActive ? 'active' : ''; ?>"
                            data-index="<?= (int) $dotIndex; ?>"
                            style="left:<?= round($dotLeft, 2); ?>%; top:<?= round($dotTop, 1); ?>px; <?= $isActive ? '--active-color:' . $activeColor . '; background:' . $activeColor . ';' : ''; ?>">
                            ✦
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- DYNAMIC CAROUSEL -->
        <div class="offers-carousel">

            <?php if (count($activeOffers) > 1): ?>
                <button type="button" class="slider-btn offer-carousel-prev" onclick="changeOffer(-1)" aria-label="Previous offer">‹</button>
            <?php endif; ?>

            <?php foreach ($activeOffers as $index => $offer): ?>
                <?php
                $offerImg = getOfferMainImage($pdo, $offer['main_image']);
                $discountText = getOfferDiscountText($offer);
                $tourNames = getOfferTourNames($pdo, $offer['tour_packages']);
                ?>

                <div class="offer-slide <?= $index === 0 ? 'active' : ''; ?>">
                    <div class="offer-feature-card">

                        <!-- IMAGE -->
                        <div class="offer-feature-image">
                            <img src="<?= htmlspecialchars($offerImg); ?>" alt="<?= htmlspecialchars($offer['title']); ?>" loading="lazy">
                            <?php if (!empty($discountText)): ?>
                                <span class="offer-feature-discount"><?= htmlspecialchars($discountText); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- CONTENT -->
                        <div class="offer-feature-content">
                            <div class="offer-feature-top">
                                <span class="offer-feature-label">LIMITED TIME OFFER</span>
                                
                            </div>

                            <h3 class="offer-feature-title"><?= htmlspecialchars($offer['title']); ?></h3>
                            <?php if (!empty($offer['description'])): ?>
                                <p class="offer-feature-desc"><?= htmlspecialchars($offer['description']); ?></p>
                            <?php endif; ?>

                            <div class="offer-feature-meta">
                                <span>
                                    <?= date('M d', strtotime($offer['start_date'])); ?> – <?= date('M d, Y', strtotime($offer['end_date'])); ?>
                                </span>
                                <?php if (!empty($tourNames)): ?>
                                    <span><?= htmlspecialchars($tourNames[0]); ?></span>
                                <?php endif; ?>
                            </div>

                            <a href="<?= SITE_URL; ?>tours.php?offer=<?= urlencode($offer['offer_code']); ?>" class="offer-feature-btn">
                                <span>Explore Offer</span>
                                <span class="offer-feature-btn-icon">↗</span>
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($activeOffers) > 1): ?>
                <button type="button" class="slider-btn offer-carousel-next" onclick="changeOffer(1)" aria-label="Next offer">›</button>
            <?php endif; ?>

        </div>

        <!-- FOOTER -->
        <div class="offers-footer">
            <a href="<?= SITE_URL; ?>tours.php" class="offers-view-all">
                <span class="offers-view-all-icon">↗</span>
                <span class="offers-view-all-text">View All Tours</span>
            </a>
           
        </div>

    </div>
</section>

<script>
    (function () {
        let currentOffer = 0;
        let isTransitioning = false;

        const slides = document.querySelectorAll('.offer-slide');
        const dots = document.querySelectorAll('.route-dot');
        const glowColors = ['#ff4d6d', '#ff8c42', '#00bfff', '#8b5cf6', '#00d68f', '#ff4fd8', '#ffd166', '#00c2a8', '#a855f7', '#ff6b35'];

        function randomColor() {
            return glowColors[Math.floor(Math.random() * glowColors.length)];
        }

        function updateActiveDot() {
            dots.forEach((dot, idx) => {
                dot.classList.remove('active');
                dot.style.removeProperty('--active-color');
                dot.style.removeProperty('background');
                
                if (idx === currentOffer) {
                    const col = randomColor();
                    // Set both the CSS variable AND the background directly for fallback
                    dot.style.setProperty('--active-color', col);
                    dot.style.background = col;
                    dot.classList.add('active');
                } else {
                    // Reset to default background for inactive dots
                    dot.style.background = '#9c6871';
                }
            });
        }

        function goToOffer(index) {
            if (isTransitioning || !slides.length || index === currentOffer) return;
            isTransitioning = true;

            const outgoing = slides[currentOffer];
            const outgoingCard = outgoing.querySelector('.offer-feature-card');

            if (outgoingCard) outgoingCard.classList.add('content-transition');

            // Update active dot immediately
            currentOffer = index;
            updateActiveDot();

            setTimeout(() => {
                outgoing.classList.remove('active');
                if (outgoingCard) outgoingCard.classList.remove('content-transition');

                const incoming = slides[currentOffer];
                incoming.classList.add('active');

                isTransitioning = false;
            }, 380); // matches CSS transition duration
        }

        window.changeOffer = function (direction) {
            if (!slides.length) return;
            let next = currentOffer + direction;
            if (next >= slides.length) next = 0;
            if (next < 0) next = slides.length - 1;
            goToOffer(next);
        };

        // Dot click
        dots.forEach((dot) => {
            dot.addEventListener('click', function () {
                const idx = parseInt(dot.getAttribute('data-index'), 10);
                if (!isNaN(idx)) goToOffer(idx);
            });
        });

        // Init
        updateActiveDot();
    })();
</script>

<?php endif; ?>