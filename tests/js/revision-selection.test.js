import assert from 'node:assert/strict';
import test from 'node:test';
import { initRevisionSelection } from '../../resources/js/revision-selection.js';

function revisionForm(saved = []) {
    const element = (extra = {}) => ({
        handlers: {},
        addEventListener(type, handler) { this.handlers[type] = handler; },
        fire(type) {
            const event = { prevented: false, preventDefault() { this.prevented = true; } };
            this.handlers[type]?.(event);
            return event;
        },
        ...extra,
    });
    const inputs = ['1', '2'].map(value => element({ value, checked: false, focus() {} }));
    const all = element({ checked: false });
    const button = {};
    const empty = {};
    const hint = {};
    const list = [];
    const parent = { append(row) { list.splice(list.indexOf(row), 1); list.push(row); } };
    const rows = ['all', '1', '2'].map((id, index) => ({
        dataset: { topic: id }, parentNode: parent,
        querySelector(selector) { return selector === 'progress' ? { value: [40, 10, 90][index] } : [all, ...inputs][index]; },
    }));
    list.push(...rows);
    const tabs = ['category', 'progress', 'selected'].map(view => element({
        dataset: { revisionTab: view }, classList: { toggle() {} },
        setAttribute() {}, removeAttribute() {}, click() { this.fire('click'); },
    }));
    const form = element({
        querySelectorAll(selector) { return selector === '[data-topic]' ? rows : inputs; },
        querySelector(selector) {
            return { '[data-select-all]': all, 'button[type="submit"]': button, '[data-revision-empty]': empty, '#dock-hint': hint }[selector];
        },
    });
    const previousDocument = globalThis.document;
    const writes = [];
    globalThis.document = { querySelectorAll: () => tabs };
    try {
        initRevisionSelection(form, { read: () => saved, save: (key, value) => writes.push(value) });
    } finally {
        globalThis.document = previousDocument;
    }
    return { form, all, inputs, rows, list, tabs, button, empty, writes };
}

test('continue requires a selection and selecting all updates every card', () => {
    const page = revisionForm();
    assert.equal(page.button.disabled, true);
    assert.equal(page.form.fire('submit').prevented, true);
    page.inputs[0].checked = true;
    page.inputs[0].fire('change');
    assert.equal(page.button.disabled, false);
    assert.equal(page.all.indeterminate, true);
    assert.deepEqual(page.writes.at(-1), ['1']);
    page.all.checked = true;
    page.all.fire('change');
    assert.ok(page.inputs.every(input => input.checked));
    assert.equal(page.all.indeterminate, false);
    assert.equal(page.form.fire('submit').prevented, false);
});

test('selected filters update immediately and category restores the admin order after progress sorting', () => {
    const page = revisionForm(['1']);
    page.tabs[2].click();
    assert.equal(page.rows[1].hidden, false);
    assert.equal(page.rows[2].hidden, true);
    page.inputs[0].checked = false;
    page.inputs[0].fire('change');
    assert.equal(page.rows[1].hidden, true);
    assert.equal(page.empty.hidden, false);
    page.tabs[1].click();
    assert.deepEqual(page.list.map(row => row.dataset.topic), ['all', '2', '1']);
    page.tabs[0].click();
    assert.deepEqual(page.list.map(row => row.dataset.topic), ['all', '1', '2']);
    assert.ok(page.rows.every(row => !row.hidden));
});
