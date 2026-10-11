    <!-- NAV -->
<nav class="main-nav" id="mainNav" aria-label="Secciones">
    <div class="container nav-inner">

        <a href="/justicia/">Justicia</a>
        <a href="/profesion/">Profesión</a>
        <a href="/colegiatura/">Colegiatura</a>
        <a href="/universidades/">Universidades</a>
        <a href="/#agenda">Agenda</a>
        <a href="/#opinion">Opinión</a>

        <button
            class="search-toggle"
            id="searchToggle"
            type="button"
            aria-label="Buscar en Info Jurídica"
            aria-expanded="false"
        >
            <span class="search-icon" aria-hidden="true"></span>
        </button>

    </div>
</nav>

<div class="search-panel" id="searchPanel">

    <div class="container">

        <form
            class="search-form"
            action="/buscar/"
            method="get"
        >

            <div class="search-field">

                <span class="search-label">
                    BUSCAR EN INFO JURÍDICA
                </span>

                <input
                    id="searchInput"
                    type="search"
                    name="q"
                    placeholder="Noticias, colegios, universidades..."
                    autocomplete="off"
                >

            </div>

            <button
                class="search-submit"
                type="submit"
            >
                Buscar
            </button>

        </form>

    </div>

</div>
