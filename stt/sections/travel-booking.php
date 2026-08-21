<section class="taxi-booking-section">

    <!-- BACKGROUND DECOR -->
    <div class="taxi-bg-grid"></div>
    <div class="taxi-circle taxi-circle-1"></div>
    <div class="taxi-circle taxi-circle-2"></div>


    <!-- =========================================
       LEFT — TAXI IMAGE
  ========================================== -->

    <div class="taxi-visual">

        <div class="taxi-yellow-circle"></div>

        <div class="taxi-image">

            <img
                src="https://images.unsplash.com/photo-1517400508447-f8dd518b86db?auto=format&fit=crop&w=1400&q=90"
                alt="Taxi Travel">

        </div>


        <!-- QUICK PICKUP -->

        <div class="taxi-pickup">

            <span class="taxi-pickup-icon">🚕</span>

            <div>
                <strong>Quick Pickup</strong>
                <small>Usually within minutes</small>
            </div>

        </div>


        <!-- PRICE CARD -->

        <div class="taxi-price-card">

            <div class="taxi-price-top">
                <span>AVAILABLE NOW</span>
                <i></i>
            </div>

            <strong class="taxi-city-ride">
                City Ride
            </strong>

            <small class="taxi-comfort">
                Comfortable • Safe • Fast
            </small>

            <div class="taxi-price-line">
                <span>Starting from</span>
                <strong>₹199</strong>
            </div>

        </div>

    </div>



    <!-- =========================================
       RIGHT — CONTENT
  ========================================== -->

    <div class="taxi-content">

        <div class="taxi-eyebrow">
            <span>✦</span>
            YOUR RIDE, YOUR WAY
        </div>


        <h2 class="taxi-heading">

            Travel

            <em>Your Way</em>

        </h2>


        <p class="taxi-text">
            Comfortable vehicles, flexible routes and packages
            designed for your journey.
        </p>


        <!-- FEATURES -->

        <div class="taxi-features">

            <div class="taxi-feature">

                <span class="taxi-feature-number">01</span>

                <div>
                    <strong>Comfortable Vehicles</strong>
                    <small>Cars for every kind of journey.</small>
                </div>

            </div>


            <div class="taxi-feature">

                <span class="taxi-feature-number">02</span>

                <div>
                    <strong>Flexible Routes</strong>
                    <small>Travel wherever your plans take you.</small>
                </div>

            </div>


            <div class="taxi-feature">

                <span class="taxi-feature-number">03</span>

                <div>
                    <strong>Custom Packages</strong>
                    <small>Built around your schedule.</small>
                </div>

            </div>

        </div>


        <!-- ROUTE -->

        <div class="taxi-route">

            <div class="taxi-route-line">

                <span class="taxi-dot taxi-dot-start"></span>

                <div></div>

                <span class="taxi-dot taxi-dot-end"></span>

            </div>


            <div class="taxi-route-details">

                <div>
                    <small>FROM</small>
                    <strong>Your Location</strong>
                </div>

                <span class="taxi-arrow">→</span>

                <div class="taxi-to">
                    <small>TO</small>
                    <strong>Your Destination</strong>
                </div>

            </div>

        </div>


        <!-- BUTTON -->

        <a
            href="<?= SITE_URL; ?>travel-booking.php"
            class="taxi-book-btn">

            <span>Book Your Taxi</span>

            <b>↗</b>

        </a>


        <!-- MINI STATS -->

        <div class="taxi-stats">

            <div>
                <strong>24/7</strong>
                <small>Available</small>
            </div>

            <div>
                <strong>5★</strong>
                <small>Rated Drivers</small>
            </div>

            <div>
                <strong>10 min</strong>
                <small>Avg. Pickup</small>
            </div>

        </div>

    </div>


    <div class="taxi-bg-number">
        24 / 7
    </div>

</section>



<style>
    /* =========================================================
   RESET
========================================================= */

    .taxi-booking-section,
    .taxi-booking-section * {
        box-sizing: border-box;
    }


    /* =========================================================
   MAIN SECTION
========================================================= */

    .taxi-booking-section {

        position: relative;

        width: 100%;

        min-height: 820px;

        overflow: hidden;

        background: #fff8dc;

        display: grid;

        /*
    IMAGE LEFT
    CONTENT RIGHT
  */

        grid-template-columns: 56% 44%;

        align-items: center;

        isolation: isolate;
    }


    /* =========================================================
   BACKGROUND GRID
========================================================= */

    .taxi-bg-grid {

        position: absolute;

        inset: 0;

        z-index: -5;

        opacity: .16;

        background-image:
            linear-gradient(rgba(0, 0, 0, .035) 1px,
                transparent 1px),
            linear-gradient(90deg,
                rgba(0, 0, 0, .035) 1px,
                transparent 1px);

        background-size: 55px 55px;

    }


    /* =========================================================
   DECORATIVE CIRCLES
========================================================= */

    .taxi-circle {

        position: absolute;

        border-radius: 50%;

        border: 1px dashed rgba(0, 0, 0, .12);

        pointer-events: none;

    }


    .taxi-circle-1 {

        width: 620px;

        height: 620px;

        left: -170px;

        top: 90px;

    }


    .taxi-circle-2 {

        width: 420px;

        height: 420px;

        left: 40px;

        top: 190px;

    }


    /* =========================================================
   LEFT VISUAL
========================================================= */

    .taxi-visual {

        position: relative;

        width: 100%;

        height: 720px;

        grid-column: 1;

        grid-row: 1;
    }


    /* =========================================================
   YELLOW CIRCLE
========================================================= */

    .taxi-yellow-circle {

        position: absolute;

        width: 560px;

        height: 560px;

        left: 7%;

        top: 75px;

        background: #f0d98b;

        border-radius: 50%;

        opacity: .75;

    }


    /* =========================================================
   TAXI IMAGE
========================================================= */

    .taxi-image {

        position: absolute;

        z-index: 4;

        width: 650px;

        height: 450px;

        left: 7%;

        top: 125px;

        overflow: hidden;

        background: #ddd;

        border-radius:
            45px 45px 120px 45px;

        box-shadow:
            0 35px 70px rgba(40, 35, 20, .18);

        transition:
            transform .6s ease,
            box-shadow .6s ease;

    }


    .taxi-image:hover {

        transform:
            translateY(-8px);

        box-shadow:
            0 45px 80px rgba(40, 35, 20, .23);

    }


    .taxi-image img {

        width: 100%;

        height: 100%;

        display: block;

        object-fit: cover;

    }


    /* =========================================================
   QUICK PICKUP
========================================================= */

    .taxi-pickup {

        position: absolute;

        z-index: 10;

        left: 5%;

        top: 17%;

        display: flex;

        align-items: center;

        gap: 10px;

        padding:
            10px 16px;

        background: #fff;

        border-radius: 50px;

        box-shadow:
            0 15px 35px rgba(0, 0, 0, .10);

    }


    .taxi-pickup-icon {

        width: 34px;

        height: 34px;

        display: flex;

        align-items: center;

        justify-content: center;

        background: #fff0b8;

        border-radius: 50%;

        font-size: 16px;

    }


    .taxi-pickup div {

        display: flex;

        flex-direction: column;

        gap: 2px;

    }


    .taxi-pickup strong {

        font-family: Arial, sans-serif;

        font-size: 11px;

        color: #222;

    }


    .taxi-pickup small {

        font-family: Arial, sans-serif;

        font-size: 8px;

        color: #999;

    }


    /* =========================================================
   PRICE CARD
========================================================= */

    .taxi-price-card {

        position: absolute;

        z-index: 12;

        width: 245px;

        left: 48%;

        bottom: 70px;

        padding: 20px;

        background: #fff;

        border-radius: 24px;

        box-shadow:
            0 22px 50px rgba(0, 0, 0, .14);

    }


    .taxi-price-top {

        display: flex;

        align-items: center;

        justify-content: space-between;

        color: #888;

        font-family: Arial, sans-serif;

        font-size: 8px;

        letter-spacing: 1px;

    }


    .taxi-price-top i {

        width: 8px;

        height: 8px;

        display: block;

        background: #55b88a;

        border-radius: 50%;

        box-shadow:
            0 0 0 5px rgba(85, 184, 138, .13);

    }


    .taxi-city-ride {

        display: block;

        margin-top: 17px;

        color: #111;

        font-family: Arial, sans-serif;

        font-size: 22px;

    }


    .taxi-comfort {

        display: block;

        margin-top: 5px;

        color: #999;

        font-family: Arial, sans-serif;

        font-size: 10px;

    }


    .taxi-price-line {

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-top: 20px;

        padding-top: 13px;

        border-top: 1px solid #eee;

    }


    .taxi-price-line span {

        color: #999;

        font-family: Arial, sans-serif;

        font-size: 9px;

    }


    .taxi-price-line strong {

        color: #111;

        font-family: Arial, sans-serif;

        font-size: 18px;

    }


    /* =========================================================
   RIGHT CONTENT
========================================================= */

    .taxi-content {

        position: relative;

        z-index: 20;

        width: 100%;

        max-width: 650px;

        padding:
            30px 9vw 30px 25px;

        grid-column: 2;

        grid-row: 1;

    }


    /* =========================================================
   LABEL
========================================================= */

    .taxi-eyebrow {

        display: inline-flex;

        align-items: center;

        gap: 9px;

        padding:
            9px 17px;

        margin-bottom: 25px;

        border:
            1px dashed #bbb4a1;

        border-radius: 50px;

        color: #777268;

        font-family: Arial, sans-serif;

        font-size: 11px;

        letter-spacing: 1.4px;

    }


    .taxi-eyebrow span {

        color: #e7a000;

    }


    /* =========================================================
   TITLE
========================================================= */

    .taxi-heading {

        margin: 0;

        color: #111;

        font-family:
            Arial,
            Helvetica,
            sans-serif;

        font-size:
            clamp(65px, 7vw, 105px);

        line-height: .84;

        letter-spacing: -7px;

        font-weight: 800;

    }


    .taxi-heading em {

        display: block;

        font-family:
            Georgia,
            "Times New Roman",
            serif;

        font-weight: 500;

        font-style: italic;

        letter-spacing: -6px;

    }


    /* =========================================================
   DESCRIPTION
========================================================= */

    .taxi-text {

        max-width: 420px;

        margin:
            30px 0 25px;

        color: #6f6b62;

        font-family: Arial, sans-serif;

        font-size: 16px;

        line-height: 1.5;

    }


    /* =========================================================
   FEATURES
========================================================= */

    .taxi-features {

        display: flex;

        flex-direction: column;

        gap: 15px;

    }


    .taxi-feature {

        display: flex;

        align-items: center;

        gap: 15px;

    }


    .taxi-feature-number {

        width: 40px;

        height: 40px;

        flex: 0 0 40px;

        display: flex;

        align-items: center;

        justify-content: center;

        border:
            1px solid #d7d1c2;

        border-radius: 50%;

        color: #888;

        font-family: Arial, sans-serif;

        font-size: 10px;

    }


    .taxi-feature div {

        display: flex;

        flex-direction: column;

        gap: 3px;

    }


    .taxi-feature strong {

        color: #111;

        font-family: Arial, sans-serif;

        font-size: 14px;

    }


    .taxi-feature small {

        color: #999;

        font-family: Arial, sans-serif;

        font-size: 10px;

    }


    /* =========================================================
   ROUTE
========================================================= */

    .taxi-route {

        max-width: 500px;

        margin-top: 25px;

        padding:
            17px 19px;

        background:
            rgba(255, 255, 255, .7);

        border:
            1px solid rgba(0, 0, 0, .08);

        border-radius: 20px;

    }


    .taxi-route-line {

        display: flex;

        align-items: center;

        width: 100%;

    }


    .taxi-dot {

        width: 10px;

        height: 10px;

        flex: 0 0 10px;

        border-radius: 50%;

    }


    .taxi-dot-start {

        background: #111;

        box-shadow:
            0 0 0 5px rgba(0, 0, 0, .06);

    }


    .taxi-dot-end {

        background: #e9a400;

        box-shadow:
            0 0 0 5px rgba(233, 164, 0, .12);

    }


    .taxi-route-line div {

        flex: 1;

        height: 1px;

        margin:
            0 10px;

        border-top:
            1px dashed #aaa;

    }


    .taxi-route-details {

        display: grid;

        grid-template-columns:
            1fr 30px 1fr;

        align-items: center;

        gap: 8px;

        margin-top: 11px;

    }


    .taxi-route-details div {

        display: flex;

        flex-direction: column;

        gap: 3px;

    }


    .taxi-route-details small {

        color: #999;

        font-family: Arial, sans-serif;

        font-size: 7px;

        letter-spacing: 1px;

    }


    .taxi-route-details strong {

        color: #222;

        font-family: Arial, sans-serif;

        font-size: 11px;

    }


    .taxi-arrow {

        text-align: center;

        color: #999;

        font-size: 17px;

    }


    .taxi-to {

        text-align: right;

    }


    /* =========================================================
   BOOK BUTTON
========================================================= */

    .taxi-book-btn {

        display: inline-flex;

        align-items: center;

        gap: 12px;

        margin-top: 23px;

        padding:
            6px 7px 6px 25px;

        background: #111;

        color: #fff;

        text-decoration: none;

        border-radius: 50px;

        font-family: Arial, sans-serif;

        font-size: 13px;

        font-weight: 600;

        transition:
            transform .3s ease,
            box-shadow .3s ease;

    }


    .taxi-book-btn b {

        width: 44px;

        height: 44px;

        display: flex;

        align-items: center;

        justify-content: center;

        background: #fff;

        color: #111;

        border-radius: 50%;

        font-size: 20px;

        font-weight: 400;

        transition:
            transform .3s ease;

    }


    .taxi-book-btn:hover {

        transform:
            translateY(-4px);

        box-shadow:
            0 15px 30px rgba(0, 0, 0, .18);

    }


    .taxi-book-btn:hover b {

        transform:
            rotate(45deg);

    }


    /* =========================================================
   STATS
========================================================= */

    .taxi-stats {

        display: flex;

        align-items: center;

        gap: 30px;

        margin-top: 22px;

    }


    .taxi-stats div {

        display: flex;

        flex-direction: column;

        gap: 3px;

    }


    .taxi-stats strong {

        color: #111;

        font-family: Arial, sans-serif;

        font-size: 13px;

    }


    .taxi-stats small {

        color: #999;

        font-family: Arial, sans-serif;

        font-size: 8px;

    }


    /* =========================================================
   BACKGROUND NUMBER
========================================================= */

    .taxi-bg-number {

        position: absolute;

        z-index: -1;

        left: 2%;

        bottom: 5px;

        color:
            rgba(0, 0, 0, .055);

        font-family: Arial, sans-serif;

        font-size: 100px;

        font-weight: 800;

        letter-spacing: -7px;

    }


    /* =========================================================
   TABLET
========================================================= */

    @media (max-width: 1100px) {

        .taxi-booking-section {

            grid-template-columns:
                54% 46%;
        }


        .taxi-content {

            padding:
                25px 4vw 25px 15px;
        }


        .taxi-heading {

            font-size: 72px;

            letter-spacing: -5px;
        }


        .taxi-image {

            width: 500px;

            height: 360px;

            left: 2%;
        }


        .taxi-yellow-circle {

            width: 450px;

            height: 450px;

            left: 0;
        }


        .taxi-price-card {

            left: 40%;

            width: 220px;
        }

    }


    /* =========================================================
   MOBILE
========================================================= */

    @media (max-width: 700px) {

        .taxi-booking-section {

            display: flex;

            flex-direction: column;

            width: 100%;

            min-height: auto;

            overflow: hidden;
        }


        /* CONTENT FIRST */

        .taxi-content {

            order: 1;

            width: 100%;

            max-width: none;

            padding:
                40px 16px 0;
        }


        .taxi-eyebrow {

            margin-bottom: 17px;

            padding:
                7px 12px;

            font-size: 8px;
        }


        .taxi-heading {

            font-size:
                clamp(50px, 15vw, 72px);

            line-height: .86;

            letter-spacing: -4px;
        }


        .taxi-heading em {

            letter-spacing: -3px;
        }


        .taxi-text {

            max-width: 310px;

            margin:
                20px 0;

            font-size: 13px;

            line-height: 1.45;
        }


        /* FEATURES */

        .taxi-features {

            gap: 11px;
        }


        .taxi-feature {

            gap: 10px;
        }


        .taxi-feature-number {

            width: 34px;

            height: 34px;

            flex-basis: 34px;

            font-size: 8px;
        }


        .taxi-feature strong {

            font-size: 10px;
        }


        .taxi-feature small {

            font-size: 7px;
        }


        /* ROUTE */

        .taxi-route {

            width: 100%;

            margin-top: 18px;

            padding: 13px;

            border-radius: 16px;
        }


        .taxi-route-details {

            grid-template-columns:
                minmax(0, 1fr) 25px minmax(0, 1fr);

            gap: 4px;
        }


        .taxi-route-details strong {

            font-size: 8px;

            white-space: nowrap;
        }


        .taxi-route-details small {

            font-size: 6px;
        }


        /* BUTTON */

        .taxi-book-btn {

            margin-top: 18px;

            padding:
                5px 6px 5px 18px;

            gap: 9px;

            font-size: 10px;
        }


        .taxi-book-btn b {

            width: 40px;

            height: 40px;

            font-size: 17px;
        }


        /* STATS */

        .taxi-stats {

            width: 100%;

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 7px;

            margin-top: 20px;
        }


        .taxi-stats strong {

            font-size: 10px;
        }


        .taxi-stats small {

            font-size: 6px;
        }


        /* =========================================
     IMAGE AREA
  ========================================== */

        .taxi-visual {

            order: 2;

            width: 100%;

            height: 380px;

            margin-top: 22px;

            overflow: hidden;
        }


        .taxi-yellow-circle {

            width: 340px;

            height: 340px;

            left: 50%;

            top: 10px;

            transform:
                translateX(-50%);
        }


        /* IMAGE */

        .taxi-image {

            width:
                calc(100% - 30px);

            height: 270px;

            left: 15px;

            top: 45px;

            border-radius:
                55px 25px 25px 25px;

            transform: none;
        }


        /* PICKUP */

        .taxi-pickup {

            left: 15px;

            top: 15px;

            padding:
                7px 10px;

            gap: 7px;
        }


        .taxi-pickup-icon {

            width: 26px;

            height: 26px;

            font-size: 12px;
        }


        .taxi-pickup strong {

            font-size: 8px;
        }


        .taxi-pickup small {

            font-size: 6px;
        }


        /* PRICE */

        .taxi-price-card {

            width: 175px;

            left: auto;

            right: 14px;

            bottom: 14px;

            padding: 13px;

            border-radius: 17px;
        }


        .taxi-price-top {

            font-size: 6px;
        }


        .taxi-price-top i {

            width: 7px;

            height: 7px;
        }


        .taxi-city-ride {

            margin-top: 11px;

            font-size: 16px;
        }


        .taxi-comfort {

            font-size: 7px;
        }


        .taxi-price-line {

            margin-top: 12px;

            padding-top: 8px;
        }


        .taxi-price-line span {

            font-size: 7px;
        }


        .taxi-price-line strong {

            font-size: 14px;
        }


        /* HIDE DESKTOP DECOR */

        .taxi-circle {

            display: none;
        }


        .taxi-bg-number {

            display: none;
        }

    }


    /* =========================================================
   SMALL PHONE
========================================================= */

    @media (max-width: 390px) {

        .taxi-content {

            padding:
                34px 14px 0;
        }


        .taxi-heading {

            font-size: 48px;

            letter-spacing: -3.5px;
        }


        .taxi-text {

            font-size: 12px;
        }


        .taxi-route {

            padding: 12px;
        }


        .taxi-route-details strong {

            font-size: 7.5px;
        }


        .taxi-visual {

            height: 345px;

            margin-top: 18px;
        }


        .taxi-yellow-circle {

            width: 300px;

            height: 300px;
        }


        .taxi-image {

            width:
                calc(100% - 26px);

            left: 13px;

            height: 245px;

            top: 40px;
        }


        .taxi-price-card {

            width: 160px;

            right: 10px;

            bottom: 10px;

            padding: 12px;
        }


        .taxi-city-ride {

            font-size: 15px;
        }

    }
</style>



<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {

            const button =
                document.querySelector(
                    ".taxi-book-btn"
                );

            if (!button) return;

            const arrow =
                button.querySelector("b");


            button.addEventListener(
                "mouseenter",
                function() {

                    if (arrow) {

                        arrow.style.transform =
                            "rotate(45deg)";

                    }

                }
            );


            button.addEventListener(
                "mouseleave",
                function() {

                    if (arrow) {

                        arrow.style.transform =
                            "rotate(0deg)";

                    }

                }
            );

        }
    );
</script>