<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Bienvenido</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
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
        .carousel { margin-top: 2rem; position: relative; }
        .carousel-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem; }
        .carousel-title { font-weight: 700; color: #374151; }
        .carousel-track { display:flex; gap:1rem; overflow-x:auto; scroll-snap-type:x mandatory; padding-bottom:.5rem; }
        .carousel-track::-webkit-scrollbar { height: 8px; }
        .carousel-track::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 6px; }
        .release-card { scroll-snap-align:start; flex: 0 0 280px; border:1px solid var(--border); border-radius: 14px; background:#fff; overflow:hidden; }
        .release-card-img { height:160px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; }
        .release-card-img img { width:100%; height:100%; object-fit:cover; display:block; }
        .release-card-body { padding: .75rem; }
        .release-meta { font-size:.85rem; color:#6b7280; }
        .badge { display:inline-block; padding:.25rem .5rem; border-radius: 999px; font-size:.75rem; }
        .badge-success { background: #28a745; color:#fff; }
        .badge-info { background: var(--primary); color:#fff; }
        .release-card.empty { border:1px dashed var(--border); }
        .release-card.empty .release-card-img { background:#fafafa; }
        .release-card.empty .release-card-body { text-align:center; color:#6b7280; }
        .carousel-arrow { position:absolute; top:50%; transform:translateY(-50%); width:42px; height:42px; border-radius:999px; border:1px solid var(--border); background:#fff; color:var(--primary); display:flex; align-items:center; justify-content:center; box-shadow:0 6px 18px rgba(17,24,39,0.08); cursor:pointer; }
        .carousel-arrow.left { left:-10px; }
        .carousel-arrow.right { right:-10px; }
        .carousel-arrow.disabled { opacity:.45; pointer-events:none; }
        @media (max-width: 940px) { .content { grid-template-columns: 1fr; } .hero-visual { order: -1; } }
    </style>
    
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
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-sign-in-alt"></i> Ingresar</a>
                    <a href="{{ route('reports.create') }}" class="btn btn-warning btn-sm btn-pulse"><i class="fas fa-bolt"></i> Registro rápido</a>
                </div>
            </div>

            <div class="content">
                <div class="copy animate__animated animate__fadeInUp">
                    <h1 class="title">Rescate y bienestar de la fauna</h1>
                    <p class="subtitle">Conecta hallazgos, traslados, evaluaciones y liberaciones en un solo lugar. Tu participación ayuda a devolver a cada animal a su hábitat.</p>
                    <div class="actions">
                        <a href="{{ route('login') }}" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Ingresar</a>
                        <a href="{{ route('reports.create') }}" class="btn btn-warning btn-pulse"><i class="fas fa-bolt"></i> Registro rápido</a>
                    </div>

                    <div class="features">
                        <div class="card card-outline card-primary">
                            <div class="card-body">
                                <strong><i class="fas fa-map-marked-alt"></i> Hallazgos geolocalizados</strong>
                                <div class="text-muted">Registra ubicaciones y sigue el proceso de rescate.</div>
                            </div>
                        </div>
                        <div class="card card-outline card-primary">
                            <div class="card-body">
                                <strong><i class="fas fa-user-md"></i> Atención integral</strong>
                                <div class="text-muted">Evaluaciones médicas, cuidados y alimentación.</div>
                            </div>
                        </div>
                        <div class="card card-outline card-primary">
                            <div class="card-body">
                                <strong><i class="fas fa-dove"></i> Liberaciones</strong>
                                <div class="text-muted">Coordina y documenta devoluciones al hábitat.</div>
                            </div>
                        </div>
                        <div class="card card-outline card-primary">
                            <div class="card-body">
                                <strong><i class="fas fa-shield-alt"></i> Comunidad segura</strong>
                                <div class="text-muted">Accesos por rol y revisión de solicitudes.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-visual animate__animated animate__fadeIn">
                    <div class="card card-outline card-primary h-100">
                        <div class="card-body d-flex align-items-center justify-content-center text-center h-100">
                            <div>
                                <div style="font-size: 56px; line-height: 1;">🐾</div>
                                <div class="text-muted" style="margin-top:.5rem;">Cuidemos la vida silvestre</div>
                            </div>
                        </div>
                    </div>
                    <div class="blob one"></div>
                    <div class="blob two"></div>
                    <div class="blob three"></div>
                </div>
            </div>

            <div class="carousel">
                <div class="carousel-header">
                    <div class="carousel-title">Animales liberados</div>
                </div>
                <button type="button" class="carousel-arrow left disabled" id="relArrowPrev" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
                <button type="button" class="carousel-arrow right" id="relArrowNext" aria-label="Siguiente"><i class="fas fa-chevron-right"></i></button>
                <div class="carousel-track" id="relTrack">
                    @php($slots = 8)
                    @if(isset($recentReleases) && $recentReleases->count() > 0)
                        @foreach($recentReleases as $rel)
                            @php($af = $rel->animalFile)
                            <div class="release-card card card-outline card-success">
                                <div class="release-card-img card-img-top">
                                    @if(!empty($af?->imagen_url))
                                        <img src="{{ asset('storage/' . $af->imagen_url) }}" alt="animal">
                                    @else
                                        <i class="fas fa-paw" style="font-size: 28px; color:#9ca3af"></i>
                                    @endif
                                </div>
                                <div class="release-card-body card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="font-weight-bold">{{ $af?->animal?->nombre ?? $af?->species?->nombre ?? 'Animal' }}</div>
                                        <span class="badge badge-success">Liberado</span>
                                    </div>
                                    <div class="release-meta mt-1">
                                        <i class="fas fa-calendar-alt"></i> {{ optional($rel->created_at)->format('d/m/Y') }}
                                    </div>
                                    @if(!empty($rel->direccion))
                                    <div class="release-meta"><i class="fas fa-map-marker-alt"></i> {{ $rel->direccion }}</div>
                                    @endif
                                    @if(!empty($rel->detalle))
                                    <div class="release-meta"><i class="fas fa-info-circle"></i> {{ $rel->detalle }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @for($i = $recentReleases->count(); $i < $slots; $i++)
                            <div class="release-card empty card card-outline card-warning">
                                <div class="release-card-img card-img-top">
                                    <i class="fas fa-dove" style="font-size: 28px; color:#9ca3af"></i>
                                </div>
                                <div class="release-card-body card-body">
                                    <div class="font-weight-bold">Sin liberaciones</div>
                                    <div class="release-meta">Espacio disponible</div>
                                </div>
                            </div>
                        @endfor
                    @else
                        @for($i = 0; $i < $slots; $i++)
                            <div class="release-card empty card card-outline card-warning">
                                <div class="release-card-img card-img-top">
                                    <i class="fas fa-dove" style="font-size: 28px; color:#9ca3af"></i>
                                </div>
                                <div class="release-card-body card-body">
                                    <div class="font-weight-bold">Aún no hay animales liberados</div>
                                    <div class="release-meta">Pronto verás liberaciones aquí</div>
                                </div>
                            </div>
                        @endfor
                    @endif
                </div>
            </div>

            <div class="footer text-muted">
                © {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>
    </section>
    <script>
    (function(){
        var track = document.getElementById('relTrack');
        var prev = document.getElementById('relArrowPrev');
        var next = document.getElementById('relArrowNext');
        function updateArrows(){
            if (!track) return;
            var maxScroll = track.scrollWidth - track.clientWidth;
            var atStart = track.scrollLeft <= 0;
            var atEnd = track.scrollLeft >= maxScroll - 1;
            if (prev) prev.classList.toggle('disabled', atStart);
            if (next) next.classList.toggle('disabled', atEnd);
        }
        if (track && prev && next) {
            var step = 320;
            prev.addEventListener('click', function(){ track.scrollBy({ left: -step, behavior: 'smooth' }); });
            next.addEventListener('click', function(){ track.scrollBy({ left: step, behavior: 'smooth' }); });
            track.addEventListener('scroll', updateArrows);
            window.addEventListener('resize', updateArrows);
            updateArrows();
        }
    })();
    </script>
</body>
</html>
