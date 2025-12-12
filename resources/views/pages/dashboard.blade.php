@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">

        <style>
            /* limit month list height and add scroll */
            .month-list-wrapper {
                max-height: 260px;
                overflow-y: auto;
                padding-right: 6px;
            }

            /* limit legend height */
            #pieLegend {
                max-height: 160px;
                overflow-y: auto;
                text-align: left;
                padding-left: 8px;
            }

            /* fix pie canvas height so it doesn't push layout */
            #monthlyPieChart {
                height: 200px !important;
                max-height: 200px;
            }

            @media (max-width: 768px) {
                #monthlyPieChart {
                    height: 160px !important;
                }

                .month-list-wrapper {
                    max-height: 180px;
                }
            }

            /* compact legend list */
            #pieLegend ul {
                margin: 0;
                padding-left: 18px;
            }

            #pieLegend li {
                margin-bottom: 4px;
                font-size: 0.92rem;
            }
        </style>

        <div class="row">
            <!-- Current Balance (BOB + UCO) -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>₹ {{ number_format($totalCurrentBalance, 2) }}</h3>
                        <p>Total Current Balance (BOB + Cash)</p>

                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">
                            BOB: ₹ {{ number_format($bobCurrentBalance, 2) }} |
                            Opening: ₹ {{ number_format($bobOpeningBalance, 2) }}
                        </p>
                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">
                            Cash: ₹ {{ number_format($cashCurrentBalance, 2) }} |
                            Opening: ₹ {{ number_format($cashOpeningBalance, 2) }}
                        </p>
                        {{-- <p style="font-size: 0.85rem; margin-top:6px; opacity:0.8;">
                            Opening Balance (BOB + Cash): ₹ {{ number_format($bobCashOpeningBalance, 2) }}
                        </p> --}}
                    </div>

                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <a href="{{ route('accounts.show') }}" class="small-box-footer">
                        Accounts <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Expense (Cash + BOB Only) -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>₹ {{ number_format($totalExpense, 2) }}</h3>
                        <p>Total Expense (Cash + BOB)</p>
                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">BOB: ₹
                            {{ number_format($bobExpense, 2) }}</p>
                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">Cash: ₹
                            {{ number_format($cashExpense, 2) }}</p>
                    </div>
                    <div class="icon"><i class="fas fa-credit-card"></i></div>
                    <a href="{{ route('transactions.show') }}" class="small-box-footer">Transactions <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Total Income -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>₹ {{ number_format($totalIncome, 2) }}</h3>
                        <p>Total Income</p>
                        <p style="font-size:0.85rem; color:#e6ffed; margin-top:6px;">
                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">BOB: ₹
                            {{ number_format($bobIncome, 2) }}</p>
                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">Cash: ₹
                            {{ number_format($cashIncome, 2) }}</p>
                    </div>
                    <div class="icon"><i class="fas fa-hand-holding-dollar"></i></div>
                    <a href="{{ route('transactions.show') }}" class="small-box-footer">Transactions <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- RNSB Card (add this inside the same .row with other small boxes) -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h5>RNSB</h5>
                        <h3>₹ {{ number_format($rnsbCurrentBalance, 2) }}</h3>
                        <p>Current Balance</p>
                        <p style="font-size:0.85rem; opacity:0.85; margin-bottom:0;">
                            <small class="text-white-50">Expense: ₹ {{ number_format($rnsbExpense, 2) }}</small>
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <a href="{{ route('accounts.show') }}?account=RNSB" class="small-box-footer">
                        View RNSB <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- CHART ROW -->
        <div class="row">
            <!-- Line Chart -->
            <section class="col-lg-12 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Income vs Expense (Last 6 Months)</h3>
                    </div>
                    <div class="card-body"><canvas id="incomeExpenseChart" style="min-height: 260px;"></canvas></div>
                </div>
            </section>

            <!-- Pie Chart (Cash + BOB Expenses Only) -->
            <section class="col-lg-12 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Expense Breakdown (Cash + BOB)</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Month List -->
                            <!-- Replace the existing Month List block with this -->
                            <!-- Month buttons (top) -->
                            <div class="col-12 mb-2">
                                <div class="d-flex flex-wrap gap-2 align-items-center" id="monthButtonGroup"
                                    style="row-gap:8px;">
                                    @forelse ($monthList as $ym => $label)
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary month-button {{ $ym === $selectedMonth ? 'active' : '' }}"
                                            style="margin-right: 10px;" data-ym="{{ $ym }}">
                                            {{ $label }}
                                        </button>
                                    @empty
                                        <span class="text-muted">No months available</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Container where JS will create NUM_CHARTS canvases + legends -->
                            <div class="row" id="pieContainerRow">
                                <div class="col-12">
                                    <div id="pieContainer" class="d-flex flex-row gap-3">
                                        <!-- canvases & legends generated by JS -->
                                    </div>
                                </div>
                            </div>

                            <!-- Pie Chart -->
                            {{-- <div class="col-7 text-center">
                                <canvas id="monthlyPieChart"></canvas>
                                <div id="pieLegend" class="mt-2 small"></div>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Transactions</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            @foreach ($recentTransactions as $tx)
                                <li class="item">
                                    <div class="product-info">
                                        <a class="product-title">
                                            {{ $tx->account->name ?? 'N/A' }}
                                            <span class="badge float-right">{{ $tx->credit_debit }}
                                                ₹{{ number_format($tx->amount, 2) }}</span>
                                        </a>
                                        <span class="product-description">
                                            {{ \Illuminate\Support\Str::limit($tx->description, 40) }} —
                                            {{ $tx->transaction_date }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </section>
        </div>



    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <script>
        Chart.register(ChartDataLabels);
        /* ---------------- LINE CHART ---------------- */
        const labels = {!! json_encode($labels ?? []) !!};
        const incomeData = {!! json_encode($incomeData ?? []) !!};
        const expenseData = {!! json_encode($expenseData ?? []) !!};

        if (document.getElementById('incomeExpenseChart')) {
            new Chart(document.getElementById('incomeExpenseChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Income',
                            data: incomeData,
                            borderWidth: 2,
                            tension: 0.2
                        },
                        {
                            label: 'Expense',
                            data: expenseData,
                            borderWidth: 2,
                            tension: 0.2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            color: '#222',
                            borderWidth: 2,
                            borderColor: '#ddd',
                            borderRadius: 5,
                            anchor: 'end',
                            align: 'top',
                            formatter: function(value, context) {
                                return value + '₹';
                            }
                        }
                    }
                }
            });
        }

        /* ---------------- PIE CHART (Cash+BOB Only) ---------------- */
        // monthlyPieData comes from server-side blade as before
        const monthlyPieData = {!! json_encode($monthlyPieData ?? []) !!};
        const monthListMap = {!! json_encode($monthList ?? []) !!}; // mapping ym -> label
        const NUM_CHARTS = 2; // change as needed

        // keep references to chart instances
        const pieCharts = new Array(NUM_CHARTS).fill(null);

        // create DOM for one pie-block (header + content row)
        function createPieBlock(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'pie-block';

            const header = document.createElement('div');
            header.className = 'pie-header';
            header.id = `pieHeader${index+1}`;
            header.innerHTML = `<div class="pie-title">Month</div><div class="pie-total" id="pieTotal${index+1}"></div>`;

            const content = document.createElement('div');
            content.className = 'pie-content';

            const canvasWrap = document.createElement('div');
            canvasWrap.style.flex = '0 0 auto';
            const canvas = document.createElement('canvas');
            canvas.id = `monthlyPieChart${index+1}`;
            canvas.className = 'pie-canvas';
            canvasWrap.appendChild(canvas);

            const legend = document.createElement('div');
            legend.id = `pieLegend${index+1}`;
            legend.className = 'pie-legend';

            content.appendChild(canvasWrap);
            content.appendChild(legend);

            // month detail (below legend)
            const monthDetail = document.createElement('div');
            monthDetail.id = `pieMonthDetail${index+1}`;
            monthDetail.className = 'pie-month-detail';

            wrapper.appendChild(header);
            wrapper.appendChild(content);
            wrapper.appendChild(monthDetail);

            return wrapper;
        }

        function buildPieUI() {
            const container = document.getElementById('pieContainer');
            container.innerHTML = '';
            for (let i = 0; i < NUM_CHARTS; i++) {
                const block = createPieBlock(i);
                container.appendChild(block);
            }
        }

        // draw pie and return Chart instance
        // draw pie and return Chart instance (REPLACE existing drawPieInto Chart creation)
        function drawPieInto(canvasId, legendId, headerId, totalId, monthDetailId, labelText, dataObj) {
            const labels = Object.keys(dataObj || {});
            const values = labels.map(k => Number(dataObj[k] || 0));
            const total = values.reduce((a, b) => a + b, 0);

            const canvasEl = document.getElementById(canvasId);
            if (!canvasEl) return null;
            const ctx = canvasEl.getContext('2d');

            // NOTE: do NOT manually set canvasEl.width/height here.
            // Let Chart.js handle sizing using aspectRatio below.

            if (!labels.length || total === 0) {
                const inst = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true, // important
                        aspectRatio: 1, // important: 1 => width == height
                        layout: {
                            padding: {
                                right: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                color: '#222',
                                borderWidth: 2,
                                borderColor: '#ddd',
                                borderRadius: 5,
                                anchor: 'end',
                                align: 'start',
                                // onset : 10,
                                font: {
                                    weight: '400',
                                    size: 10
                                },
                                display: function(context) {
                                    const val = context.dataset.data[context.dataIndex] || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    if (!total) return false;
                                    return (val / total) >= 0.01;
                                },
                                formatter: function(value, context) {
                                    const fullLabel = context.chart.data.labels[context.dataIndex] || '';
                                    return fullLabel + '\n' + value + '₹';
                                }
                            }
                        },

                    }
                });

                document.getElementById(legendId).innerHTML =
                    `<div class="text-muted">No expense data for ${labelText}</div>`;
                document.getElementById(headerId).querySelector('.pie-title').textContent = labelText || 'N/A';
                document.getElementById(totalId).textContent = '₹0';
                document.getElementById(monthDetailId).textContent = '';
                return inst;
            }

            const inst = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true, // important
                    aspectRatio: 1, // important: 1 => width == height
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            color: '#222',
                            borderWidth: 2,
                            borderColor: '#ddd',
                            borderRadius: 5,
                            anchor: 'end',
                            align: 'start',
                            // onset : 10,
                            font: {
                                weight: '400',
                                size: 10
                            },
                            display: function(context) {
                                const val = context.dataset.data[context.dataIndex] || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                if (!total) return false;
                                return (val / total) >= 0.01;
                            },
                            formatter: function(value, context) {
                                const fullLabel = context.chart.data.labels[context.dataIndex] || '';
                                return fullLabel + '\n' + value + '₹';
                            }
                        }
                    },

                }
            });

            // build legend (same as before)
            const bgColors = (inst.data.datasets[0].backgroundColor || []);
            let html = '<ul style="list-style-type: none;">';
            labels.forEach((cat, i) => {
                const v = values[i] || 0;
                const pct = total > 0 ? ((v / total) * 100).toFixed(1) : '0.0';
                const color = bgColors[i] || '#ccc';
                html += `<li>
                    <div class="legend-left">
                        <div class="legend-color-box" style="background:${color}"></div>
                        <div class="legend-label legend-value">${cat} ₹${v.toLocaleString()} · ${pct}%</div>
                    </div>
                 </li>`;
            });
            html += '</ul>';
            document.getElementById(legendId).innerHTML = html;

            // header and month detail
            document.getElementById(headerId).querySelector('.pie-title').textContent = labelText;
            document.getElementById(totalId).textContent = `Total ₹${total.toLocaleString()}`;
            document.getElementById(monthDetailId).textContent = `Details for ${labelText}`;

            return inst;
        }


        // choose months to show (same logic as original)
        function selectMonthsToShow(selectedYm) {
            const keys = Object.keys(monthlyPieData);
            if (!keys.length) return [];
            let idx = keys.indexOf(selectedYm);
            if (idx === -1) idx = 0;
            const months = [];
            for (let k = 0; k < NUM_CHARTS; k++) {
                const i = idx + k;
                if (i < keys.length) months.push(keys[i]);
            }
            // pad
            let p = 0;
            while (months.length < NUM_CHARTS && p < keys.length) {
                if (!months.includes(keys[p])) months.push(keys[p]);
                p++;
            }
            return months;
        }

        // main renderer
        function renderDynamicPies(selectedYm) {
            const monthsToShow = selectMonthsToShow(selectedYm);

            // destroy previous charts
            for (let i = 0; i < pieCharts.length; i++) {
                if (pieCharts[i]) {
                    try {
                        pieCharts[i].destroy();
                    } catch (e) {}
                    pieCharts[i] = null;
                }
            }

            for (let i = 0; i < NUM_CHARTS; i++) {
                const ym = monthsToShow[i] || null;
                const canvasId = `monthlyPieChart${i+1}`;
                const legendId = `pieLegend${i+1}`;
                const headerId = `pieHeader${i+1}`;
                const totalId = `pieTotal${i+1}`;
                const monthDetailId = `pieMonthDetail${i+1}`;
                const labelText = ym ? (monthListMap[ym] || ym) : 'N/A';
                const dataObj = ym ? (monthlyPieData[ym] || {}) : {};
                pieCharts[i] = drawPieInto(canvasId, legendId, headerId, totalId, monthDetailId, labelText, dataObj);
            }

            // optional: scroll first chart into view for better UX
            const firstCanvas = document.getElementById('monthlyPieChart1');
            if (firstCanvas) firstCanvas.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // setup UI + initial render
        buildPieUI();
        const initialMonth = "{{ $selectedMonth ?? '' }}";
        if (initialMonth) renderDynamicPies(initialMonth);
        else {
            const keys = Object.keys(monthlyPieData);
            if (keys.length) renderDynamicPies(keys[0]);
            else renderDynamicPies(null);
        }

        // month button handlers (single correct listener)
        document.querySelectorAll('.month-button').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.month-button').forEach(x => x.classList.remove('active'));
                this.classList.add('active');
                renderDynamicPies(this.dataset.ym);
            });
        });
    </script>

@endsection
