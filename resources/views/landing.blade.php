<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Bienvenido</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary: #3c8dbc;
            --info: #17a2b8;
            --success: #28a745;
            --warning: #ffc107;
            --bg-gradient: linear-gradient(180deg, #ffffff 0%, #eaf5fb 100%);
            --card-bg: #ffffff;
            --border: #e5e7eb;
            --text: #1f2937;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body { margin: 0; font-family: 'Source Sans Pro', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--text); background: #f4f6f9; }
        .hero {
            min-height: 100vh;
            background: var(--bg-gradient);
            position: relative;
            overflow: hidden;
        }
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem; }
        .nav {
            display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; gap: 1rem;
        }
        .brand { display: flex; align-items: center; gap: .75rem; font-weight: 700; letter-spacing: .5px; }
        .brand-badge { width: 38px; height: 38px; border-radius: 10px; background: #eaf5fb; border: 1px solid var(--border); display: grid; place-items: center; color: var(--primary); }
        .brand-badge i { font-size: 18px; }
        .cta { display: flex; gap: .75rem; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .85rem 1.1rem; border-radius: 12px; border: 1px solid var(--border); background: #ffffff; color: var(--text); text-decoration: none; transition: transform .2s ease, box-shadow .2s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(17,24,39,0.08); }
        .btn-primary { background: var(--primary); border-color: var(--primary); color: #fff; }
        .btn-primary:hover { background: #357ca5; }
        .btn-accent { background: var(--warning); border-color: var(--warning); color: #1f2937; box-shadow: 0 8px 20px rgba(255,193,7,0.25); }
        .btn-accent:hover { background: #e0a800; box-shadow: 0 8px 28px rgba(255,193,7,0.35); }
        .btn-hero { padding: 1rem 1.25rem; font-weight: 600; }
        @keyframes pulseGlow { 0%{ box-shadow: 0 0 0 0 rgba(255,193,7,0.55);} 70%{ box-shadow: 0 0 0 12px rgba(255,193,7,0);} 100%{ box-shadow: 0 0 0 0 rgba(255,193,7,0);} }
        .btn-pulse { animation: pulseGlow 2.2s infinite; }
        .content { display: grid; grid-template-columns: 1.1fr .9fr; gap: 2rem; align-items: center; padding-top: 1.5rem; }
        .title { font-size: clamp(2rem, 4vw, 3.25rem); line-height: 1.05; margin: 0; }
        .subtitle { font-size: clamp(1rem, 2vw, 1.25rem); color: #4b5563; margin-top: 1rem; }
        .actions { margin-top: 1.75rem; display: flex; gap: .75rem; flex-wrap: wrap; }
        .card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 18px; padding: 1rem; }
        .features { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.25rem; }
        .feature { padding: 1rem; border-radius: 14px; background: #ffffff; border: 1px dashed var(--border); }
        .hero-visual { position: relative; height: 100%; min-height: 320px; }
        .blob { position: absolute; border-radius: 50%; filter: blur(24px); opacity: .25; }
        .blob.one { width: 220px; height: 220px; background: #d7eef7; top: 10%; left: 10%; }
        .blob.two { width: 300px; height: 300px; background: #e6f7ef; bottom: 10%; right: 15%; }
        .blob.three { width: 180px; height: 180px; background: #e3f4f9; bottom: 35%; left: 55%; }
        .footer { margin-top: 3rem; text-align: center; color: #6b7280; font-size: .9rem; }
        @media (max-width: 940px) { .content { grid-template-columns: 1fr; } .hero-visual { order: -1; } }
    </style>
    <script src="https://kit.fontawesome.com/a2e0f6ad5b.js" crossorigin="anonymous"></script>
</head>
<body>
    <section class="hero">
        <div class="container">
            <div class="nav">
                <div class="brand">
                    <div class="brand-badge"><i class="fas fa-paw"></i></div>
                    <div>Rescate Animales</div>
                </div>
                <div class="cta">
                    <a href="{{ route('login') }}" class="btn">Ingresar</a>
                    <a href="{{ route('reporte-rapido') }}" class="btn btn-accent btn-hero btn-pulse"><i class="fas fa-bolt"></i> Reporte rápido</a>
                </div>
            </div>

            <div class="content">
                <div class="copy animate__animated animate__fadeInUp">
                    <h1 class="title">Rescate y bienestar de la fauna</h1>
                    <p class="subtitle">Conecta hallazgos, traslados, evaluaciones y liberaciones en un solo lugar. Tu participación ayuda a devolver a cada animal a su hábitat.</p>
                    <div class="actions">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-hero"><i class="fas fa-sign-in-alt"></i> Ingresar</a>
                        <a href="{{ route('reporte-rapido') }}" class="btn btn-accent btn-hero btn-pulse"><i class="fas fa-bolt"></i> Reporte rápido</a>
                    </div>

                    <div class="features">
                        <div class="feature">
                            <strong><i class="fas fa-map-marked-alt"></i> Hallazgos geolocalizados</strong>
                            <div>Registra ubicaciones y sigue el proceso de rescate.</div>
                        </div>
                        <div class="feature">
                            <strong><i class="fas fa-user-md"></i> Atención integral</strong>
                            <div>Evaluaciones médicas, cuidados y alimentación.</div>
                        </div>
                        <div class="feature">
                            <strong><i class="fas fa-dove"></i> Liberaciones</strong>
                            <div>Coordina y documenta devoluciones al hábitat.</div>
                        </div>
                        <div class="feature">
                            <strong><i class="fas fa-shield-alt"></i> Comunidad segura</strong>
                            <div>Accesos por rol y revisión de solicitudes.</div>
                        </div>
                    </div>
                </div>

                <div class="hero-visual animate__animated animate__fadeIn">
                    <div class="card" style="height: 100%; display: grid; place-items: center;">
                        <div style="text-align:center;">
                            <div style="font-size: 56px; line-height: 1;">🐾</div>
                            <div style="margin-top:.5rem; opacity:.85;">Cuidemos la vida silvestre</div>
                        </div>
                    </div>
                    <div class="blob one"></div>
                    <div class="blob two"></div>
                    <div class="blob three"></div>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>
    </section>
</body>
</html>
