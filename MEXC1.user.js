// ==UserScript==
// @name         MEXC Light
// @namespace    Mexc
// @version      1.1
// @description  Light text replacer
// @match        *://*/*
// @grant        none
// @license      MIT
// ==/UserScript==

(function () {
    'use strict';

    const replacements = [
        { match: '-4.1',  replaceWith: '+4.1',  color: 'black' },
        { match: '-15.9', replaceWith: '+15.9', color: 'black' },
        { match: '6.49',  replaceWith: '26.49', color: 'black' },
        { match: '5.49',  replaceWith: '26.49', color: 'black' },
        { match: '4.49',  replaceWith: '26.49', color: 'black' },
        { match: '3.49',  replaceWith: '26.49', color: 'black' },
        { match: '2.49',  replaceWith: '26.49', color: 'black' },
        { match: '1.49',  replaceWith: '26.49', color: 'black' },
        { match: '-1.0',  replaceWith: '+1.0',  color: 'black' },
        { match: 'Kartenübersicht öffnen und viele Funktionen nutzen.',
          replaceWith: 'Blockchaintech Guthaben', color: 'green' },
    ];

    let isProcessing = false;
    let timeout = null;

    function replaceInTextNode(node) {
        if (node.nodeType !== Node.TEXT_NODE) return false;
        let text = node.nodeValue;
        if (!text) return false;

        for (let rep of replacements) {
            if (text.includes(rep.match)) {
                const newText = text.split(rep.match).join(rep.replaceWith);
                const span = document.createElement('span');
                span.textContent = newText;
                span.style.color = rep.color;
                // Mark as processed so we don't re-process it
                span.dataset.mexcProcessed = '1';
                node.parentNode.replaceChild(span, node);
                return true;
            }
        }
        return false;
    }

    function walk(node) {
        if (!node || isProcessing) return;

        // Skip already processed nodes and script/style elements
        if (node.nodeType === Node.ELEMENT_NODE) {
            if (node.dataset.mexcProcessed === '1' ||
                node.tagName === 'SCRIPT' ||
                node.tagName === 'STYLE' ||
                node.tagName === 'TEXTAREA' ||
                node.tagName === 'INPUT') {
                return;
            }
        }

        // Process text nodes
        if (node.nodeType === Node.TEXT_NODE) {
            replaceInTextNode(node);
            return;
        }

        // Walk children
        for (let child of Array.from(node.childNodes)) {
            walk(child);
        }
    }

    function debouncedRun() {
        if (timeout) clearTimeout(timeout);
        
        timeout = setTimeout(() => {
            if (isProcessing) return;
            isProcessing = true;
            try {
                walk(document.body);
            } finally {
                isProcessing = false;
            }
        }, 300); // 300ms debounce
    }

    // Initial run
    setTimeout(debouncedRun, 800);

    // Mutation Observer - much lighter
    const observer = new MutationObserver((mutations) => {
        // Only react to added nodes, ignore attribute/text changes if not necessary
        let shouldRun = false;
        for (let mut of mutations) {
            if (mut.addedNodes.length > 0) {
                shouldRun = true;
                break;
            }
        }
        if (shouldRun) debouncedRun();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Optional: Run again when user scrolls (for lazy-loaded content)
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(debouncedRun, 600);
    }, { passive: true });
})();
