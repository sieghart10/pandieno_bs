function resetAndSubmit(selectedFilter) {
    // Get the form
    const form = document.getElementById('filter-form');

    // Reset all filters except the selected one
    form.querySelectorAll('select').forEach(select => {
        if (select.name !== selectedFilter) {
            select.value = ''; // Reset to default
        }
    });

    // Submit the form
    form.submit();
}
