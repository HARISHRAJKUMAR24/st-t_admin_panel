<?php

/*
|--------------------------------------------------------------------------
| FOOTER SETTINGS
|--------------------------------------------------------------------------
*/

if (!isset($pdo)) {
  require_once __DIR__ . '/../config/config.php';
}


/*
|--------------------------------------------------------------------------
| GET SETTINGS
|--------------------------------------------------------------------------
*/

$footerSettings = getSettings($pdo);


/*
|--------------------------------------------------------------------------
| FOOTER DATA
|--------------------------------------------------------------------------
*/

$footerSiteName = !empty($footerSettings['site_title'])
  ? $footerSettings['site_title']
  : ($footerSettings['site_name'] ?? '');

$footerTagline = $footerSettings['site_tagline'] ?? '';

$footerAddress = $footerSettings['address'] ?? '';

$footerPhone = $footerSettings['contact_phone'] ?? '';

$footerEmail = $footerSettings['contact_email'] ?? '';

$footerText = $footerSettings['footer_text'] ?? '';

$footerSocialLinks = getSocialLinks($pdo);

?>
<style>
 /* =========================================================
   PROFESSIONAL TRAVEL FOOTER
   Background image retained
   ========================================================= */

.travel-footer {
    position: relative;
    width: 100%;
    min-height: 500px;
    overflow: hidden;

    background:
        linear-gradient(
            180deg,
            rgba(0, 0, 0, 0.12) 0%,
            rgba(0, 0, 0, 0.42) 48%,
            rgba(0, 0, 0, 0.88) 100%
        ),
        url("./assets/images/footer.png")
        center center / cover no-repeat;

    color: #fff;
}


/* =========================================================
   DARK OVERLAY
   ========================================================= */

.footer-overlay {
    position: absolute;
    inset: 0;

    background:
        linear-gradient(
            90deg,
            rgba(0, 0, 0, 0.55) 0%,
            rgba(0, 0, 0, 0.12) 48%,
            rgba(0, 0, 0, 0.42) 100%
        );

    pointer-events: none;
}


/* =========================================================
   MAIN CONTAINER
   ========================================================= */

.footer-content {
    position: relative;
    z-index: 2;

    width: min(1180px, calc(100% - 80px));

    min-height: 500px;

    margin: 0 auto;

    padding: 90px 0 24px;

    display: flex;
    flex-direction: column;

    justify-content: flex-end;
}


/* =========================================================
   MAIN FOOTER AREA
   ========================================================= */

.footer-main {
    width: 100%;

    display: grid;

    grid-template-columns: 1.15fr 0.85fr;

    column-gap: 120px;

    align-items: end;

    padding-bottom: 20px;
}


/* =========================================================
   BRAND
   ========================================================= */

.footer-brand {
    max-width: 540px;

    padding-bottom: 5px;
}


.footer-logo {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    margin: 0 0 18px;

    color: #fff;

    text-decoration: none;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: clamp(32px, 3vw, 42px);

    font-weight: 600;

    font-style: italic;

    line-height: 1.15;

    letter-spacing: -0.6px;

    text-shadow:
        0 2px 12px rgba(0, 0, 0, 0.35);
}


.footer-logo span {
    color: #f0b36b;

    font-size: 19px;

    font-style: normal;
}


.footer-brand p {
    max-width: 470px;

    margin: 0;

    color:
        rgba(255, 255, 255, 0.82);

    font-size: 14px;

    line-height: 1.8;

    text-shadow:
        0 1px 8px rgba(0, 0, 0, 0.35);
}


/* =========================================================
   CONTACT
   ========================================================= */

.footer-contact {
    max-width: 350px;

    justify-self: end;

    padding-bottom: 5px;
}


.footer-contact h3 {
    margin: 0 0 12px;

    color: #fff;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 23px;

    font-weight: 600;

    font-style: italic;

    line-height: 1.2;

    text-shadow:
        0 2px 10px rgba(0, 0, 0, 0.4);
}


.footer-column-line {
    width: 50px;

    height: 2px;

    margin-bottom: 20px;

    background: rgba(255, 255, 255, 0.85);
}


.footer-contact p {
    margin: 0 0 17px;

    color:
        rgba(255, 255, 255, 0.82);

    font-size: 14px;

    line-height: 1.75;

    text-shadow:
        0 1px 8px rgba(0, 0, 0, 0.4);
}


.footer-contact a {
    display: block;

    width: fit-content;

    margin-bottom: 8px;

    color: #fff;

    font-size: 14px;

    line-height: 1.5;

    text-decoration: none;

    text-shadow:
        0 1px 8px rgba(0, 0, 0, 0.4);

    transition:
        color 0.2s ease,
        transform 0.2s ease;
}


.footer-contact a:hover {
    color: #f0b36b;

    transform: translateX(3px);
}


/* =========================================================
   BOTTOM BAR
   ========================================================= */

.footer-bottom {
    width: 100%;

    padding-top: 20px;

    border-top:
        1px solid
        rgba(255, 255, 255, 0.28);

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 30px;
}


/* =========================================================
   COPYRIGHT
   ========================================================= */

.footer-copyright {
    color:
        rgba(255, 255, 255, 0.72);

    font-size: 12px;

    line-height: 1.5;
}


/* =========================================================
   SOCIAL ICONS
   ========================================================= */

.footer-socials {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;
}


.footer-socials a {
    width: 34px;
    height: 34px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background:
        rgba(255, 255, 255, 0.94);

    color: #222;

    text-decoration: none;

    font-size: 13px;

    box-shadow:
        0 3px 12px rgba(0, 0, 0, 0.18);

    transition:
        transform 0.2s ease,
        background 0.2s ease,
        color 0.2s ease;
}


.footer-socials a:hover {
    transform: translateY(-3px);

    background: #fff;

    color: #bd795e;
}


/* =========================================================
   REMOVE OLD STATIC FOOTER PARTS
   ========================================================= */

.footer-newsletter,
.footer-destinations,
.footer-location-btn {
    display: none !important;
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 900px) {

    .travel-footer {
        min-height: 480px;
    }

    .footer-content {
        width: calc(100% - 50px);

        min-height: 480px;

        padding-top: 70px;
    }

    .footer-main {
        grid-template-columns: 1fr 1fr;

        column-gap: 50px;
    }

    .footer-contact {
        justify-self: end;

        max-width: 300px;
    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 650px) {

    .travel-footer {
        min-height: auto;
    }

    .footer-content {
        width: calc(100% - 36px);

        min-height: auto;

        padding:
            55px 0 20px;
    }

    .footer-main {
        grid-template-columns: 1fr;

        row-gap: 38px;

        padding-bottom: 10px;
    }

    .footer-brand {
        max-width: 100%;
    }

    .footer-contact {
        max-width: 100%;

        justify-self: start;
    }

    .footer-logo {
        font-size: 30px;
    }

    .footer-brand p {
        font-size: 13px;
    }

    .footer-contact h3 {
        font-size: 21px;
    }

    .footer-contact p,
    .footer-contact a {
        font-size: 13px;
    }

    .footer-bottom {
        margin-top: 25px;

        flex-direction: column;

        align-items: flex-start;

        gap: 18px;
    }

    .footer-socials {
        justify-content: flex-start;

        flex-wrap: wrap;
    }

    .footer-socials a {
        width: 34px;
        height: 34px;
    }

}
</style>
<footer class="travel-footer">

  <!-- Background overlay -->
  <div class="footer-overlay"></div>

 <div class="footer-content">

    <div class="footer-main">

        <!-- BRAND -->
        <div class="footer-brand">

            <?php if ($footerSiteName !== ''): ?>

                <a
                    href="<?= htmlspecialchars(SITE_URL); ?>"
                    class="footer-logo"
                >
                    <?= htmlspecialchars($footerSiteName); ?>
                    <span>✦</span>
                </a>

            <?php endif; ?>


            <?php if ($footerTagline !== ''): ?>

                <p>
                    <?= nl2br(
                        htmlspecialchars($footerTagline)
                    ); ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- CONTACT -->
        <div class="footer-column footer-contact">

            <h3>Contact Info</h3>

            <div class="footer-column-line"></div>


            <?php if ($footerAddress !== ''): ?>

                <p>
                    <?= nl2br(
                        htmlspecialchars($footerAddress)
                    ); ?>
                </p>

            <?php endif; ?>


            <?php if ($footerPhone !== ''): ?>

                <a href="tel:<?= htmlspecialchars($footerPhone); ?>">
                    <?= htmlspecialchars($footerPhone); ?>
                </a>

            <?php endif; ?>


            <?php if ($footerEmail !== ''): ?>

                <a href="mailto:<?= htmlspecialchars($footerEmail); ?>">
                    <?= htmlspecialchars($footerEmail); ?>
                </a>

            <?php endif; ?>

        </div>

    </div>


    <!-- BOTTOM -->
    <div class="footer-bottom">

        <div class="footer-copyright">

            <?php if ($footerText !== ''): ?>

                <?= htmlspecialchars($footerText); ?>

            <?php endif; ?>

        </div>


        <?php if (!empty($footerSocialLinks)): ?>

            <div class="footer-socials">

                <?php foreach (
                    $footerSocialLinks as $platform => $url
                ): ?>

                    <?php if (!empty($url)): ?>

                        <?php

                        $icon = 'bi bi-link-45deg';

                        switch (strtolower(trim($platform))) {

                            case 'facebook':
                                $icon = 'bi bi-facebook';
                                break;

                            case 'instagram':
                                $icon = 'bi bi-instagram';
                                break;

                            case 'linkedin':
                                $icon = 'bi bi-linkedin';
                                break;

                            case 'twitter':
                            case 'x':
                                $icon = 'bi bi-twitter-x';
                                break;

                            case 'youtube':
                                $icon = 'bi bi-youtube';
                                break;

                            case 'whatsapp':
                                $icon = 'bi bi-whatsapp';
                                break;
                        }

                        ?>

                        <a
                            href="<?= htmlspecialchars($url); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="<?= htmlspecialchars($platform); ?>"
                        >
                            <i class="<?= $icon; ?>"></i>
                        </a>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

</footer>