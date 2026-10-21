export function initRevisionSelection(form, { read, save }) {
    const rows = [...form.querySelectorAll('[data-topic]')];
    const inputs = [...form.querySelectorAll('input[name="categories[]"], input[name="topics[]"]')];
    const all = form.querySelector('[data-select-all]');
    const button = form.querySelector('button[type="submit"]');
    const empty = form.querySelector('[data-revision-empty]');
    const hint = form.querySelector('#dock-hint');
    const tabs = [...document.querySelectorAll('[data-revision-tab]')];
    const saved = read('rivex.selected', []);
    const selected = Array.isArray(saved) ? saved.map(String) : [];
    let view = 'category';

    const render = () => {
        const checked = inputs.filter(input => input.checked);
        if (all) {
            all.checked = inputs.length > 0 && checked.length === inputs.length;
            all.indeterminate = checked.length > 0 && checked.length < inputs.length;
        }
        if (button) button.disabled = checked.length === 0;
        if (hint) hint.textContent = checked.length
            ? `${checked.length} ${checked.length === 1 ? 'category' : 'categories'} selected to revise`
            : 'Select one or more categories to revise';

        rows.forEach(row => { row.hidden = view === 'selected' && !row.querySelector('input')?.checked; });
        if (empty) empty.hidden = view !== 'selected' || checked.length > 0;
        const ordered = view === 'progress'
            ? [...rows].sort((a, b) => {
                if (a.dataset.topic === 'all') return -1;
                if (b.dataset.topic === 'all') return 1;
                return Number(b.querySelector('progress').value) - Number(a.querySelector('progress').value);
            }) : rows;
        ordered.forEach(row => row.parentNode.append(row));
    };
    const persist = () => {
        save('rivex.selected', inputs.filter(input => input.checked).map(input => input.value));
        render();
    };
    inputs.forEach(input => {
        input.checked = selected.includes(input.value);
        input.addEventListener('change', persist);
    });
    all?.addEventListener('change', () => {
        inputs.forEach(input => { input.checked = all.checked; });
        persist();
    });
    tabs.forEach(tab => tab.addEventListener('click', event => {
        event.preventDefault();
        view = tab.dataset.revisionTab;
        tabs.forEach(item => {
            item.classList.toggle('tab--active', item === tab);
            if (item === tab) item.setAttribute('aria-current', 'page');
            else item.removeAttribute('aria-current');
        });
        render();
    }));
    form.addEventListener('submit', event => {
        if (!inputs.some(input => input.checked)) {
            event.preventDefault();
            tabs.find(tab => tab.dataset.revisionTab === 'category')?.click();
            inputs[0]?.focus();
        }
    });
    render();
}
