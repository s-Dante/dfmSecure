<nav class="navbar">
    <img src="{{ asset('/logos/DFM_SECURE_LOGO.png') }}" alt="Logo">

    <nav>
        <!-- //Generales -->
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('profile') }}">Perfil</a>
        <a href="{{ route('consultation') }}">Consulta</a>
        <a href="{{ route('sinisterDetail') }}">Detalle de Siniestro</a>

        <!-- //Asegurado -->
        <a href="{{ route('myVehicle') }}">Mis Vehiculos</a>
        <a href="{{ route('editVehicle') }}">Editar Vehiculo</a>
        <a href="{{ route('myPolicies') }}">Mis Polizas</a>

        <!-- //Ajustador -->
        <a href="{{ route('sinisterRegister') }}">Registar Siniestro</a>
        <a href="{{ route('sinisterEdit') }}">Editar Siniestro</a>

        <!-- //Supervisor -->
        <a href="{{ route('search') }}">Buscar</a>

        <!-- //Administrador -->
        <a href="{{ route('manage') }}">Gestionar Usuarios</a>

        <!-- //Cerrar Sesión -->
    </nav>
</nav>