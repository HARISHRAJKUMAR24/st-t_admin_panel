<!-- =========================================================
     FULL WIDTH · REDUCED HEIGHT JOURNEY SECTION
========================================================= -->
<section class="journey-section">

    <!-- LEFT IMAGE -->
    <div class="journey-image">
        <img id="journeyMainImage"
            src="https://images.unsplash.com/photo-1521292270410-a8c4d716d518?auto=format&fit=crop&w=1600&q=90"
            alt="Travel experience">
    </div>

    <!-- RIGHT PANEL -->
    <div class="journey-content">
        <div class="journey-inner">

            <!-- TITLE -->
            <h2>
                How We <em>Plan</em> Your
                <span>Journey</span>
            </h2>

            <!-- AUTO STEP NAVIGATION -->
            <div class="journey-steps">
                <!-- 01 -->
                <button class="journey-step active" data-step="0">
                    <svg class="progress-ring" viewBox="0 0 44 44">
                        <circle class="ring-bg" cx="22" cy="22" r="20" />
                        <circle class="ring-progress" cx="22" cy="22" r="20" />
                    </svg>
                    <span>01</span>
                </button>
                <div class="step-line"></div>

                <!-- 02 -->
                <button class="journey-step" data-step="1">
                    <svg class="progress-ring" viewBox="0 0 44 44">
                        <circle class="ring-bg" cx="22" cy="22" r="20" />
                        <circle class="ring-progress" cx="22" cy="22" r="20" />
                    </svg>
                    <span>02</span>
                </button>
                <div class="step-line"></div>

                <!-- 03 -->
                <button class="journey-step" data-step="2">
                    <svg class="progress-ring" viewBox="0 0 44 44">
                        <circle class="ring-bg" cx="22" cy="22" r="20" />
                        <circle class="ring-progress" cx="22" cy="22" r="20" />
                    </svg>
                    <span>03</span>
                </button>
                <div class="step-line"></div>

                <!-- 04 -->
                <button class="journey-step" data-step="3">
                    <svg class="progress-ring" viewBox="0 0 44 44">
                        <circle class="ring-bg" cx="22" cy="22" r="20" />
                        <circle class="ring-progress" cx="22" cy="22" r="20" />
                    </svg>
                    <span>04</span>
                </button>
            </div>

            <!-- DECORATIVE MAP IMAGE -->
            <div class="journey-art">
                <img id="journeyArt"
                    src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=400&q=90"
                    alt="Travel map">
            </div>

            <!-- TEXT -->
            <div class="journey-text" id="journeyText">
                <h3 id="journeyTitle">We Design Your Route</h3>
                <p id="journeyDescription">
                    Your dedicated journey specialist crafts a bespoke itinerary around your exact preferences and budget.
                </p>
            </div>

        </div>
    </div>
</section>

<style>
    /* =========================================================
       FULL WIDTH · COMPACT HEIGHT JOURNEY SECTION
    ========================================================= */
    .journey-section {
        width: 100%;
        min-height: 500px; /* REDUCED HEIGHT HERE */
        display: grid;
        grid-template-columns: 58% 42%;
        background: #fff2c5;
        overflow: hidden;
    }

    .journey-image {
        position: relative;
        min-height: 500px; /* REDUCED HEIGHT HERE */
        overflow: hidden;
        border-radius: 28px 0 0 28px;
    }

    .journey-image img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        transition: transform 1.2s ease;
    }

    .journey-image:hover img {
        transform: scale(1.025);
    }

    .journey-content {
        min-height: 500px; /* REDUCED HEIGHT HERE */
        padding: 20px;
        background: #181816;
        color: white;
        border-radius: 0 28px 28px 0;
    }

    .journey-inner {
        min-height: 460px;
        height: 100%;
        border: 1px dashed rgba(255, 255, 255, .25);
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 40px 35px 25px; /* REDUCED PADDING */
    }

    .journey-inner h2 {
        max-width: 520px;
        font-size: clamp(32px, 3.5vw, 42px); /* SLIGHTLY SMALLER TEXT */
        line-height: 1.08;
        letter-spacing: -2px;
        font-weight: 600;
    }

    .journey-inner h2 em {
        font-family: "Playfair Display", Georgia, serif;
        font-style: italic;
        font-weight: 500;
    }

    .journey-inner h2 span {
        display: block;
    }

    .journey-steps {
        margin-top: 30px; /* REDUCED MARGIN */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .journey-step {
        position: relative;
        width: 44px; /* SMALLER CIRCLE */
        height: 44px;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: transparent;
        color: rgba(255, 255, 255, .40);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color .35s ease, transform .35s ease;
    }

    .journey-step span {
        position: relative;
        z-index: 3;
    }

    .journey-step.active {
        color: #fff;
        transform: scale(1.04);
    }

    .progress-ring {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
        overflow: visible;
    }

    .ring-bg {
        fill: none;
        stroke: rgba(255, 255, 255, .10);
        stroke-width: 1.5;
    }

    .ring-progress {
        fill: none;
        stroke: #fff;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-dasharray: 125.66;
        stroke-dashoffset: 125.66;
    }

    .journey-step.active .ring-progress {
        animation: circleLoading 4s linear forwards;
    }

    @keyframes circleLoading {
        from { stroke-dashoffset: 125.66; }
        to { stroke-dashoffset: 0; }
    }

    .journey-step.completed {
        color: #fff;
    }

    .journey-step.completed .ring-progress {
        stroke: #fff;
        stroke-dashoffset: 0;
    }

    .step-line {
        width: 25px; /* SHORTER LINE */
        height: 1px;
        background: rgba(255, 255, 255, .17);
    }

    .journey-art {
        width: 140px; /* SMALLER ART */
        height: 140px;
        margin-top: 25px;
        overflow: hidden;
        border-radius: 30% 40% 35% 45%;
        transform: rotate(3deg);
        border: 1px solid rgba(255, 255, 255, .15);
        box-shadow: 0 12px 30px rgba(0, 0, 0, .35);
        transition: transform .5s ease;
    }

    .journey-art:hover {
        transform: rotate(0deg) scale(1.03);
    }

    .journey-art img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        opacity: 1;
        transition: opacity .45s ease, transform .6s ease;
    }

    .journey-text {
        max-width: 430px;
        margin-top: 20px; /* REDUCED MARGIN */
        transition: opacity .35s ease, transform .35s ease;
    }

    .journey-text.changing {
        opacity: 0;
        transform: translateY(10px);
    }

    .journey-text h3 {
        margin-bottom: 8px;
        font-size: 24px; /* SMALLER TITLE */
        letter-spacing: -1px;
        font-weight: 600;
    }

    .journey-text p {
        color: rgba(255, 255, 255, .58);
        font-size: 15px; /* SMALLER PARAGRAPH */
        line-height: 1.35;
    }

    /* =========================================================
       MOBILE RESPONSIVE
    ========================================================= */
    @media(max-width:900px) {
        .journey-section {
            grid-template-columns: 1fr;
            min-height: auto;
        }
        .journey-image {
            min-height: 350px; /* REDUCED MOBILE HEIGHT */
            height: 350px;
            border-radius: 28px 28px 0 0;
        }
        .journey-content {
            min-height: auto;
            border-radius: 0 0 28px 28px;
        }
        .journey-inner {
            min-height: 450px;
            padding: 35px 20px 25px;
        }
        .journey-inner h2 {
            font-size: 34px;
        }
    }

    @media(max-width:600px) {
        .journey-image {
            min-height: 280px; /* EVEN SMALLER ON PHONES */
            height: 280px;
        }
        .journey-content {
            padding: 10px;
        }
        .journey-inner {
            min-height: 400px;
            padding: 25px 15px 20px;
        }
        .journey-inner h2 {
            font-size: 28px;
            letter-spacing: -1px;
        }
        .journey-step {
            width: 38px;
            height: 38px;
            font-size: 12px;
        }
        .step-line {
            width: 15px;
        }
        .journey-art {
            width: 110px;
            height: 110px;
        }
        .journey-text h3 {
            font-size: 20px;
        }
        .journey-text p {
            font-size: 13px;
        }
    }
</style>

<script>
    /* =========================================================
       JOURNEY DATA
    ========================================================= */
    const journeyData = [
        {
            title: "We Design Your Route",
            description: "Your dedicated journey specialist crafts a bespoke itinerary around your exact preferences and budget.",
            image: "https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=400&q=90"
        },
        {
            title: "We Match Your Style",
            description: "From slow escapes to unforgettable adventures, we shape every detail around the way you love to travel.",
            image: "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=400&q=90"
        },
        {
            title: "We Refine Every Detail",
            description: "Hotels, experiences, transfers and hidden gems are carefully selected to create a seamless journey.",
            image: "https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=400&q=90"
        },
        {
            title: "You Simply Enjoy",
            description: "Everything is ready before you leave, so you can focus on discovering new places and making memories.",
            image: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=400&q=90"
        }
    ];

    const STEP_DURATION = 4000;
    let currentStep = 0;
    let stepTimer;

    const steps = document.querySelectorAll(".journey-step");
    const title = document.getElementById("journeyTitle");
    const description = document.getElementById("journeyDescription");
    const art = document.getElementById("journeyArt");
    const text = document.getElementById("journeyText");

    function changeStep(index) {
        currentStep = index;
        clearTimeout(stepTimer);

        steps.forEach((step, i) => {
            step.classList.remove("active", "completed");
            if (i < index) step.classList.add("completed");
        });
        steps[index].classList.add("active");

        text.classList.add("changing");
        art.style.opacity = "0";

        setTimeout(() => {
            title.textContent = journeyData[index].title;
            description.textContent = journeyData[index].description;
            art.src = journeyData[index].image;
            text.classList.remove("changing");
            art.style.opacity = "1";
        }, 300);

        stepTimer = setTimeout(() => {
            let next = currentStep + 1;
            if (next >= journeyData.length) next = 0;
            changeStep(next);
        }, STEP_DURATION);
    }

    steps.forEach((step, index) => {
        step.addEventListener("click", () => changeStep(index));
    });

    changeStep(0);
</script>