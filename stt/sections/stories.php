<?php
$testimonials = getTestimonials($pdo, 12);
?>

<?php if (!empty($testimonials)): ?>

<section class="stories-section">

    <div class="stories-inner">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="row align-items-start gy-4">

            <div class="col-lg-7">

                <span class="stories-badge">
                    ✥&nbsp; What Our Travellers Say &nbsp;✥
                </span>

                <div class="stories-title">
                    Stories That <em>Come</em> Back<br>
                    With Every Journey
                </div>

                <div class="stories-cta-row">

                    <a href="#" class="stories-btn">

                        <span>
                            See All Travellers Stories
                        </span>

                        <span class="arrow">
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                    <a href="#" class="stories-ig">

                        <i class="bi bi-instagram"></i>

                        Instagram Stories

                    </a>

                </div>


                <!-- RATING -->

                <div class="stories-rating-row">

                    <div class="stories-avatars">

                        <?php
                        $ratingAvatars = array_slice(
                            $testimonials,
                            0,
                            4
                        );
                        ?>

                        <?php foreach ($ratingAvatars as $testimonial): ?>

                            <img
                                src="<?= htmlspecialchars(
                                    $testimonial['logo_url']
                                ); ?>"
                                alt="<?= htmlspecialchars(
                                    $testimonial['name']
                                ); ?>"
                            >

                        <?php endforeach; ?>

                    </div>


                    <div>

                        <div class="stories-score">

                            <span class="stars">
                                ★★★★★
                            </span>

                        </div>

                        <div class="stories-score-sub">

                            Based on
                            <?= number_format(
                                count($testimonials)
                            ); ?>
                            traveller reviews

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 PHOTO STACK
            ================================================== -->

            <div class="col-lg-5">

                <div class="stories-photo-stack">

                    <?php
                    $photoTestimonials = array_slice(
                        $testimonials,
                        0,
                        3
                    );
                    ?>

                    <?php foreach (
                        $photoTestimonials
                        as $testimonial
                    ): ?>

                        <img
                            src="<?= htmlspecialchars(
                                $testimonial['logo_url']
                            ); ?>"
                            alt="<?= htmlspecialchars(
                                $testimonial['name']
                            ); ?>"
                        >

                    <?php endforeach; ?>

                </div>

            </div>

        </div>


        <!-- =====================================================
             TESTIMONIAL COLUMNS
        ====================================================== -->

        <div class="stories-columns">

            <?php

            /*
             * Split testimonials into 3 columns
             */

            $columns = [
                [],
                [],
                []
            ];

            foreach (
                $testimonials
                as $index => $testimonial
            ) {

                $columnIndex = $index % 3;

                $columns[$columnIndex][] =
                    $testimonial;
            }

            ?>


            <?php foreach ($columns as $columnIndex => $column): ?>

                <div
                    class="stories-col <?= $columnIndex === 1
                        ? 'col-mid'
                        : ''; ?>"
                >

                    <?php foreach ($column as $testimonial): ?>

                        <div class="stories-card">

                            <!-- PERSON -->

                            <div class="stories-person">

                                <img
                                    src="<?= htmlspecialchars(
                                        $testimonial['logo_url']
                                    ); ?>"
                                    alt="<?= htmlspecialchars(
                                        $testimonial['name']
                                    ); ?>"
                                >

                                <div>

                                    <div class="name">

                                        <?= htmlspecialchars(
                                            $testimonial['name']
                                        ); ?>

                                    </div>

                                    <div class="role">

                                        Traveller

                                    </div>

                                </div>

                            </div>


                            <!-- DIVIDER -->

                            <div class="stories-card-divider"></div>


                            <!-- TESTIMONIAL -->

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $testimonial['testimonial']
                                    )
                                ); ?>

                            </p>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const storiesSection =
        document.querySelector(".stories-section");

    const storiesColumns =
        document.querySelectorAll(
            ".stories-columns .stories-col"
        );

    if (
        !storiesSection ||
        storiesColumns.length !== 3
    ) {
        return;
    }


    let positions = [
        0,
        -35,
        0
    ];


    let directions = [
        1,
        -1,
        1
    ];


    const speeds = [
        7,
        5,
        7
    ];


    let lastTime = performance.now();


    function animateStories(currentTime) {

        const delta = Math.min(
            (currentTime - lastTime) / 1000,
            0.05
        );

        lastTime = currentTime;


        for (let i = 0; i < 3; i++) {

            positions[i] +=
                directions[i] *
                speeds[i] *
                delta;


            if (positions[i] >= 35) {

                positions[i] = 35;

                directions[i] = -1;
            }


            if (positions[i] <= -35) {

                positions[i] = -35;

                directions[i] = 1;
            }


            storiesColumns[i].style.transform =
                `translate3d(0, ${positions[i]}px, 0)`;
        }


        requestAnimationFrame(
            animateStories
        );
    }


    requestAnimationFrame(
        animateStories
    );

});
</script>