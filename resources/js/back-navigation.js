export function handleBackNavigation(event, browser = window) {
    if (/\/admin(?:\/|$)/.test(browser.location.pathname)
        || event.defaultPrevented
        || event.button !== 0
        || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const link = event.target.closest?.('a[data-back]');
    if (!link || link.hasAttribute('download')
        || (link.target && link.target !== '_self')) {
        return;
    }

    // A new tab has no previous entry, so keep the link's normal fallback URL.
    if (browser.history.length > 1) {
        event.preventDefault();
        browser.history.back();
    }
}
