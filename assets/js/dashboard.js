/**
 * TrustNet Dashboard JavaScript
 * Real-time analytics and live updates
 */

class TrustNetDashboard {
    constructor() {
        this.updateInterval = null;
        this.charts = {};
        this.init();
    }
    
    init() {
        this.loadRealTimeStats();
        this.setupAutoRefresh();
        this.setupEventListeners();
    }
    
    loadRealTimeStats() {
        // Load live visitors count
        this.updateLiveVisitors();
        
        // Load recent activity
        this.updateRecentActivity();
        
        // Load chart data
        this.updateCharts();
    }
    
    updateLiveVisitors() {
        fetch('/trustnet/api/get-analytics.php?action=live_visitors')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('liveVisitors').innerText = data.count;
                    document.getElementById('liveVisitors').classList.add('pulse');
                    setTimeout(() => {
                        document.getElementById('liveVisitors').classList.remove('pulse');
                    }, 1000);
                }
            })
            .catch(error => console.error('Error loading live visitors:', error));
    }
    
    updateRecentActivity() {
        fetch('/trustnet/api/get-analytics.php?action=recent_activity')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.logs) {
                    const container = document.getElementById('recentActivity');
                    if (container) {
                        let html = '<div class="activity-timeline">';
                        data.logs.forEach(log => {
                            html += `
                                <div class="timeline-item">
                                    <div class="timeline-time">${log.timestamp}</div>
                                    <div class="timeline-content">
                                        <span class="badge badge-${log.activity_type}">${log.activity_type}</span>
                                        <span class="timeline-details">${log.details}</span>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    }
                }
            })
            .catch(error => console.error('Error loading activity:', error));
    }
    
    updateCharts() {
        // Update login chart
        if (this.charts.loginChart) {
            fetch('/trustnet/api/get-analytics.php?action=login_stats')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        this.charts.loginChart.data.datasets[0].data = data.counts;
                        this.charts.loginChart.update();
                    }
                })
                .catch(error => console.error('Error updating chart:', error));
        }
    }
    
    setupAutoRefresh() {
        // Refresh every 30 seconds
        this.updateInterval = setInterval(() => {
            this.updateLiveVisitors();
            this.updateRecentActivity();
        }, 30000);
    }
    
    setupEventListeners() {
        // Handle website selector change
        const websiteSelector = document.getElementById('websiteSelector');
        if (websiteSelector) {
            websiteSelector.addEventListener('change', (e) => {
                this.loadWebsiteStats(e.target.value);
            });
        }
        
        // Handle export data button
        const exportBtn = document.getElementById('exportData');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportAnalytics();
            });
        }
    }
    
    loadWebsiteStats(websiteId) {
        fetch(`/trustnet/api/get-analytics.php?action=website_stats&website_id=${websiteId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('websiteVisitors').innerText = data.stats.unique_visitors;
                    document.getElementById('websitePageViews').innerText = data.stats.total_visits;
                }
            })
            .catch(error => console.error('Error loading website stats:', error));
    }
    
    exportAnalytics() {
        window.location.href = '/trustnet/api/export-analytics.php';
    }
    
    destroy() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.trustNetDashboard = new TrustNetDashboard();
});

// Utility function to format numbers
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Utility function to format time ago
function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    let interval = Math.floor(seconds / 31536000);
    
    if (interval > 1) return interval + ' years ago';
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + ' months ago';
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + ' days ago';
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + ' hours ago';
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + ' minutes ago';
    return Math.floor(seconds) + ' seconds ago';
}