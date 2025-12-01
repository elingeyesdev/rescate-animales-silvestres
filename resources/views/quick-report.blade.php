<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro Rápido - Rescate Animales</title>
    <link rel="icon" type="image/png" href="{{ asset('Fotos/Patota.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('Fotos/Patota.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-container { height: 600px; border-radius: 10px; border: 1px solid #dee2e6; }
        .emergency-header { background: linear-gradient(135deg, #dc3545, #c82333); color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .ci-field { display: none; }
        .content-wrapper { margin-left: 0 !important; }
        .main-header { margin-left: 0 !important; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h1 class="m-0">Registro Rápido de Emergencia</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="emergency-header text-center">
                            <h3>Reporte de Animales en Riesgo</h3>
                            <p class="mb-0">Complete el formulario para reportar animales en situación de emergencia</p>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header"><h3 class="card-title">Ubicación del Reporte</h3></div>
                                    <div class="card-body p-0 d-flex flex-column">
                                        <div id="map" class="map-container"></div>
                                        <div class="p-3 border-top">
                                            <div class="alert alert-info mb-0" role="alert">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Haga clic en el mapa para marcar la ubicación exacta del reporte.
                                                Puede arrastrar el marcador, hacer zoom y usar su ubicación actual.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header"><h3 class="card-title">Información del Reporte</h3></div>
                                    <div class="card-body">
                                        <form id="reporteForm" action="{{ route('reporte-rapido.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <label>Tipo de Emergencia</label>
                                                <div class="icheck-danger">
                                                    <input type="radio" id="incendio" name="tipo_emergencia" value="incendio" checked>
                                                    <label for="incendio">Animales en Incendio</label>
                                                </div>
                                                <div class="icheck-warning">
                                                    <input type="radio" id="otro" name="tipo_emergencia" value="otro">
                                                    <label for="otro">Otra Emergencia</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Descripción</label>
                                                <textarea name="descripcion" class="form-control" rows="4" placeholder="Describa la situación y el tipo de animales"></textarea>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Nombre</label>
                                                    <input type="text" name="nombre" class="form-control" placeholder="Opcional">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Teléfono</label>
                                                    <input type="tel" name="telefono" class="form-control" placeholder="Opcional">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Fotografía</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/jpeg,image/jpg,image/png">
                                                    <label class="custom-file-label" for="foto">Seleccionar archivo</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Latitud</label>
                                                    <input type="text" name="lat" class="form-control" placeholder="-" readonly>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Longitud</label>
                                                    <input type="text" name="lng" class="form-control" placeholder="-" readonly>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-outline-info"><i class="fas fa-location-arrow"></i> Usar mi ubicación</button>
                                                <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane"></i> Enviar Reporte</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
