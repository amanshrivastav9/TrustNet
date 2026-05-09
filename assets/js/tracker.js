/**
 * TrustNet Tracking SDK - Complete Version
 * Version: 2.0.0
 * Description: Advanced tracking script for website analytics with geolocation
 */

(function() {
    'use strict';
    
    // Configuration
    const config = {
        apiUrl: 'http://localhost/trustnet/api/activity-track.php',
        apiKey: null,
        sessionStart: Date.now(),
        sessionId: null,
        pagesVisited: 1,
        trackClicks: true,
        trackScroll: true,
        trackForms: true,
        trackTime: true,
        heartbeatInterval: 30000, // 30 seconds
        debug: false
    };
    
    // Get API key from script tag
    const scripts = document.getElementsByTagName('script');
    const currentScript = scripts[scripts.length - 1];
    config.apiKey = currentScript.getAttribute('data-api-key');
    config.debug = currentScript.getAttribute('data-debug') === 'true';
    
    if (!config.apiKey) {
        console.error('TrustNet: API key not found. Add data-api-key attribute to script tag.');
        return;
    }
    
    // Generate session ID
    config.sessionId = generateSessionId();
    
    // Log function for debugging
    function log(message) {
        if (config.debug) {
            console.log(`[TrustNet] ${message}`);
        }
    }
    
    // Generate unique session ID
    function generateSessionId() {
        return 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Get device fingerprint (simplified)
    function getFingerprint() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.textBaseline = 'top';
        ctx.font = '14px Arial';
        ctx.fillStyle = '#f60';
        ctx.fillRect(0, 0, 10, 10);
        ctx.fillStyle = '#069';
        ctx.fillText('TrustNet', 2, 15);
        
        return {
            canvas: canvas.toDataURL(),
            userAgent: navigator.userAgent,
            language: navigator.language,
            platform: navigator.platform,
            screenResolution: `${screen.width}x${screen.height}`,
            colorDepth: screen.colorDepth,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };
    }
    
    // Send tracking data to server
    async function sendTrackData(action, data = {}) {
        const payload = {
            api_key: config.apiKey,
            action: action,
            session_id: config.sessionId,
            pages_visited: config.pagesVisited,
            ...data,
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer,
            user_agent: navigator.userAgent,
            viewport: `${window.innerWidth}x${window.innerHeight}`,
            timestamp: new Date().toISOString(),
            fingerprint: getFingerprint()
        };
        
        // Use sendBeacon for page unload events (more reliable)
        if (action === 'session' && navigator.sendBeacon) {
            const blob = new Blob([JSON.stringify(payload)], {type: 'application/json'});
            const sent = navigator.sendBeacon(config.apiUrl, blob);
            log(`Session end sent via beacon: ${sent}`);
        } else {
            try {
                const response = await fetch(config.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                    keepalive: true
                });
                const result = await response.json();
                log(`${action} tracked:`, result);
                return result;
            } catch (error) {
                console.error('TrustNet tracking error:', error);
                // Queue failed requests for retry
                queueFailedRequest(action, payload);
            }
        }
    }
    
    // Queue for failed requests
    let failedQueue = [];
    
    function queueFailedRequest(action, payload) {
        failedQueue.push({ action, payload, timestamp: Date.now() });
        
        // Try to send queued requests when online
        window.addEventListener('online', () => {
            log('Online detected, flushing queue...');
            flushQueue();
        });
    }
    
    async function flushQueue() {
        while (failedQueue.length > 0) {
            const item = failedQueue.shift();
            try {
                await sendTrackData(item.action, item.payload);
                log('Queued request sent successfully');
            } catch (error) {
                console.error('Failed to send queued request:', error);
                // Re-queue if it's less than 5 minutes old
                if (Date.now() - item.timestamp < 300000) {
                    failedQueue.push(item);
                }
            }
        }
    }
    
    // Track page view
    function trackPageView() {
        log('Tracking page view');
        
        // Increment page count for session
        config.pagesVisited++;
        
        sendTrackData('pageview', {
            load_time: window.performance ? 
                window.performance.timing.loadEventEnd - window.performance.timing.navigationStart : 0
        });
    }
    
    // Debounce function for scroll and resize events
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Track click events
    function setupClickTracking() {
        if (!config.trackClicks) return;
        
        document.addEventListener('click', function(e) {
            let target = e.target;
            let clickPath = [];
            let depth = 0;
            const maxDepth = 5;
            
            // Build click path
            while (target && target !== document.body && depth < maxDepth) {
                let selector = target.tagName.toLowerCase();
                if (target.id) selector += `#${target.id}`;
                if (target.className && typeof target.className === 'string') {
                    const classes = target.className.split(' ').filter(c => c).join('.');
                    if (classes) selector += `.${classes}`;
                }
                if (target.getAttribute('data-track')) {
                    selector += `[data-track="${target.getAttribute('data-track')}"]`;
                }
                clickPath.unshift(selector);
                target = target.parentElement;
                depth++;
            }
            
            // Get link URL if clicked on anchor
            let linkUrl = null;
            if (e.target.closest('a')) {
                linkUrl = e.target.closest('a').href;
            }
            
            // Debounce click tracking
            clearTimeout(window.clickTimeout);
            window.clickTimeout = setTimeout(() => {
                sendTrackData('click', {
                    element: clickPath.join(' > '),
                    element_text: (e.target.innerText || e.target.value || '').substring(0, 200),
                    element_id: e.target.id || null,
                    element_class: e.target.className || null,
                    link_url: linkUrl,
                    coordinates: { x: e.pageX, y: e.pageY }
                });
            }, 100);
        });
    }
    
    // Track scroll depth
    function setupScrollTracking() {
        if (!config.trackScroll) return;
        
        let maxScroll = 0;
        let scrollSent = { 25: false, 50: false, 75: false, 90: false, 100: false };
        
        const handleScroll = debounce(() => {
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (window.scrollY / scrollHeight) * 100;
            
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                
                // Send scroll depth events
                for (let depth in scrollSent) {
                    if (scrollPercent >= depth && !scrollSent[depth]) {
                        scrollSent[depth] = true;
                        sendTrackData('scroll', { depth: parseInt(depth) });
                        log(`Scroll depth ${depth}% reached`);
                    }
                }
            }
        }, 500);
        
        window.addEventListener('scroll', handleScroll);
    }
    
    // Track form submissions
    function setupFormTracking() {
        if (!config.trackForms) return;
        
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                const formData = new FormData(e.target);
                const formFields = Array.from(formData.keys());
                
                // Don't track password fields
                const sensitiveFields = formFields.filter(field => 
                    field.toLowerCase().includes('password') || 
                    field.toLowerCase().includes('credit') ||
                    field.toLowerCase().includes('card')
                );
                
                sendTrackData('form_submit', {
                    form_id: e.target.id || 'unnamed',
                    form_name: e.target.name || null,
                    form_action: e.target.action,
                    form_method: e.target.method,
                    field_count: formFields.length,
                    sensitive_fields_count: sensitiveFields.length
                });
                
                log('Form submission tracked');
            }
        });
    }
    
    // Track time on page
    let timeIntervals = [];
    
    function setupTimeTracking() {
        if (!config.trackTime) return;
        
        const intervals = [10, 30, 60, 120, 180, 300, 600]; // seconds
        let pageStartTime = Date.now();
        
        intervals.forEach(interval => {
            const timeout = setTimeout(() => {
                const timeSpent = Math.floor((Date.now() - pageStartTime) / 1000);
                sendTrackData('time_on_page', { seconds: interval, total_time: timeSpent });
                log(`Time on page: ${interval}s reached`);
            }, interval * 1000);
            timeIntervals.push(timeout);
        });
    }
    
    // Heartbeat to keep session alive
    let heartbeatInterval = null;
    
    function startHeartbeat() {
        heartbeatInterval = setInterval(() => {
            const sessionDuration = Math.floor((Date.now() - config.sessionStart) / 1000);
            sendTrackData('heartbeat', { 
                session_duration: sessionDuration,
                pages_visited: config.pagesVisited
            });
            log('Heartbeat sent');
        }, config.heartbeatInterval);
    }
    
    // Track exit intent
    function setupExitIntent() {
        let exitIntentTriggered = false;
        
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY <= 0 && !exitIntentTriggered) {
                exitIntentTriggered = true;
                const sessionDuration = Math.floor((Date.now() - config.sessionStart) / 1000);
                sendTrackData('exit_intent', {
                    session_duration: sessionDuration,
                    pages_visited: config.pagesVisited
                });
                log('Exit intent detected');
            }
        });
    }
    
    // Track page visibility (tab switching)
    function setupVisibilityTracking() {
        let hiddenStart = null;
        
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                hiddenStart = Date.now();
                log('Page hidden');
            } else if (hiddenStart) {
                const hiddenDuration = Math.floor((Date.now() - hiddenStart) / 1000);
                sendTrackData('visibility_change', {
                    hidden: false,
                    hidden_duration: hiddenDuration
                });
                log(`Page visible again after ${hiddenDuration}s`);
                hiddenStart = null;
            }
        });
    }
    
    // Track performance metrics
    function trackPerformance() {
        if (window.performance && window.performance.timing) {
            const timing = window.performance.timing;
            const navigationStart = timing.navigationStart;
            
            const metrics = {
                dom_ready: timing.domContentLoadedEventEnd - navigationStart,
                page_load: timing.loadEventEnd - navigationStart,
                first_byte: timing.responseStart - navigationStart,
                dom_interactive: timing.domInteractive - navigationStart
            };
            
            log('Performance metrics:', metrics);
            
            // Send performance data on page load
            sendTrackData('performance', metrics);
        }
    }
    
    // Track session end
    function setupSessionTracking() {
        window.addEventListener('beforeunload', function() {
            const duration = Math.floor((Date.now() - config.sessionStart) / 1000);
            sendTrackData('session', { 
                duration: duration,
                pages_visited: config.pagesVisited
            });
            log(`Session ended. Duration: ${duration}s, Pages: ${config.pagesVisited}`);
            
            // Clear intervals
            if (heartbeatInterval) clearInterval(heartbeatInterval);
            timeIntervals.forEach(timeout => clearTimeout(timeout));
        });
    }
    
    // Track external links
    function setupExternalLinkTracking() {
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                const isExternal = link.hostname !== window.location.hostname;
                if (isExternal) {
                    sendTrackData('external_link', {
                        link_url: link.href,
                        link_text: (link.innerText || '').substring(0, 100)
                    });
                    log('External link tracked');
                }
            }
        });
    }
    
    // Track downloads
    function setupDownloadTracking() {
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                const downloadExtensions = /\.(pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|exe|dmg|iso|mp3|mp4|avi)$/i;
                if (downloadExtensions.test(link.pathname)) {
                    sendTrackData('download', {
                        file_url: link.href,
                        file_name: link.pathname.split('/').pop(),
                        file_size: null // Could be fetched via HEAD request if needed
                    });
                    log('Download tracked');
                }
            }
        });
    }
    
    // Initialize all trackers
    async function init() {
        log('Initializing TrustNet tracking...', { version: '2.0.0', apiKey: config.apiKey });
        
        // Get device fingerprint and send initial data
        const fingerprint = getFingerprint();
        log('Device fingerprint collected');
        
        // Track initial page view
        trackPageView();
        
        // Setup all event listeners
        setupClickTracking();
        setupScrollTracking();
        setupFormTracking();
        setupTimeTracking();
        setupSessionTracking();
        setupExitIntent();
        setupVisibilityTracking();
        setupExternalLinkTracking();
        setupDownloadTracking();
        
        // Start heartbeat
        startHeartbeat();
        
        // Track performance after page load
        if (document.readyState === 'complete') {
            trackPerformance();
        } else {
            window.addEventListener('load', trackPerformance);
        }
        
        // Track initial session start
        log('TrustNet tracking initialized successfully');
    }
    
    // Start tracking when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose public API for developers
    window.TrustNet = {
        trackEvent: (eventName, eventData) => sendTrackData('custom', { event_name: eventName, event_data: eventData }),
        getSessionId: () => config.sessionId,
        setUserId: (userId) => {
            config.userId = userId;
            sendTrackData('identify', { user_id: userId });
        },
        setUserProperties: (properties) => {
            config.userProperties = properties;
            sendTrackData('identify', { user_properties: properties });
        }
    };
    
    log('Public API exposed as window.TrustNet');
})();