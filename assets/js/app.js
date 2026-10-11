    const searchToggle = document.getElementById('searchToggle');
    const searchPanel = document.getElementById('searchPanel');
    const searchInput = document.getElementById('searchInput');

    function openSearch() {
        searchPanel.classList.add('is-open');
        searchToggle.classList.add('is-open');

        searchToggle.setAttribute(
            'aria-expanded',
            'true'
        );

        setTimeout(() => {
            searchInput.focus();
        }, 200);
    }

    function closeSearch() {
        searchPanel.classList.remove('is-open');
        searchToggle.classList.remove('is-open');

        searchToggle.setAttribute(
            'aria-expanded',
            'false'
        );
    }

    searchToggle.addEventListener('click', () => {

        const isOpen =
            searchPanel.classList.contains('is-open');

        if (isOpen) {
            closeSearch();
        } else {
            openSearch();
        }

    });

    document.addEventListener('keydown', (event) => {

        if (event.key === 'Escape') {
            closeSearch();
        }

    });