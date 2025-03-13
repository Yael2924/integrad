@extends('layouts.base')

@section('inicio')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

@guest

<!-- Sección de Bienvenida con Fade-in -->
<section class="hero fade-in">
    <div class="hero-content">
        <h1 class="bienvenida">¡Bienvenido a Moshan Premium Barber Shop!</h1>
        <br><br><br><br>
        <a href="#agenda-cita" class="btn-custom"><i class="bi bi-calendar"></i>  AGENDAR CITA</a>
    </div>
</section>


<!-- Logo en tarjeta con Fade-in -->
<section class="logo-card fade-in section-bg-light">
    <div class="card">
        <img src="{{ asset('img/logo_barberia_sin_fondo.png') }}" alt="Logotipo de la Barbería" class="card-img-top logo-img">
    </div>
</section>

<!-- Galería con efectos de Fade-in -->
<section id="medio" class="shopify-section shopify-section--rich-text section-bg-dark">
    <div class="prose text-center fade-in">
        <h1 class="titulo-blanco">Galería</h1>

        <!-- Separador personalizado -->
        <div class="separator"></div>
        
    </div>
    <div class="gallery-container">
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar6.jpg') }}" alt="Imagen 1">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar7.jpg') }}" alt="Imagen 2">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar3.jpg') }}" alt="Imagen 3">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar8.jpg') }}" alt="Imagen 4">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar9.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar10.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar11.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar12.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar13.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/afuera.jpg') }}" alt="Imagen 5">
        </div>
    </div>
</section>

<!-- ¿Quiénes Somos? con Slide-in -->
<div id="quienes-somos" class="row justify-content-center align-items-center text-center my-5 fade-in section-bg-light">
    <div class="col-md-5 ps-md-5">
        <h1 class="titulo-negro">¿QUIÉNES SOMOS?</h1>

        <!-- Separador personalizado -->
        <div class="separator-negro"></div>
        <br>
        <p class="contenido-negro">En <strong>Moshan Premium Barber Shop</strong>, redefinimos el concepto de la barbería tradicional con un enfoque premium. Desde 2017, 
            nos hemos dedicado a ofrecer un servicio exclusivo para quienes buscan más que un simple corte: una experiencia 
            de lujo, estilo y cuidado personal.
        </p>
        <p class="contenido-negro"> 
            Cada detalle en <strong>Moshan</strong> está pensado para brindarte una atención de primer nivel. Contamos con barberos expertos 
            que combinan técnicas clásicas y modernas, utilizando solo productos de alta calidad para garantizar un resultado 
            impecable. Nuestro ambiente sofisticado y acogedor está diseñado para que disfrutes de un momento de relajación 
            mientras realzamos tu imagen.
        </p>
    </div>
    <div class="col-md-6">
        <img src="{{ asset('img/bar2.jpg') }}" alt="Barbero" class="img-fluid">
    </div>
</div>

<!-- Mapa de Ubicación con Fade-in -->
<section id="ubicacion" class="row justify-content-center my-5 fade-in section-bg-dark">
    <div class="col-12 col-md-5 mx-auto" id="map" style="height: 400px;"><br><br></div>
    <div class="col-12 col-md-6 align-self-center" id="contacto">
        <h1 class="titulo-blanco text-center">CONTÁCTANOS</h1>
        
        <!-- Separador personalizado -->
        <div class="separator"></div>

        <br>
        <p class="contenido-blanco text-center"><i class="bi bi-telephone"></i> Teléfono: +52 1 919 146 6815</p>
        <p class="contenido-blanco text-center"><i class="bi bi-envelope"></i> Email: contacto@moshanbarber.com</p>
        <p class="contenido-blanco text-center" style="cursor: pointer;" onclick="window.open('https://wa.me/5219191466815', '_blank');"><i class="bi bi-whatsapp"></i> WhatsApp: +52 919 146 6815</p>
        <p class="contenido-blanco text-center"><i class="bi bi-geo-alt"></i> Dirección: Av. Primera Nte. Ote. 73-49, Norte,</p>
        <p class="contenido-blanco text-center"> 29950 Ocosingo, Chis.</p>
        <div class="contenido-blanco text-center">
            <br><br>
            <button onclick="abrirGoogleMaps()" class="btn-custom"><i class="bi bi-geo-alt"></i> CÓMO LLEGAR</button>
        </div>
    </div>
</section>

<!-- Agendar Cita con Slide-in -->
<div id="agenda-cita" class="row justify-content-center align-items-center text-center my-5 fade-in section-bg-light">
    <div class="col-md-5 ps-md-5">
        <h1 class="titulo-negro">Agenda tu cita</h1>

        <!-- Separador personalizado -->
        <div class="separator-negro"></div>
        <br>
        <p class="contenido-negro">Para agendar una cita con nosotros es muy fácil,
            solo descarga nuestra aplicación móvil, crea una cuenta o inicia sesión si ya cuentas con una,
            y disfruta de los beneficios de <strong>Moshan Premium Barber Shop.</strong>
        </p>
        <br>
        <a href="" class="btn-custom"><i class="bi bi-download"></i> DESCARGA LA APLICACIÓN AQUI</a>
        <br><br>
    </div>
    <div class="col-md-6">
        <img src="{{ asset('img/app_movil.png') }}" alt="Barbero" class="img-fluid app-movil-img">
    </div>
</div>

<!-- CSS para el separador -->
<style>
    .separator {
        width: 100px;  /* Ancho de la línea */
        height: 2px;  /* Grosor de la línea */
        background-color: white;  /* Color de la línea */
        margin: 10px auto;  /* Centrar horizontalmente */
    }

    .separator-negro {
        width: 100px;  /* Ancho de la línea */
        height: 2px;  /* Grosor de la línea */
        background-color: black;  /* Color de la línea */
        margin: 10px auto;  /* Centrar horizontalmente */
    }
</style>



<style>
    @media (max-width: 768px) {
        #map {
            height: 300px; /* Reduce el tamaño del mapa en pantallas más pequeñas */
        }

        .contenido-blanco {
            font-size: 1rem; /* Ajusta el tamaño del texto en móviles */
        }

        .btn-custom {
            font-size: 0.9rem; /* Ajusta el tamaño del botón */
        }
    }
</style>



<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyAR2feL-okG5-0FXOXoyvkXAWRQYrr4yKs&loading=async&callback=initMap" async defer></script>

<script>
    function initMap() {
        const location = { lat: 16.909292, lng: -92.094195 };
        const map = new google.maps.Map(document.getElementById('map'), {
            center: location,
            zoom: 14,
            mapTypeId: 'roadmap'
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map,
            title: "Estamos aquí"
        });

        const mapTypeControlDiv = document.createElement("div");
        mapTypeControlDiv.innerHTML = ` 
            <button onclick="setMapType('roadmap')" class="btn btn-sm btn-secondary">🛣 Normal</button>
            <button onclick="setMapType('satellite')" class="btn btn-sm btn-secondary">🛰 Satélite</button>
        `;
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(mapTypeControlDiv);

        window.setMapType = function (type) {
            map.setMapTypeId(type);
        }
    }

    function abrirGoogleMaps() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                let lat = position.coords.latitude;
                let lng = position.coords.longitude;
                window.open(`https://www.google.com/maps/dir/?api=1&origin=${lat},${lng}&destination=16.909292,-92.094195`, '_blank');
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
    const fadeElements = document.querySelectorAll('.fade-in');
    const slideElements = document.querySelectorAll('.slide-in');
    const galleryCards = document.querySelectorAll('.gallery-card');

    // Configuración del Intersection Observer
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            } else {
                entry.target.classList.remove('visible');
            }
        });
    }, { threshold: 0.5 });

    // Observar los elementos con las clases deseadas
    fadeElements.forEach(el => observer.observe(el));
    slideElements.forEach(el => observer.observe(el));
    galleryCards.forEach(el => observer.observe(el));
});
</script>

@endguest

@endsection
