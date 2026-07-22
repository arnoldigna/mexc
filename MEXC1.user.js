// ==UserScript==
// @name         MEXC Fast
// @namespace    Mexc
// @version      1.3
// @description  Faster text replacer
// @match        *://*/*
// @grant        none
// @license      MIT
// ==/UserScript==

(function () {
    'use strict';

    const replacements = [
        { match: '268,80',  replaceWith: '26.470,80'},
        { match: '268.80',  replaceWith: '26.470,80'},
        { match: '− 15.9', replaceWith: '+15.9'},
        { match: '− 1.0',  replaceWith: '+1.0'},
        { match: 'Kartenübersicht öffnen und viele Funktionen nutzen.',
          replaceWith: 'Blockchaintech Guthaben', color: 'green' },
    ];

    let running = false;

    function replaceTextNodes() {
        if (running) return;
        running = true;

        try {
            const walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );

            let node;
            while ((node = walker.nextNode())) {
                const parent = node.parentNode;
                if (!parent || parent.dataset?.mexcDone) continue;

                let text = node.nodeValue;
                if (!text) continue;

                for (let r of replacements) {
                    if (text.includes(r.match)) {
                        const span = document.createElement('span');
                        span.textContent = text.split(r.match).join(r.replaceWith);
                        span.style.color = r.color;
                        span.dataset.mexcDone = '1';
                        parent.replaceChild(span, node);
                        break;
                    }
                }
            }
        } catch (e) {}

        running = false;
    }

    // Run as soon as possible
    setTimeout(replaceTextNodes, 300);

    // Faster observer with shorter delay
    const observer = new MutationObserver(() => {
        setTimeout(replaceTextNodes, 80);   // 80ms delay
    });

    observer.observe(document.body, { 
        childList: true, 
        subtree: true 
    });

    // Extra aggressive run for new content
    setInterval(replaceTextNodes, 800);
})();
