<!-- =========================================================
     FULL WIDTH JOURNEY SECTION
     4 DIFFERENT IMAGE SHAPES
========================================================= -->

<section class="journey-section">

    <!-- =========================
         LEFT LARGE IMAGE
    ========================== -->

    <div class="journey-image">

        <img
            src="https://images.unsplash.com/photo-1521292270410-a8c4d716d518?auto=format&fit=crop&w=1800&q=90"
            alt="Travel experience"
        >

    </div>


    <!-- =========================
         RIGHT DARK CONTENT
    ========================== -->

    <div class="journey-content">

        <div class="journey-inner">


            <!-- TITLE -->

            <h2 class="journey-heading">

                How We <em>Plan</em> Your

                <span>Journey</span>

            </h2>


            <!-- =========================
                 STEPS
            ========================== -->

            <div class="journey-steps">


                <button
                    class="journey-step active"
                    data-step="0"
                    type="button"
                >

                    <svg
                        class="progress-ring"
                        viewBox="0 0 44 44"
                    >

                        <circle
                            class="ring-bg"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                        <circle
                            class="ring-progress"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                    </svg>

                    <span>01</span>

                </button>


                <div class="step-line"></div>


                <button
                    class="journey-step"
                    data-step="1"
                    type="button"
                >

                    <svg
                        class="progress-ring"
                        viewBox="0 0 44 44"
                    >

                        <circle
                            class="ring-bg"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                        <circle
                            class="ring-progress"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                    </svg>

                    <span>02</span>

                </button>


                <div class="step-line"></div>


                <button
                    class="journey-step"
                    data-step="2"
                    type="button"
                >

                    <svg
                        class="progress-ring"
                        viewBox="0 0 44 44"
                    >

                        <circle
                            class="ring-bg"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                        <circle
                            class="ring-progress"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                    </svg>

                    <span>03</span>

                </button>


                <div class="step-line"></div>


                <button
                    class="journey-step"
                    data-step="3"
                    type="button"
                >

                    <svg
                        class="progress-ring"
                        viewBox="0 0 44 44"
                    >

                        <circle
                            class="ring-bg"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                        <circle
                            class="ring-progress"
                            cx="22"
                            cy="22"
                            r="20"
                        />

                    </svg>

                    <span>04</span>

                </button>

            </div>


            <!-- =========================
                 CHANGING IMAGE
            ========================== -->

            <div
                class="journey-art journey-shape-0"
                id="journeyArt"
            >

                <img
                    id="journeyArtImage"
                    src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=600&q=90"
                    alt="Travel planning"
                >

            </div>


            <!-- =========================
                 TEXT
            ========================== -->

            <div
                class="journey-text"
                id="journeyText"
            >

                <h3 id="journeyTitle">
                    Tell Us Your Dream
                </h3>

                <p id="journeyDescription">
                    Share where you want to go, what moves you, and how you want to travel.
                </p>

            </div>

        </div>

    </div>

</section>



<style>

/* =========================================================
   RESET ONLY INSIDE SECTION
========================================================= */

.journey-section,
.journey-section * {

    box-sizing:
        border-box;

}


/* =========================================================
   MAIN FULL WIDTH
========================================================= */

.journey-section {

    position:
        relative;

    width:
        100%;

    min-height:
        780px;

    display:
        grid;

    grid-template-columns:
        58% 42%;

    overflow:
        hidden;

    background:
        #fff2c5;

}


/* =========================================================
   LEFT IMAGE
========================================================= */

.journey-image {

    position:
        relative;

    width:
        100%;

    height:
        780px;

    overflow:
        hidden;

    border-radius:
        28px 0 0 28px;

}


.journey-image img {

    width:
        100%;

    height:
        100%;

    display:
        block;

    object-fit:
        cover;

    object-position:
        center;

    transition:
        transform 1.2s ease;

}


.journey-image:hover img {

    transform:
        scale(1.035);

}


/* =========================================================
   RIGHT DARK PANEL
========================================================= */

.journey-content {

    position:
        relative;

    width:
        100%;

    height:
        780px;

    padding:
        20px;

    background:
        #181816;

    color:
        #fff;

    border-radius:
        0 28px 28px 0;

}


/* =========================================================
   INNER BORDER
========================================================= */

.journey-inner {

    position:
        relative;

    width:
        100%;

    height:
        100%;

    min-height:
        740px;

    border:
        1px dashed
        rgba(255,255,255,.25);

    border-radius:
        20px;

    display:
        flex;

    flex-direction:
        column;

    align-items:
        center;

    text-align:
        center;

    padding:
        70px 35px 35px;

}


/* =========================================================
   TITLE
========================================================= */

.journey-heading {

    margin:
        0;

    max-width:
        600px;

    color:
        #fff;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size:
        clamp(40px, 3.7vw, 62px);

    line-height:
        1.04;

    letter-spacing:
        -3px;

    font-weight:
        700;

}


.journey-heading em {

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-style:
        italic;

    font-weight:
        500;

}


.journey-heading span {

    display:
        block;

}


/* =========================================================
   STEP NAV
========================================================= */

.journey-steps {

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    margin-top:
        48px;

}


/* =========================================================
   STEP BUTTON
========================================================= */

.journey-step {

    position:
        relative;

    width:
        50px;

    height:
        50px;

    flex:
        0 0 50px;

    padding:
        0;

    border:
        0;

    background:
        transparent;

    border-radius:
        50%;

    color:
        rgba(255,255,255,.38);

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    font-family:
        Arial,
        sans-serif;

    font-size:
        15px;

    font-weight:
        600;

    cursor:
        pointer;

    transition:
        color .3s ease,
        transform .3s ease;

}


.journey-step span {

    position:
        relative;

    z-index:
        5;

}


.journey-step.active {

    color:
        #fff;

    transform:
        scale(1.04);

}


.journey-step.completed {

    color:
        #fff;

}


/* =========================================================
   CIRCLE RING
========================================================= */

.progress-ring {

    position:
        absolute;

    inset:
        0;

    width:
        100%;

    height:
        100%;

    transform:
        rotate(-90deg);

}


.ring-bg {

    fill:
        none;

    stroke:
        rgba(255,255,255,.10);

    stroke-width:
        1.5;

}


.ring-progress {

    fill:
        none;

    stroke:
        #fff;

    stroke-width:
        2;

    stroke-linecap:
        round;

    stroke-dasharray:
        125.66;

    stroke-dashoffset:
        125.66;

}


.journey-step.active .ring-progress {

    animation:
        journeyProgress
        4s
        linear
        forwards;

}


.journey-step.completed .ring-progress {

    stroke:
        #fff;

    stroke-dashoffset:
        0;

}


@keyframes journeyProgress {

    from {

        stroke-dashoffset:
            125.66;

    }

    to {

        stroke-dashoffset:
            0;

    }

}


/* =========================================================
   STEP LINE
========================================================= */

.step-line {

    width:
        30px;

    height:
        1px;

    flex:
        0 0 30px;

    background:
        rgba(255,255,255,.17);

}


/* =========================================================
   IMAGE CONTAINER
========================================================= */

.journey-art {

    position:
        relative;

    width:
        190px;

    height:
        190px;

    margin-top:
        42px;

    overflow:
        hidden;

    border:
        2px solid
        #f8d9c8;

    box-shadow:
        0 18px 45px
        rgba(0,0,0,.38);

    transition:
        opacity .35s ease,
        transform .6s cubic-bezier(.2,.8,.2,1);

}


/* =========================================================
   IMAGE
========================================================= */

.journey-art img {

    width:
        100%;

    height:
        100%;

    display:
        block;

    object-fit:
        cover;

    object-position:
        center;

    transition:
        transform .6s ease;

}


/* =========================================================
   01 — FLOWER / SCALLOPED
========================================================= */

.journey-shape-0 {

    border-radius:
        42%
        58%
        45%
        55%
        /
        55%
        45%
        58%
        42%;

}


/* Extra scallop feeling */

.journey-shape-0::before {

    content:
        "";

    position:
        absolute;

    inset:
        -2px;

    z-index:
        3;

    pointer-events:
        none;

    border:
        2px solid
        #f8d9c8;

    border-radius:
        42%
        58%
        45%
        55%
        /
        55%
        45%
        58%
        42%;

}


/* =========================================================
   02 — CUT BOX / ORGANIC MAP
========================================================= */

.journey-shape-1 {

    border-radius:
        18%
        35%
        22%
        38%
        /
        28%
        20%
        34%
        25%;

    transform:
        rotate(-1deg);

}


/* =========================================================
   03 — CURVED STAR / BURST
========================================================= */

.journey-shape-2 {

    clip-path:
        polygon(
            50% 0%,
            61% 13%,
            76% 7%,
            79% 23%,
            95% 27%,
            87% 42%,
            100% 53%,
            87% 62%,
            94% 78%,
            78% 80%,
            72% 96%,
            57% 87%,
            46% 100%,
            36% 87%,
            21% 94%,
            20% 78%,
            4% 74%,
            12% 59%,
            0% 48%,
            13% 38%,
            7% 22%,
            23% 21%,
            28% 5%,
            42% 14%
        );

    border:
        0;

}


/* Border simulation for star */

.journey-shape-2::after {

    content:
        "";

    position:
        absolute;

    inset:
        0;

    z-index:
        4;

    pointer-events:
        none;

    border:
        2px solid
        #f8d9c8;

    clip-path:
        inherit;

}


/* =========================================================
   04 — ROUNDED DIAMOND
========================================================= */

.journey-shape-3 {

    border-radius:
        42%
        58%
        42%
        58%
        /
        58%
        42%
        58%
        42%;

    transform:
        rotate(45deg);

}


/* Keep image itself visually straight */

.journey-shape-3 img {

    transform:
        rotate(-45deg)
        scale(1.35);

}


/* =========================================================
   TEXT
========================================================= */

.journey-text {

    width:
        100%;

    max-width:
        500px;

    margin-top:
        28px;

    transition:
        opacity .35s ease,
        transform .35s ease;

}


.journey-text.changing {

    opacity:
        0;

    transform:
        translateY(12px);

}


.journey-text h3 {

    margin:
        0 0 12px;

    color:
        #fff;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size:
        27px;

    line-height:
        1.15;

    letter-spacing:
        -1px;

    font-weight:
        600;

}


.journey-text p {

    margin:
        0;

    color:
        rgba(255,255,255,.58);

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size:
        15px;

    line-height:
        1.45;

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width:1100px) {

    .journey-section {

        grid-template-columns:
            56% 44%;

    }


    .journey-image,
    .journey-content {

        height:
            680px;

    }


    .journey-inner {

        min-height:
            640px;

        padding:
            55px 20px 25px;

    }


    .journey-heading {

        font-size:
            40px;

    }


    .journey-steps {

        margin-top:
            34px;

    }


    .journey-art {

        width:
            155px;

        height:
            155px;

        margin-top:
            30px;

    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width:800px) {

    .journey-section {

        width:
            100%;

        min-height:
            auto;

        display:
            flex;

        flex-direction:
            column;

    }


    /* LEFT IMAGE */

    .journey-image {

        width:
            100%;

        height:
            340px;

        min-height:
            340px;

        border-radius:
            24px 24px 0 0;

    }


    /* RIGHT */

    .journey-content {

        width:
            100%;

        height:
            auto;

        min-height:
            620px;

        padding:
            10px;

        border-radius:
            0 0 24px 24px;

    }


    .journey-inner {

        min-height:
            595px;

        height:
            auto;

        padding:
            38px 15px 30px;

        border-radius:
            18px;

    }


    .journey-heading {

        font-size:
            34px;

        letter-spacing:
            -1.5px;

    }


    .journey-steps {

        margin-top:
            28px;

    }


    .journey-step {

        width:
            44px;

        height:
            44px;

        flex-basis:
            44px;

        font-size:
            12px;

    }


    .step-line {

        width:
            17px;

        flex-basis:
            17px;

    }


    .journey-art {

        width:
            145px;

        height:
            145px;

        margin-top:
            30px;

    }


    .journey-text {

        max-width:
            330px;

        margin-top:
            25px;

    }


    .journey-text h3 {

        font-size:
            21px;

    }


    .journey-text p {

        font-size:
            13px;

    }

}


/* =========================================================
   SMALL PHONE
========================================================= */

@media (max-width:480px) {

    .journey-image {

        height:
            280px;

        min-height:
            280px;

    }


    .journey-content {

        min-height:
            550px;

        padding:
            8px;

    }


    .journey-inner {

        min-height:
            525px;

        padding:
            30px 10px 25px;

    }


    .journey-heading {

        font-size:
            29px;

    }


    .journey-steps {

        margin-top:
            23px;

    }


    .journey-step {

        width:
            39px;

        height:
            39px;

        flex-basis:
            39px;

        font-size:
            11px;

    }


    .step-line {

        width:
            12px;

        flex-basis:
            12px;

    }


    .journey-art {

        width:
            118px;

        height:
            118px;

        margin-top:
            26px;

    }


    .journey-text {

        margin-top:
            21px;

        max-width:
            290px;

    }


    .journey-text h3 {

        font-size:
            19px;

    }


    .journey-text p {

        font-size:
            12px;

        line-height:
            1.4;

    }

}

</style>



<script>

/* =========================================================
   JOURNEY DATA
========================================================= */

(function () {

    const journeyData = [

        {
            title:
                "Tell Us Your Dream",

            description:
                "Share where you want to go, what moves you, and how you want to travel.",

            image:
                "https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=600&q=90",

            shape:
                "journey-shape-0"
        },


        {
            title:
                "We Design Your Route",

            description:
                "Your dedicated journey specialist creates a route around your preferences and budget.",

            image:
                "https://images.unsplash.com/photo-1519904981063-b0cf448d479e?auto=format&fit=crop&w=600&q=90",

            shape:
                "journey-shape-1"
        },


        {
            title:
                "We Refine Every Detail",

            description:
                "Hotels, experiences, transfers and hidden gems are carefully selected for your journey.",

            image:
                "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=600&q=90",

            shape:
                "journey-shape-2"
        },


        {
            title:
                "You Simply Enjoy",

            description:
                "Everything is ready before you leave, so you can focus on discovering new places.",

            image:
                "https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=90",

            shape:
                "journey-shape-3"
        }

    ];


    const STEP_DURATION =
        4000;


    let currentStep =
        0;


    let timer;


    const steps =
        document.querySelectorAll(
            ".journey-step"
        );


    const art =
        document.getElementById(
            "journeyArt"
        );


    const artImage =
        document.getElementById(
            "journeyArtImage"
        );


    const title =
        document.getElementById(
            "journeyTitle"
        );


    const description =
        document.getElementById(
            "journeyDescription"
        );


    const text =
        document.getElementById(
            "journeyText"
        );


    /* =====================================================
       CHANGE STEP
    ===================================================== */

    function changeStep(index) {

        currentStep =
            index;


        clearTimeout(timer);


        /* RESET STEPS */

        steps.forEach(
            function(step, i) {

                step.classList.remove(
                    "active",
                    "completed"
                );


                if (i < index) {

                    step.classList.add(
                        "completed"
                    );

                }

            }
        );


        /* ACTIVE */

        steps[index].classList.add(
            "active"
        );


        /* FADE OUT */

        text.classList.add(
            "changing"
        );


        art.style.opacity =
            "0";


        art.style.transform =
            "scale(.94)";


        setTimeout(
            function() {

                /* CHANGE CONTENT */

                title.textContent =
                    journeyData[index].title;


                description.textContent =
                    journeyData[index].description;


                artImage.src =
                    journeyData[index].image;


                /* CHANGE SHAPE */

                art.classList.remove(
                    "journey-shape-0",
                    "journey-shape-1",
                    "journey-shape-2",
                    "journey-shape-3"
                );


                art.classList.add(
                    journeyData[index].shape
                );


                /* SHOW */

                art.style.opacity =
                    "1";


                art.style.transform =
                    "scale(1)";


                text.classList.remove(
                    "changing"
                );

            },
            320
        );


        /* NEXT */

        timer =
            setTimeout(
                function() {

                    let next =
                        currentStep + 1;


                    if (
                        next >=
                        journeyData.length
                    ) {

                        next = 0;

                    }


                    changeStep(
                        next
                    );

                },
                STEP_DURATION
            );

    }


    /* =====================================================
       CLICK
    ===================================================== */

    steps.forEach(
        function(step, index) {

            step.addEventListener(
                "click",
                function() {

                    changeStep(
                        index
                    );

                }
            );

        }
    );


    /* START */

    changeStep(0);

})();

</script>