/**
 * dashboard.js - SmartRT Pro Dashboard Logic
 * Handles data fetching, charts rendering, and animated counters.
 */

let dashboardBooted = false;

function bootDashboard() {
    if (dashboardBooted) return;
    dashboardBooted = true;

    let activePage = 'dashboard';
    try {
        activePage = localStorage.getItem('activePage') || 'dashboard';
    } catch (e) {
        activePage = 'dashboard';
    }
    if (activePage === 'dashboard') {
        initDashboard();
    }
}

bootDashboard();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootDashboard, { once: true });
}

// Override showPage to init dashboard when selected
const originalShowPage = window.showPage;
window.showPage = function(pageId) {
    originalShowPage(pageId);
    const dashboardVisible = (() => {
        const el = document.getElementById('page-dashboard');
        return !!el && !el.classList.contains('hidden') && el.style.display !== 'none';
    })();
    if (pageId === 'dashboard' || dashboardVisible) {
        initDashboard();
    }
};

let iuranChart = null;
let demografiChart = null;

async function initDashboard() {
    console.log('Initializing Dashboard...');
    
    // 1. Show Loading State (Optional: use skeletons)
    const dashboardEntity = document.getElementById('page-dashboard');
    if (!dashboardEntity) return;

    try {
        // Fetch global data by default (blok_id=0)
        const response = await fetch('api/get_dashboard_summary.php?blok_id=0'); 
        const result = await response.json();

        if (result.status === 'success') {
            updateDashboardUI(result.data);
        } else {
            console.error('API Error:', result.message);
        }
    } catch (error) {
        console.error('Fetch Error:', error);
    }
}

function updateDashboardUI(data) {
    // 1. Update KPI Values with Animation
    animateValue('dash-saldo', 0, data.kas_blok, 1500, 'currency');
    
    // Partisipasi Iuran calculation
    const totalWarga = data.total_warga || 1; // prevent div by zero
    // In a real app, you'd get "Lunas" count. For now, let's derive from demografi or mock
    const lunasCount = Math.floor(totalWarga * 0.85); 
    const percent = Math.round((lunasCount / totalWarga) * 100);
    
    document.getElementById('dash-iuran-percent').innerText = `${percent}%`;
    document.getElementById('dash-iuran-progress').style.width = `${percent}%`;
    document.getElementById('dash-iuran-detail').innerText = `${lunasCount}/${totalWarga} Warga`;
    
    document.getElementById('dash-laporan-count').innerText = data.laporan_aktif;
    document.getElementById('dash-agenda-text').innerText = data.agenda_terdekat || 'Tidak ada agenda terdekat';

    // 2. Render Charts
    renderIuranTrendChart(data.iuran_labels, data.iuran_data);
    renderDemografiChart(data.demografi);

    // 3. Trigger Animations
    const dashboardPage = document.getElementById('page-dashboard');
    dashboardPage.classList.remove('stagger-ready');
    void dashboardPage.offsetWidth;
    dashboardPage.classList.add('stagger-ready');
}

function animateValue(id, start, end, duration, type = 'number') {
    const obj = document.getElementById(id);
    if (!obj) return;
    
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        
        if (type === 'currency') {
            obj.innerHTML = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        } else {
            obj.innerHTML = value;
        }
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

function renderIuranTrendChart(labels, data) {
    const ctx = document.getElementById('iuranTrendChart').getContext('2d');
    if (iuranChart) iuranChart.destroy();

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    iuranChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Iuran',
                data: data,
                borderColor: '#10b981',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(255,255,255,0.05)' },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10 },
                        callback: (value) => 'Rp ' + (value / 1000) + 'k'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 10 } }
                }
            }
        }
    });
}

function renderDemografiChart(demografi) {
    const ctx = document.getElementById('demografiChart').getContext('2d');
    if (demografiChart) demografiChart.destroy();

    const labels = Object.keys(demografi);
    const values = Object.values(demografi);

    demografiChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
}
