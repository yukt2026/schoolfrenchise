/**
 * TECH ERA COMPUTER - Custom Client Script
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Dynamic AJAX Districts Loader for State Selects
    document.querySelectorAll('.dce-state-select').forEach(function (stateSelect) {
        const targetDistrictId = stateSelect.dataset.districtTarget || 'district_id';
        const districtSelect = document.getElementById(targetDistrictId);

        function updateDistricts(stateId, selectedDistrictId = '') {
            if (!districtSelect) return;

            const defaultPlaceholder = districtSelect.getAttribute('data-placeholder') || 'Select District';

            if (!stateId) {
                districtSelect.innerHTML = `<option value="">${defaultPlaceholder}</option>`;
                districtSelect.disabled = false;
                return;
            }

            districtSelect.innerHTML = '<option value="">Loading districts...</option>';
            districtSelect.disabled = true;

            fetch('/api/districts/' + stateId)
                .then(response => response.json())
                .then(data => {
                    districtSelect.innerHTML = `<option value="">${defaultPlaceholder}</option>`;
                    if (data && data.length > 0) {
                        data.forEach(function (dist) {
                            const option = document.createElement('option');
                            option.value = dist.id;
                            option.textContent = dist.name;
                            if (selectedDistrictId && selectedDistrictId == dist.id) {
                                option.selected = true;
                            }
                            districtSelect.appendChild(option);
                        });
                    } else {
                        districtSelect.innerHTML = '<option value="">No districts found</option>';
                    }
                    districtSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Error fetching districts:', err);
                    districtSelect.innerHTML = `<option value="">${defaultPlaceholder}</option>`;
                    districtSelect.disabled = false;
                });
        }

        stateSelect.addEventListener('change', function () {
            updateDistricts(this.value);
        });

        // If state is already selected on load and district select has no options yet
        if (stateSelect.value && districtSelect && districtSelect.options.length <= 1) {
            const initialDistId = districtSelect.getAttribute('data-selected-district') || '';
            updateDistricts(stateSelect.value, initialDistId);
        }
    });

    // 2. Animated Counter for Homepage Metrics
    const counters = document.querySelectorAll('.dce-counter');
    if (counters.length > 0) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target') || '0', 10);
                    let count = 0;
                    const speed = 2000 / (target || 1);
                    const updateCount = () => {
                        const increment = Math.ceil(target / 40);
                        count += increment;
                        if (count < target) {
                            el.innerText = count.toLocaleString();
                            setTimeout(updateCount, 40);
                        } else {
                            el.innerText = target.toLocaleString();
                        }
                    };
                    updateCount();
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    }
});
