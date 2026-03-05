<x-app-layout>
    <x-slot name="content">
        <article>
            <x-dashboard-statistics title="Total de siniestros" value="100" />
            <x-dashboard-statistics title="Siniestros activos" value="100" />
            <x-dashboard-statistics title="Siniestros cerrados" value="100" />
            <x-dashboard-statistics title="Siniestros pendientes" value="100" />
        </article>

        <article>
            <x-sinister-card
                image="https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85"
                alt="Coche siniestrado" folio="123456789" vehicle="Toyota Corolla" status="Activo" url="#" />

            <x-sinister-card
                image="https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85"
                alt="Coche siniestrado" folio="123456789" vehicle="Toyota Corolla" status="Activo" url="#" />

            <x-sinister-card
                image="https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85"
                alt="Coche siniestrado" folio="123456789" vehicle="Toyota Corolla" status="Activo" url="#" />

            <x-sinister-card
                image="https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85"
                alt="Coche siniestrado" folio="123456789" vehicle="Toyota Corolla" status="Activo" url="#" />
        </article>
    </x-slot>
</x-app-layout>