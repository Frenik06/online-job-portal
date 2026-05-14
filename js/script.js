const searchInput = document.querySelector('#searchInput');
const typeFilter = document.querySelector('#typeFilter');
const jobCards = document.querySelectorAll('.job-card');
const applyDialog = document.querySelector('#applyDialog');
const closeDialog = document.querySelector('#closeDialog');
const jobIdField = document.querySelector('#jobIdField');
const modalJobTitle = document.querySelector('#modalJobTitle');

function filterJobs() {
    const searchText = searchInput.value.toLowerCase().trim();
    const selectedType = typeFilter.value;

    jobCards.forEach((card) => {
        const matchesSearch = card.dataset.search.includes(searchText);
        const matchesType = selectedType === 'all' || card.dataset.type === selectedType;
        card.classList.toggle('hidden', !(matchesSearch && matchesType));
    });
}

searchInput.addEventListener('input', filterJobs);
typeFilter.addEventListener('change', filterJobs);

document.querySelectorAll('.apply-button').forEach((button) => {
    button.addEventListener('click', () => {
        jobIdField.value = button.dataset.jobId;
        modalJobTitle.textContent = `Apply for ${button.dataset.jobTitle}`;
        applyDialog.showModal();
    });
});

closeDialog.addEventListener('click', () => {
    applyDialog.close();
});
