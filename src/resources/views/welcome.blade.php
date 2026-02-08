<!DOCTYPE html>
<html lang="es">

<head>
    <!-- ================= SEO BÁSICO ================= -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>
        CronoSENA CATA | Gestión Académica SENA Málaga Santander
    </title>

    <meta name="description"
        content="CronoSENA CATA es la plataforma oficial de planificación académica del Centro Agroempresarial y Turístico de los Andes (SENA Málaga, Santander). Consulta horarios, planificación e información académica." />

    <meta name="author" content="CronoSENA - Xenthrall" />
    <meta name="robots" content="index, follow" />

    <!-- ================= OPEN GRAPH / REDES ================= -->
    <meta property="og:title" content="CronoSENA CATA | Gestión Académica SENA Málaga" />
    <meta property="og:description"
        content="Consulta horarios y gestiona la planificación académica del Centro Agroempresarial y Turístico de los Andes - SENA Málaga, Santander." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://cata.cronosena.site" />
    <meta property="og:image" content="https://cata.cronosena.site/images/logo-cata-removebg.png" />


    <link rel="canonical" href="https://cata.cronosena.site" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <style>
        :root {
            --text-dark: #111827;
            --text-light: #fff;
            --gradient: linear-gradient(83.21deg, #3245ff 0%, #bc52ee 100%);
            --font: Inter, Roboto, 'Helvetica Neue', Arial, sans-serif;
            --pink-light: #ffe9f2;
            /* fondo rosa claro */
            --pink-accent: #d66aa8;
            /* texto / hover */
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            font-family: var(--font);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text-dark);
            position: relative;
            background: #fafafa;
        }

        #background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            filter: blur(40px) saturate(120%);
            object-fit: cover;
            opacity: 1;
            animation: gradientMove 15s ease infinite;
        }

        main {
            text-align: center;
            backdrop-filter: blur(20px);
            padding: 2rem 3rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.55);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.8s ease both;
            border: 1px solid rgba(255, 255, 255, 0.6);
            max-width: 620px;
            width: calc(100% - 2rem);
        }

        h1 {
            font-size: 2rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.25em;
        }

        p {
            color: var(--muted);
            opacity: 0.95;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .header-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 0 8px;
            position: relative;
            flex-direction: column;
            /* logo arriba, crono + button abajo */
        }

        /* Logo */
        .header-logo {
            height: 80px;
            width: auto;
            object-fit: contain;
            filter: var(--logo-filter);
            transition: filter 0.3s ease;
            z-index: 1;
        }

        .header-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 12px;
        }

        .crono-wrapper {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-crono {
            height: 92px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.12));
            transition: filter 0.25s ease, transform 0.15s ease;
            cursor: pointer;
        }

        .header-crono:hover {
            transform: translateY(-4px);
            filter: drop-shadow(0 6px 18px rgba(214, 106, 168, 0.18));
        }

        /* Mensaje de saludo */
        .crono-message {
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            white-space: nowrap;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 3;
        }

        .crono-message.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Botón minimal rosa claro, junto a Crono */
        .button-crono {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--pink-accent);
            background: var(--pink-light);
            border: 1px solid rgba(214, 106, 168, 0.14);
            box-shadow: none;
            transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
        }

        .button-crono:hover {
            transform: translateY(-3px);
            background: #ffd0ea;
            box-shadow: 0 6px 18px rgba(214, 106, 168, 0.08);
        }

        /* Mantener otros botones existentes (Planificación / Instructor) */
        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        a.button {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            color: var(--text-light);
            background: var(--gradient);
            transition: all 0.25s ease;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12),
                inset 0 -2px 0 rgba(0, 0, 0, 0.24);
        }

        a.button:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        #back-button {
            position: absolute;
            left: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.2rem 0.2rem;
            border-radius: 50%;
            text-decoration: none;
            font-weight: 100;
            opacity: 0.6;
            color: var(--text-light);
            background: var(--gradient);
            transition: all 0.25s ease;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12),
                inset 0 -2px 0 rgba(0, 0, 0, 0.24);
            z-index: 2;
        }

        #back-button:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        .button-admin {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            color: var(--text-light);
            background: var(--gradient);
            transition: all 0.25s ease;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12),
                inset 0 -2px 0 rgba(0, 0, 0, 0.24);
            opacity: 0.8;
        }

        .button-admin:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        footer {
            position: absolute;
            bottom: 1rem;
            font-size: 0.85rem;
            color: rgba(0, 0, 0, 0.5);
        }

        @keyframes gradientMove {
            0% {
                transform: scale(1) translate(0, 0);
            }

            50% {
                transform: scale(1.1) translate(-10px, -10px);
            }

            100% {
                transform: scale(1) translate(0, 0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            main {
                padding: 1.2rem;
                width: 94%;
            }

            h1 {
                font-size: 1.5rem;
            }

            .header-container {
                flex-direction: column;
                gap: 10px;
            }

            .header-crono {
                height: 84px;
            }

            .button-crono {
                padding: 0.55rem 0.9rem;
                font-size: 0.92rem;
            }

        }

        @media (max-width: 420px) {
            .header-logo {
                width: 100%;
                max-height: 100px;
            }

            .header-crono {
                height: 68px;
            }

            .button-crono {
                padding: 0.45rem 0.75rem;
                font-size: 0.88rem;
            }

            footer {
                font-size: 0.8rem;
            }
        }

    </style>
</head>

<body>


    <!-- Fondo SVG -->
    <img id="background" src="/images/background.svg" alt="Fondo CronoSENA" />

    <a class="button-admin" href="{{ url('/admin') }}">Administración General</a>

    <main>
        <a href="https://cronosena.site" id="back-button" title="Volver a CronoSENA" aria-label="Volver a CronoSENA">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true">
                <path d="m15 18-6-6 6-6" />
            </svg>
        </a>

        <div class="header-bar">
            <img src="/images/logo-cata-removebg.png" class="header-logo" 
            alt="CronoSENA CATA - Centro Agroempresarial y Turístico de los Andes SENA Málaga">

            <!-- Contenedor con Crono y botón lado a lado -->
            <div class="header-container">
                <div class="crono-wrapper" role="group" aria-label="Crono y consultar horario">
                    <div style="position:relative;">
                        <img src="/images/crono.svg" alt="crono Mascota" class="header-crono" id="crono-logo">
                        <div id="crono-message" class="crono-message">👋 ¡Hola! Soy Crono 🦉</div>
                    </div>

                    <a class="button-crono" href="{{ route('horario.index') }}" title="Consultar horarios">
                        Consultar horarios
                    </a>
                </div>
            </div>
        </div>

        <p>CronoSENA v{{ config('app.version', '1.0.0') }}</p>

        <div class="buttons">
            <a class="button" href="{{ url('/planificacion') }}">
                Planificación Académica
            </a>

            <a class="button" href="{{ url('/instructor') }}">
                Espacio del Instructor
            </a>
        </div>
    </main>

    <footer>© {{ date('Y') }} CronoSENA — Xenthrall</footer>

    <script>
        const cronoLogo = document.getElementById('crono-logo');
        const cronoMessage = document.getElementById('crono-message');
        let timeoutId;

        cronoLogo.addEventListener('click', () => {
            clearTimeout(timeoutId);
            cronoMessage.classList.add('show');

            timeoutId = setTimeout(() => {
                cronoMessage.classList.remove('show');
            }, 2000);
        });
    </script>
</body>

</html>
