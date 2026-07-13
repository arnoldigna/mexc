// ==UserScript==
// @name         MEXC Ultra Light
// @namespace    Mexc
// @version      1.2
// @description  Minimal & safe text replacer
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
        { match: '-1.0',  replaceWith: '+1.0',  color: 'black' },
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
                        break; // only one replacement per text node
                    }
                }
            }
        } catch (e) {
            console.error('MEXC script error:', e);
        } finally {
            running = false;
        }
    }

    // Run once after page loads
    setTimeout(replaceTextNodes, 1200);

    // Very slow and light observer
    const observer = new MutationObserver(() => {
        // Only run max once every 2 seconds
        if (!window.mexcLastRun || Date.now() - window.mexcLastRun > 2000) {
            window.mexcLastRun = Date.now();
            setTimeout(replaceTextNodes, 500);
        }
    });

    observer.observe(document.body, { 
        childList: true, 
        subtree: true 
    });

    // Also run every 4 seconds as backup (for very dynamic sites)
    setInterval(() => {
        if (Date.now() - (window.mexcLastRun || 0) > 3000) {
            replaceTextNodes();
        }
    }, 4000);
})();
