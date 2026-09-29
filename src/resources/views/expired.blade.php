<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ShopKeeper') }} | @lang('kazitds::kazitds.Expired')</title>
    <link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: #0f172a; /* dark login-style background */
            color: #fff;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        /* Particles background */
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
            top: 0;
            left: 0;
        }

        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
            margin: auto;
            top: 50%;
            transform: translateY(-50%);
        }

        .error-code {
            font-size: 5.5rem;
            font-weight: bold;
            color: #1d4ed8; /* Encodex primary */
            line-height: 1;
        }

        .error-message {
            font-size: 1.25rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .btn-encodex {
            background-color: #1d4ed8;
            color: #fff;
            transition: 0.3s;
        }

        .btn-encodex:hover {
            background-color: #2563eb;
            color: #fff;
        }

        .encodex-icon {
            font-size: 6rem;
            color: #1d4ed8;
        }

        .support-message {
            color: #94a3b8;
            font-size: 1rem;
        }

        @media (max-width: 575.98px) {
            .error-container {
                max-width: 320px;
            }

            .error-code {
                font-size: 3.25rem;
            }

            .encodex-icon {
                font-size: 4.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Particles -->
    <div id="particles-js"></div>

    <div class="error-container animate__animated animate__fadeIn">
        <!-- Encodex Icon -->
        <div class="mb-4 animate__animated animate__bounceIn">
            <i class="fas fa-calendar-times encodex-icon"></i>
        </div>

        <!-- Expired Code -->
        <div class="error-code animate__animated animate__fadeInDown">@lang('kazitds::kazitds.EXPIRED')</div>

        <!-- Error message -->
        <p class="error-message animate__animated animate__fadeInUp mb-4">
            @lang('kazitds::kazitds.Your software access has expired.')
        </p>

        <p class="support-message animate__animated animate__fadeInUp">
            @lang('kazitds::kazitds.Please contact support to renew your service.')
        </p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Particles.js library -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        /* Particles.js config */
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#1d4ed8" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5, "random": true },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#1d4ed8", "opacity": 0.4, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": true, "straight": false, "out_mode": "out" }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" } },
                "modes": { "repulse": { "distance": 100 }, "push": { "particles_nb": 4 } }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>
