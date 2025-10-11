<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

    <a href="{{ route('productor.panel') }}"
       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
       :class="{
        'border-indigo-500 text-gray-900 focus:border-indigo-700': request()->routeIs('productor.panel'),
        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300': !request()->routeIs('productor.panel')
       }">
        Inicio
    </a>

    <a href="{{ route('cuaderno.index') }}"
       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
       :class="{
        'border-indigo-500 text-gray-900 focus:border-indigo-700': request()->routeIs('cuaderno.index') || request()->routeIs('cuaderno.inicio') || request()->routeIs('cuaderno.registro'),
        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300': !(request()->routeIs('cuaderno.index') || request()->routeIs('cuaderno.inicio') || request()->routeIs('cuaderno.registro'))
       }">
        Cuaderno de Campo
    </a>

    <a href="{{ route('productor.unidades-productivas.index') }}"
       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
       :class="{
        'border-indigo-500 text-gray-900 focus:border-indigo-700': request()->routeIs('productor.unidades-productivas.index'),
        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300': !request()->routeIs('productor.unidades-productivas.index')
       }">
        Mis Chacras
    </a>

    <a href="{{ route('productor.stock.index') }}"
       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
       :class="{
        'border-indigo-500 text-gray-900 focus:border-indigo-700': request()->routeIs('productor.stock.index'),
        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300': !request()->routeIs('productor.stock.index')
       }">
        Mi Stock
    </a>

    <a href="{{ route('productor.reportes.index') }}"
       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
       :class="{
        'border-indigo-500 text-gray-900 focus:border-indigo-700': request()->routeIs('productor.reportes.index'),
        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300': !request()->routeIs('productor.reportes.index')
       }">
        Reportes
    </a>

    <a href="{{ route('productor.estadisticas.index') }}"
       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
       :class="{
        'border-indigo-500 text-gray-900 focus:border-indigo-700': request()->routeIs('productor.estadisticas.index'),
        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300': !request()->routeIs('productor.estadisticas.index')
       }">
        Estadísticas
    </a>

</div>