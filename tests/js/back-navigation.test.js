import assert from 'node:assert/strict';
import test from 'node:test';
import { handleBackNavigation } from '../../resources/js/back-navigation.js';

function clickBack({ pathname = '/revision', historyLength = 2, link = {}, ...eventOptions } = {}) {
    let backCalls = 0;
    const browser = {
        location: { pathname },
        history: { length: historyLength, back: () => backCalls++ },
    };
    const event = {
        button: 0,
        defaultPrevented: false,
        target: { closest: () => ({ hasAttribute: () => false, ...link }) },
        preventDefault() { this.defaultPrevented = true; },
        ...eventOptions,
    };

    handleBackNavigation(event, browser);
    return { backCalls, prevented: event.defaultPrevented };
}

test('public back links return to the actual browser history entry', () => {
    assert.deepEqual(clickBack(), { backCalls: 1, prevented: true });
});

test('a directly opened page follows its fallback link', () => {
    assert.deepEqual(clickBack({ historyLength: 1 }), { backCalls: 0, prevented: false });
});

test('admin navigation never uses the public history handler', () => {
    for (const pathname of ['/admin', '/admin/sections/1', '/app/admin/login']) {
        assert.deepEqual(clickBack({ pathname }), { backCalls: 0, prevented: false });
    }
});

test('new tab actions and other page controls keep their normal behavior', () => {
    for (const options of [
        { ctrlKey: true }, { metaKey: true }, { shiftKey: true }, { altKey: true },
        { button: 1 }, { link: { target: '_blank' } },
        { link: { hasAttribute: () => true } },
        { target: { closest: () => null } },
    ]) {
        assert.deepEqual(clickBack(options), { backCalls: 0, prevented: false });
    }
});

test('an existing confirmation handler can cancel navigation', () => {
    assert.deepEqual(clickBack({ defaultPrevented: true }), { backCalls: 0, prevented: true });
});
