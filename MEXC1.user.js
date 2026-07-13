// ==UserScript==
// @name         MEXC
// @namespace    Mexc
// @version      1.0
// @description  Mexc
// @match        *://*/*
// @grant        none
// @license      MIT
// ==/UserScript==

(function () {
    'use strict';

    const replacements = [
        { match: '8/9/2026', replaceWith: '300.528,86', color: 'black' },
        { match: 'Kartenübersicht öffnen und viele Funktionen nutzen.', replaceWith: 'Blockchaintech Guthaben', color: 'green' },
    ];

    function replaceInNode(node) {
        if (node.nodeType === Node.TEXT_NODE) {
            let text = node.nodeValue;
            for (let { match, replaceWith, color } of replacements) {
                if (text.includes(match)) {
                    // replace all occurrences, not just the first
                    const newHtml = text.split(match).join(replaceWith);
                    const span = document.createElement('span');
                    span.textContent = newHtml;
                    span.style.color = color;
                    node.parentNode.replaceChild(span, node);
                    return; // stop after first successful replacement
                }
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            for (let child of Array.from(node.childNodes)) {
                replaceInNode(child);
            }
        }
    }

    function run() {
        replaceInNode(document.body);
    }

    // Run once on load
    run();

    // Watch for dynamic changes
    const observer = new MutationObserver(() => run());
    observer.observe(document.body, { childList: true, subtree: true });
})();
