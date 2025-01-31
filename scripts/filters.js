function resetAndSubmit(selectedFilter) {
    const form = document.getElementById('filter-form');

    form.querySelectorAll('select').forEach(select => {
        if (select.name !== selectedFilter) {
            select.value = ''; // Reset to default
        }
    });

    form.submit();
}
