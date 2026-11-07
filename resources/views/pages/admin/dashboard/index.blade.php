@extends('layouts.admin')

@section('admin_title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Dashboard Header with Developer Vibe -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="font-clash text-slate-900 mb-0" style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">
                    Admin Command Center
                </h1>
                <span class="badge font-mono-code" style="background: rgba(99, 102, 241, 0.1); color: #6366F1; border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 8px; font-size: 0.72rem; padding: 4px 8px;">
                    v2.4 &bull; LIVE TELEMETRY
                </span>
            </div>
            <p class="text-slate-500 font-jakarta mb-0 mt-1" style="font-size: 0.9rem;">
                Real-time store metrics, financial telemetry, and live order activities.
            </p>
        </div>

        <!-- Real-time Status Chip & Date -->
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-2xl border border-slate-200 shadow-sm" style="border-radius: 14px;">
                <span class="position-relative d-inline-flex" style="width: 8px; height: 8px;">
                    <span class="pulse-badge position-absolute" style="width: 100%; height: 100%; border-radius: 9999px; background: #10B981; opacity: 0.75;"></span>
                    <span style="position: relative; width: 8px; height: 8px; border-radius: 9999px; background: #10B981;"></span>
                </span>
                <span class="font-mono-code text-slate-700" style="font-size: 0.75rem; font-weight: 600;">SYS_HEALTH: OPTIMAL</span>
            </div>
            <div class="d-none d-lg-flex align-items-center gap-2 px-3 py-2 bg-white rounded-2xl border border-slate-200 shadow-sm text-slate-600 font-mono-code" style="border-radius: 14px; font-size: 0.75rem;">
                <iconify-icon icon="lucide:clock" width="14" height="14" class="text-indigo-500"></iconify-icon>
                <span>{{ now()->format('D, d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         1. KPI STATS SECTION (4 Gradient Stat Cards with Real Data)
         Responsive Grid: 1 mobile, 2 tablet, 4 desktop
         ==================================================================== -->
    <div class="row g-4 mb-4 pb-2">

        <!-- Card 1 - Purple: Monthly Earnings -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="adm-stat-card adm-stat-purple group h-100">
                <div class="adm-card-blob"></div>
                <div class="d-flex justify-content-between align-items-start position-relative">
                    <div>
                        <span class="adm-label text-purple-700 d-block mb-1" style="color: #7E22CE !important;">
                            MONTHLY EARNINGS
                        </span>
                        <div class="font-clash text-slate-900 my-1" id="kpiMonthlyEarnings" style="font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;">
                            Rp {{ number_format($earningsThisMonth, 0, ',', '.') }}
                        </div>
                    </div>
                    <!-- Icon container with scale-110 hover effect -->
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs"
                         style="width: 48px; height: 48px; border-radius: 16px; background: rgba(168, 85, 247, 0.15); color: #7E22CE; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <iconify-icon icon="lucide:wallet" width="24" height="24"></iconify-icon>
                    </div>
                </div>
                <!-- Trend Indicator -->
                <div class="d-flex align-items-center gap-2 mt-3 pt-1 position-relative">
                    <span class="d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill font-mono-code"
                          style="font-size: 0.72rem; font-weight: 700; background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25);">
                        <iconify-icon icon="lucide:trending-up" width="13" height="13"></iconify-icon>
                        +12%
                    </span>
                    <span class="text-slate-500 font-jakarta" style="font-size: 0.75rem;">vs bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Card 2 - Teal: Annual Earnings -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="adm-stat-card adm-stat-teal group h-100">
                <div class="adm-card-blob"></div>
                <div class="d-flex justify-content-between align-items-start position-relative">
                    <div>
                        <span class="adm-label text-teal-700 d-block mb-1" style="color: #0F766E !important;">
                            ANNUAL EARNINGS
                        </span>
                        <div class="font-clash text-slate-900 my-1" id="kpiAnnualEarnings" style="font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;">
                            Rp {{ number_format($earningsThisYear, 0, ',', '.') }}
                        </div>
                    </div>
                    <!-- Icon container with scale-110 hover effect -->
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs"
                         style="width: 48px; height: 48px; border-radius: 16px; background: rgba(20, 184, 166, 0.15); color: #0F766E; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <iconify-icon icon="lucide:badge-dollar-sign" width="24" height="24"></iconify-icon>
                    </div>
                </div>
                <!-- Year Label Indicator -->
                <div class="d-flex align-items-center gap-2 mt-3 pt-1 position-relative">
                    <span class="d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill font-mono-code"
                          style="font-size: 0.72rem; font-weight: 700; background: rgba(13, 148, 136, 0.12); color: #0F766E; border: 1px solid rgba(13, 148, 136, 0.25);">
                        <iconify-icon icon="lucide:calendar" width="13" height="13"></iconify-icon>
                        FY {{ date('Y') }}
                    </span>
                    <span class="text-slate-500 font-jakarta" style="font-size: 0.75rem;">tahun fiskal berjalan</span>
                </div>
            </div>
        </div>

        <!-- Card 3 - Blue: Monthly Target -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="adm-stat-card adm-stat-blue group h-100">
                <div class="adm-card-blob"></div>
                <div class="d-flex justify-content-between align-items-start position-relative">
                    <div>
                        <span class="adm-label text-blue-700 d-block mb-1" style="color: #0369A1 !important;">
                            MONTHLY TARGET
                        </span>
                        <div class="font-clash text-slate-900 my-1" id="kpiTargetPct" style="font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;">
                            {{ number_format($earningsPercentage, 2) }}%
                        </div>
                    </div>
                    <!-- Icon container with scale-110 hover effect -->
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs"
                         style="width: 48px; height: 48px; border-radius: 16px; background: rgba(14, 165, 233, 0.15); color: #0369A1; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <iconify-icon icon="lucide:target" width="24" height="24"></iconify-icon>
                    </div>
                </div>
                <!-- Target Progress Bar -->
                <div class="mt-3 pt-1 position-relative">
                    <div class="w-100 rounded-pill overflow-hidden" style="height: 7px; background: rgba(14, 165, 233, 0.18);">
                        <div class="h-100 rounded-pill smooth-all"
                             id="kpiTargetBar"
                             style="width: {{ min(100, $earningsPercentage) }}%; background: linear-gradient(90deg, #0284C7 0%, #6366F1 100%);"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 text-slate-500 font-mono-code" style="font-size: 0.7rem;">
                        <span>Target: Rp 50.000.000</span>
                        <span class="text-blue-700 font-weight-bold" id="kpiTargetSubtext">{{ number_format($earningsPercentage, 1) }}% tercapai</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 - Orange: Pending Orders -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="adm-stat-card adm-stat-orange group h-100">
                <div class="adm-card-blob"></div>
                <div class="d-flex justify-content-between align-items-start position-relative">
                    <div>
                        <span class="adm-label text-orange-700 d-block mb-1" style="color: #C2410C !important;">
                            PENDING ORDERS
                        </span>
                        <div class="font-clash text-slate-900 my-1" id="kpiPendingOrders" style="font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;">
                            {{ $pendingRequests }}
                        </div>
                    </div>
                    <!-- Icon container with scale-110 hover effect -->
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs"
                         style="width: 48px; height: 48px; border-radius: 16px; background: rgba(249, 115, 22, 0.15); color: #C2410C; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <iconify-icon icon="lucide:clock" width="24" height="24"></iconify-icon>
                    </div>
                </div>
                <!-- Needs Attention Flag Indicator -->
                <div class="d-flex align-items-center gap-2 mt-3 pt-1 position-relative">
                    <span class="d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill font-mono-code"
                          style="font-size: 0.72rem; font-weight: 700; background: rgba(245, 158, 11, 0.14); color: #B45309; border: 1px solid rgba(245, 158, 11, 0.3);">
                        <iconify-icon icon="lucide:alert-circle" width="13" height="13"></iconify-icon>
                        needs attention
                    </span>
                    <span class="text-slate-500 font-jakarta" style="font-size: 0.75rem;">butuh tindakan</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ====================================================================
         2. CHARTS & DATA VISUALIZATION (REAL DATA)
         - Earnings Overview (2/3 width)
         - Category Mix (1/3 width)
         ==================================================================== -->
    <div class="row g-4 mb-4 pb-2">

        <!-- 1. Earnings Overview (2/3 width) -->
        <div class="col-12 col-lg-8">
            <div class="adm-glass-card p-4 p-md-5 h-100 position-relative">

                <!-- Header & Controls -->
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h2 class="font-clash text-slate-900 mb-0" style="font-size: 1.35rem; font-weight: 800; letter-spacing: -0.01em;">
                                Earnings Overview
                            </h2>
                            <span class="badge font-mono-code" style="background: rgba(16, 185, 129, 0.1); color: #059669; border-radius: 6px; font-size: 0.68rem; padding: 3px 6px;">
                                REALTIME TELEMETRY
                            </span>
                        </div>
                        <p class="text-slate-500 font-jakarta mb-0 mt-1" style="font-size: 0.82rem;">
                            Perbandingan arus Revenue, Expenses, dan Net Profit berdasarkan data aktual toko
                        </p>
                    </div>

                    <!-- View Switcher (Monthly / Weekly) -->
                    <div class="d-flex align-items-center p-1 rounded-2xl border border-slate-200 bg-slate-100/80" style="border-radius: 14px;">
                        <button type="button"
                                id="btnViewWeekly"
                                class="btn btn-sm px-3 py-1.5 font-jakarta font-weight-bold rounded-xl smooth-all"
                                style="border-radius: 10px; font-size: 0.78rem; background: #FFFFFF; color: #4F46E5; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                            Weekly View
                        </button>
                        <button type="button"
                                id="btnViewMonthly"
                                class="btn btn-sm px-3 py-1.5 font-jakarta font-weight-bold rounded-xl smooth-all"
                                style="border-radius: 10px; font-size: 0.78rem; background: transparent; color: #64748B;">
                            Monthly View
                        </button>
                    </div>
                </div>

                <!-- Legend with Colored Dots -->
                <div class="d-flex flex-wrap align-items-center gap-3 gap-sm-4 mb-4 pb-2 border-bottom border-slate-100">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #6366F1; box-shadow: 0 0 8px rgba(99, 102, 241, 0.6);"></span>
                        <span class="font-jakarta text-slate-700" style="font-size: 0.82rem; font-weight: 600;">Revenue</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #F59E0B; box-shadow: 0 0 8px rgba(245, 158, 11, 0.6);"></span>
                        <span class="font-jakarta text-slate-700" style="font-size: 0.82rem; font-weight: 600;">Expenses</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #10B981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);"></span>
                        <span class="font-jakarta text-slate-700" style="font-size: 0.82rem; font-weight: 600;">Profit</span>
                    </div>
                </div>

                <!-- Animated SVG Line Chart Container -->
                <div class="position-relative" style="min-height: 280px; width: 100%;">
                    <!-- Chart Tooltip Overlay -->
                    <div id="chartTooltip" class="chart-tooltip">Rp 0</div>

                    <svg id="earningsSvgChart"
                         viewBox="0 0 760 280"
                         preserveAspectRatio="none"
                         style="width: 100%; height: 280px; overflow: visible;">
                        <defs>
                            <!-- Linear Gradient for Area Fill under Revenue -->
                            <linearGradient id="revenueAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#6366F1" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#6366F1" stop-opacity="0.0"/>
                            </linearGradient>
                            <linearGradient id="profitAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10B981" stop-opacity="0.15"/>
                                <stop offset="100%" stop-color="#10B981" stop-opacity="0.0"/>
                            </linearGradient>
                            <filter id="glowRevenue" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#6366F1" flood-opacity="0.3"/>
                            </filter>
                        </defs>

                        <!-- Background Grid Lines -->
                        <g class="chart-grid-lines" stroke="#E2E8F0" stroke-dasharray="4,4" stroke-width="1">
                            <line x1="50" y1="40" x2="740" y2="40" />
                            <line x1="50" y1="95" x2="740" y2="95" />
                            <line x1="50" y1="150" x2="740" y2="150" />
                            <line x1="50" y1="205" x2="740" y2="205" />
                            <line x1="50" y1="250" x2="740" y2="250" stroke="#CBD5E1" stroke-dasharray="none" />
                        </g>

                        <!-- Y-axis Labels -->
                        <g class="chart-y-labels" fill="#94A3B8" font-size="10" font-family="'JetBrains Mono', monospace" text-anchor="end">
                            <text x="42" y="44">20M</text>
                            <text x="42" y="99">15M</text>
                            <text x="42" y="154">10M</text>
                            <text x="42" y="209">5M</text>
                            <text x="42" y="254">0</text>
                        </g>

                        <!-- Area Fill Under Revenue -->
                        <path id="pathRevenueArea"
                              d="M 60,160 C 115,145 140,130 170,130 C 225,130 250,145 280,145 C 335,145 360,105 390,105 C 445,105 470,70 500,70 C 555,70 580,52 610,52 C 665,52 690,88 720,88 L 720,250 L 60,250 Z"
                              fill="url(#revenueAreaGrad)"
                              opacity="0.85" />

                        <!-- Area Fill Under Profit -->
                        <path id="pathProfitArea"
                              d="M 60,202 C 115,190 140,177 170,177 C 225,177 250,190 280,190 C 335,190 360,161 390,161 C 445,161 470,136 500,136 C 555,136 580,128 610,128 C 665,128 690,147 720,147 L 720,250 L 60,250 Z"
                              fill="url(#profitAreaGrad)"
                              opacity="0.75" />

                        <!-- Path 1: Expenses (Orange #F59E0B) -->
                        <path id="pathExpenses"
                              class="svg-animated-path"
                              d="M 60,195 C 115,192 140,188 170,188 C 225,188 250,192 280,192 C 335,192 360,179 390,179 C 445,179 470,170 500,170 C 555,170 580,162 610,162 C 665,162 690,176 720,176"
                              fill="none"
                              stroke="#F59E0B"
                              stroke-width="3"
                              stroke-linecap="round"
                              stroke-linejoin="round" />

                        <!-- Path 2: Profit (Emerald #10B981) -->
                        <path id="pathProfit"
                              class="svg-animated-path"
                              d="M 60,202 C 115,190 140,177 170,177 C 225,177 250,190 280,190 C 335,190 360,161 390,161 C 445,161 470,136 500,136 C 555,136 580,128 610,128 C 665,128 690,147 720,147"
                              fill="none"
                              stroke="#10B981"
                              stroke-width="3.5"
                              stroke-linecap="round"
                              stroke-linejoin="round" />

                        <!-- Path 3: Revenue (Indigo #6366F1) -->
                        <path id="pathRevenue"
                              class="svg-animated-path"
                              d="M 60,160 C 115,145 140,130 170,130 C 225,130 250,145 280,145 C 335,145 360,105 390,105 C 445,105 470,70 500,70 C 555,70 580,52 610,52 C 665,52 690,88 720,88"
                              fill="none"
                              stroke="#6366F1"
                              stroke-width="4"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              filter="url(#glowRevenue)" />

                        <!-- Interactive Nodes for Revenue -->
                        <g id="chartNodesRevenue" fill="#6366F1" stroke="#FFFFFF" stroke-width="2.5">
                            <!-- Injected dynamically -->
                        </g>

                        <!-- X-axis Labels -->
                        <g id="xAxisLabels" fill="#64748B" font-size="11" font-family="'Plus Jakarta Sans', sans-serif" font-weight="600" text-anchor="middle">
                            <!-- Injected dynamically -->
                        </g>
                    </svg>
                </div>

            </div>
        </div>

        <!-- 2. Category Mix (1/3 width, REAL DATA) -->
        <div class="col-12 col-lg-4">
            <div class="adm-glass-card p-4 p-md-5 h-100 d-flex flex-column justify-content-between position-relative">

                <!-- Header -->
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h2 class="font-clash text-slate-900 mb-0" style="font-size: 1.35rem; font-weight: 800; letter-spacing: -0.01em;">
                            Category Mix
                        </h2>
                        <span class="badge font-mono-code" style="background: rgba(99, 102, 241, 0.1); color: #6366F1; border-radius: 6px; font-size: 0.68rem; padding: 3px 6px;">
                            INVENTORY
                        </span>
                    </div>
                    <p class="text-slate-500 font-jakarta mb-3" style="font-size: 0.82rem;">
                        Distribusi stok dan proporsi kategori produk real
                    </p>
                </div>

                <!-- Animated Donut Chart Container -->
                <div class="d-flex align-items-center justify-content-center position-relative my-3" style="min-height: 220px;">
                    <svg width="220" height="220" viewBox="0 0 200 200">
                        <defs>
                            <filter id="donutGlow" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.08"/>
                            </filter>
                        </defs>

                        <!-- Base Circle Track -->
                        <circle cx="100" cy="100" r="70" fill="none" stroke="#F1F5F9" stroke-width="18" />

                        <!-- Dynamic Segments from Database -->
                        <g id="donutSegmentsGroup">
                            @foreach($categoryList as $c)
                                <circle class="donut-segment"
                                        cx="100" cy="100" r="70"
                                        fill="none"
                                        stroke="{{ $c['color'] }}"
                                        stroke-width="18"
                                        stroke-dasharray="{{ $c['dasharray'] }}"
                                        stroke-dashoffset="600"
                                        style="--target-dashoffset: {{ $c['dashoffset'] }}; transform-origin: center; transform: rotate(-90deg);"
                                        filter="url(#donutGlow)" />
                            @endforeach
                        </g>

                        <!-- Center Text: Total Items Count -->
                        <g text-anchor="middle">
                            <text x="100" y="96" class="font-clash" id="donutTotalCount" font-size="28" font-weight="800" fill="#0F172A">{{ $totalItems }}</text>
                            <text x="100" y="116" class="font-jakarta adm-label" font-size="9" font-weight="700" fill="#64748B" letter-spacing="1.5">TOTAL ITEMS</text>
                        </g>
                    </svg>
                </div>

                <!-- Legend with Colored Dot Indicators -->
                <div class="d-flex flex-column gap-2 mt-2 pt-2 border-top border-slate-100" id="donutLegendContainer">
                    @foreach($categoryList as $c)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-xl smooth-all" style="background: rgba(15, 23, 42, 0.02); border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2.5">
                                <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: {{ $c['color'] }}; box-shadow: 0 0 6px {{ $c['color'] }};"></span>
                                <span class="font-jakarta text-slate-800" style="font-size: 0.85rem; font-weight: 600;">{{ $c['name'] }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge font-mono-code font-weight-bold" style="background: rgba(99, 102, 241, 0.1); color: #334155; font-size: 0.75rem; border-radius: 8px;">{{ $c['count'] }} items</span>
                                <span class="text-slate-400 font-mono-code" style="font-size: 0.72rem;">{{ $c['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

    </div>

    <!-- ====================================================================
         3. QUICK ACTIONS SECTION
         ==================================================================== -->
    <div class="mb-4 pb-2">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="font-clash text-slate-900 mb-0" style="font-size: 1.2rem; font-weight: 800;">
                    Quick Actions
                </h3>
                <span class="text-slate-500 font-jakarta" style="font-size: 0.8rem;">Jalan pintas operasional untuk administrator</span>
            </div>
            <span class="adm-label text-slate-400 font-mono-code" style="font-size: 0.68rem;">COMMAND SHORTCUTS</span>
        </div>

        <div class="row g-3 g-md-4">

            <!-- Action 1: New Product (Indigo) -->
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.products.index') }}" class="adm-action-card adm-action-indigo group h-100">
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs flex-shrink-0"
                         style="width: 46px; height: 46px; border-radius: 16px; background: rgba(99, 102, 241, 0.12); color: #4F46E5;">
                        <iconify-icon icon="lucide:plus-circle" width="22" height="22"></iconify-icon>
                    </div>
                    <div>
                        <div class="font-clash text-slate-900 mb-0" style="font-size: 0.95rem; font-weight: 700; line-height: 1.2;">
                            New Product
                        </div>
                        <span class="text-slate-500 font-jakarta d-block mt-0.5" style="font-size: 0.75rem;">
                            Tambah inventaris
                        </span>
                    </div>
                </a>
            </div>

            <!-- Action 2: Export Data (Emerald) -> Live CSV stream -->
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.dashboard.export') }}" id="btnExportData" class="adm-action-card adm-action-emerald group h-100">
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs flex-shrink-0"
                         style="width: 46px; height: 46px; border-radius: 16px; background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <iconify-icon icon="lucide:download-cloud" width="22" height="22"></iconify-icon>
                    </div>
                    <div>
                        <div class="font-clash text-slate-900 mb-0" style="font-size: 0.95rem; font-weight: 700; line-height: 1.2;">
                            Export Data
                        </div>
                        <span class="text-slate-500 font-jakarta d-block mt-0.5" style="font-size: 0.75rem;">
                            CSV Telemetri Penjualan
                        </span>
                    </div>
                </a>
            </div>

            <!-- Action 3: Customer List (Amber) -->
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.users.index') }}" class="adm-action-card adm-action-amber group h-100">
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs flex-shrink-0"
                         style="width: 46px; height: 46px; border-radius: 16px; background: rgba(245, 158, 11, 0.12); color: #D97706;">
                        <iconify-icon icon="lucide:users" width="22" height="22"></iconify-icon>
                    </div>
                    <div>
                        <div class="font-clash text-slate-900 mb-0" style="font-size: 0.95rem; font-weight: 700; line-height: 1.2;">
                            Customer List
                        </div>
                        <span class="text-slate-500 font-jakarta d-block mt-0.5" style="font-size: 0.75rem;">
                            Kelola pelanggan
                        </span>
                    </div>
                </a>
            </div>

            <!-- Action 4: System Config (Rose) -->
            <div class="col-6 col-lg-3">
                <div role="button" data-toggle="modal" data-target="#systemConfigModal" class="adm-action-card adm-action-rose group h-100">
                    <div class="d-flex align-items-center justify-content-center smooth-all group-hover:scale-110 shadow-xs flex-shrink-0"
                         style="width: 46px; height: 46px; border-radius: 16px; background: rgba(244, 63, 94, 0.12); color: #E11D48;">
                        <iconify-icon icon="lucide:settings-2" width="22" height="22"></iconify-icon>
                    </div>
                    <div>
                        <div class="font-clash text-slate-900 mb-0" style="font-size: 0.95rem; font-weight: 700; line-height: 1.2;">
                            System Config
                        </div>
                        <span class="text-slate-500 font-jakarta d-block mt-0.5" style="font-size: 0.75rem;">
                            API &amp; Environment
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ====================================================================
         4. ACTIVITY FEED ("Live Activity" Card with Real Events)
         ==================================================================== -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="adm-glass-card p-4 p-md-5">

                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 mb-4 pb-2 border-bottom border-slate-100">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h2 class="font-clash text-slate-900 mb-0" style="font-size: 1.35rem; font-weight: 800; letter-spacing: -0.01em;">
                                Live Activity
                            </h2>
                            <span class="d-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill font-mono-code"
                                  style="font-size: 0.7rem; font-weight: 700; background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25);">
                                <span class="d-inline-block rounded-circle" style="width: 6px; height: 6px; background-color: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                                LIVE FEED
                            </span>
                        </div>
                        <p class="text-slate-500 font-jakarta mb-0 mt-1" style="font-size: 0.82rem;">
                            Notifikasi pesanan, pembayaran, dan pendaftaran pengguna terkini dari database
                        </p>
                    </div>

                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm px-3 py-1.5 rounded-xl font-jakarta font-weight-bold text-decoration-none smooth-all"
                       style="border-radius: 10px; font-size: 0.78rem; background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;">
                        Lihat Seluruh Log Transaksi &rarr;
                    </a>
                </div>

                <!-- Activity Items List (Live from Database) -->
                <div class="d-flex flex-column gap-3" id="liveActivityFeedContainer">
                    @foreach($activityFeed as $item)
                        <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 p-3 rounded-2xl smooth-all border border-slate-100 hover:border-slate-200"
                             style="background: #FFFFFF; border-radius: 16px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center shadow-xs flex-shrink-0"
                                     style="width: 44px; height: 44px; border-radius: 14px; background: {{ $item['icon_bg'] }}; color: {{ $item['icon_color'] }};">
                                    <iconify-icon icon="{{ $item['icon'] }}" width="20" height="20"></iconify-icon>
                                </div>
                                <div>
                                    <div class="font-jakarta font-weight-bold text-slate-900" style="font-size: 0.9rem;">
                                        {{ $item['title'] }}
                                    </div>
                                    <div class="text-slate-500 font-mono-code" style="font-size: 0.75rem;">
                                        {{ $item['details'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge font-mono-code" style="background: {{ $item['badge_bg'] }}; color: {{ $item['badge_color'] }}; font-size: 0.72rem; border-radius: 8px; padding: 5px 9px;">
                                    {{ $item['badge'] }}
                                </span>
                                <span class="text-slate-400 font-mono-code" style="font-size: 0.72rem;">{{ $item['time_ago'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

</div>

<!-- System Config Modal (Quick Action 4) -->
<div class="modal fade" id="systemConfigModal" tabindex="-1" role="dialog" aria-labelledby="systemConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header px-4 pt-4 pb-2 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-2xl" style="width: 40px; height: 40px; background: rgba(244, 63, 94, 0.12); color: #E11D48;">
                        <iconify-icon icon="lucide:settings-2" width="22" height="22"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title font-clash font-weight-bold text-slate-900 mb-0" id="systemConfigModalLabel" style="font-size: 1.15rem;">System Configuration</h5>
                        <span class="text-slate-400 font-mono-code" style="font-size: 0.72rem;">ENVIRONMENT: PRODUCTION &bull; DRVN v2.4</span>
                    </div>
                </div>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-3 font-jakarta">
                <div class="p-3 mb-3 rounded-xl border border-slate-200" style="background: #F8FAFC; border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold text-slate-700" style="font-size: 0.82rem;">Database Driver</span>
                        <span class="badge font-mono-code" style="background: #E2E8F0; color: #334155;">{{ config('database.default') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold text-slate-700" style="font-size: 0.82rem;">Payment Gateway</span>
                        <span class="badge font-mono-code" style="background: rgba(16, 185, 129, 0.15); color: #047857;">Midtrans Snap Active</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold text-slate-700" style="font-size: 0.82rem;">Cache &amp; Session</span>
                        <span class="badge font-mono-code" style="background: #E2E8F0; color: #334155;">File / Redis Ready</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="adm-label text-slate-600 mb-1 d-block" style="font-size: 0.7rem;">STORE CURRENCY</label>
                    <input type="text" class="form-control font-mono-code" value="IDR (Rp) - Indonesian Rupiah" readonly style="border-radius: 10px; font-size: 0.85rem; background: #F1F5F9;">
                </div>
                <div class="mb-2">
                    <label class="adm-label text-slate-600 mb-1 d-block" style="font-size: 0.7rem;">MONTHLY REVENUE GOAL</label>
                    <input type="text" class="form-control font-mono-code" value="Rp 50.000.000" readonly style="border-radius: 10px; font-size: 0.85rem; background: #F1F5F9;">
                </div>
            </div>
            <div class="modal-footer px-4 pb-4 pt-2 border-0 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary px-4 py-2 font-jakarta font-weight-bold btn-press" type="button" data-dismiss="modal" style="border-radius: 12px; font-size: 0.85rem; background: #6366F1; border-color: #4F46E5;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        /* ==== REAL TELEMETRY DATA BINDING & INTERACTIVITY ==== */
        var weeklyTelemetry = @json($weeklyTelemetry);
        var monthlyTelemetry = @json($monthlyTelemetry);
        var isWeekly = true;

        var btnWeekly = document.getElementById('btnViewWeekly');
        var btnMonthly = document.getElementById('btnViewMonthly');

        var pathRevenue = document.getElementById('pathRevenue');
        var pathExpenses = document.getElementById('pathExpenses');
        var pathProfit = document.getElementById('pathProfit');
        var pathRevenueArea = document.getElementById('pathRevenueArea');
        var pathProfitArea = document.getElementById('pathProfitArea');
        var xAxisLabels = document.getElementById('xAxisLabels');
        var chartNodesRevenue = document.getElementById('chartNodesRevenue');
        var tooltip = document.getElementById('chartTooltip');

        function formatIDR(num) {
            return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // SVG Coordinate Generator from Real Data
        function buildSvgPathData(items, key, maxVal, width, height, padX, padY) {
            var step = (width - (padX * 2)) / (items.length - 1);
            var points = items.map(function (item, idx) {
                var x = padX + (idx * step);
                var ratio = maxVal > 0 ? (item[key] / maxVal) : 0;
                var y = (height - padY) - (ratio * (height - (padY * 2)));
                return { x: x, y: y, val: item[key], label: item.day || item.month };
            });

            if (points.length === 0) return { path: '', area: '', points: [] };

            var pathStr = "M " + points[0].x.toFixed(1) + "," + points[0].y.toFixed(1);
            for (var i = 1; i < points.length; i++) {
                var prev = points[i - 1];
                var cur = points[i];
                var cp1x = prev.x + (cur.x - prev.x) / 2;
                var cp2x = cp1x;
                pathStr += " C " + cp1x.toFixed(1) + "," + prev.y.toFixed(1) + " " + cp2x.toFixed(1) + "," + cur.y.toFixed(1) + " " + cur.x.toFixed(1) + "," + cur.y.toFixed(1);
            }

            var areaStr = pathStr + " L " + points[points.length - 1].x.toFixed(1) + ",250 L " + points[0].x.toFixed(1) + ",250 Z";

            return {
                path: pathStr,
                area: areaStr,
                points: points
            };
        }

        function renderChart() {
            var items = isWeekly ? weeklyTelemetry : monthlyTelemetry;
            var maxVal = 22000000; // 22M max scale

            var revResult = buildSvgPathData(items, 'revenue', maxVal, 760, 280, 60, 40);
            var expResult = buildSvgPathData(items, 'expenses', maxVal, 760, 280, 60, 40);
            var profResult = buildSvgPathData(items, 'profit', maxVal, 760, 280, 60, 40);

            pathRevenue.setAttribute('d', revResult.path);
            pathExpenses.setAttribute('d', expResult.path);
            pathProfit.setAttribute('d', profResult.path);
            pathRevenueArea.setAttribute('d', revResult.area);
            pathProfitArea.setAttribute('d', profResult.area);

            // X-axis labels
            var labelsHtml = '';
            var nodesHtml = '';
            revResult.points.forEach(function (pt) {
                labelsHtml += '<text x="' + pt.x.toFixed(1) + '" y="272">' + pt.label + '</text>';
                nodesHtml += '<circle class="chart-point" cx="' + pt.x.toFixed(1) + '" cy="' + pt.y.toFixed(1) + '" r="5" data-val="Revenue: ' + formatIDR(pt.val) + ' (' + pt.label + ')"></circle>';
            });

            xAxisLabels.innerHTML = labelsHtml;
            chartNodesRevenue.innerHTML = nodesHtml;

            // Re-trigger SVG Dash Animation
            [pathRevenue, pathExpenses, pathProfit].forEach(function (p) {
                if (!p) return;
                p.classList.remove('svg-animated-path');
                void p.offsetWidth;
                p.classList.add('svg-animated-path');
            });

            bindTooltips();
        }

        function setView(weekly) {
            isWeekly = weekly;
            if (isWeekly) {
                btnWeekly.style.background = '#FFFFFF';
                btnWeekly.style.color = '#4F46E5';
                btnWeekly.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';

                btnMonthly.style.background = 'transparent';
                btnMonthly.style.color = '#64748B';
                btnMonthly.style.boxShadow = 'none';
            } else {
                btnMonthly.style.background = '#FFFFFF';
                btnMonthly.style.color = '#4F46E5';
                btnMonthly.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';

                btnWeekly.style.background = 'transparent';
                btnWeekly.style.color = '#64748B';
                btnWeekly.style.boxShadow = 'none';
            }
            renderChart();
        }

        if (btnWeekly && btnMonthly) {
            btnWeekly.addEventListener('click', function () { setView(true); });
            btnMonthly.addEventListener('click', function () { setView(false); });
        }

        // SVG Tooltips Binding
        function bindTooltips() {
            var svg = document.getElementById('earningsSvgChart');
            var points = document.querySelectorAll('.chart-point');
            if (!svg || !tooltip) return;

            points.forEach(function (pt) {
                pt.addEventListener('mouseenter', function () {
                    var val = pt.getAttribute('data-val');
                    tooltip.textContent = val;
                    tooltip.classList.add('is-active');

                    var rect = pt.getBoundingClientRect();
                    var containerRect = svg.parentElement.getBoundingClientRect();
                    tooltip.style.left = (rect.left - containerRect.left + (rect.width / 2) - 60) + 'px';
                    tooltip.style.top = (rect.top - containerRect.top - 38) + 'px';

                    pt.setAttribute('r', '7');
                });

                pt.addEventListener('mouseleave', function () {
                    tooltip.classList.remove('is-active');
                    pt.setAttribute('r', '5');
                });
            });
        }

        // Initial Chart Render
        renderChart();

        // Real-Time Telemetry Update Callback (invoked when Topbar Refresh is clicked)
        window.updateDashboardTelemetry = function (data) {
            if (!data) return;

            // Update KPI cards
            var elMonth = document.getElementById('kpiMonthlyEarnings');
            if (elMonth) elMonth.textContent = formatIDR(data.earningsThisMonth);

            var elYear = document.getElementById('kpiAnnualEarnings');
            if (elYear) elYear.textContent = formatIDR(data.earningsThisYear);

            var elTarget = document.getElementById('kpiTargetPct');
            if (elTarget) elTarget.textContent = Number(data.earningsPercentage).toFixed(2) + '%';

            var elBar = document.getElementById('kpiTargetBar');
            if (elBar) elBar.style.width = Math.min(100, data.earningsPercentage) + '%';

            var elTargetSub = document.getElementById('kpiTargetSubtext');
            if (elTargetSub) elTargetSub.textContent = Number(data.earningsPercentage).toFixed(1) + '% tercapai';

            var elPending = document.getElementById('kpiPendingOrders');
            if (elPending) elPending.textContent = data.pendingRequests;

            // Update datasets
            if (data.weeklyTelemetry) weeklyTelemetry = data.weeklyTelemetry;
            if (data.monthlyTelemetry) monthlyTelemetry = data.monthlyTelemetry;

            renderChart();

            // Update Donut Chart
            if (data.totalItems && document.getElementById('donutTotalCount')) {
                document.getElementById('donutTotalCount').textContent = data.totalItems;
            }

            // Update Activity Feed
            if (data.activityFeed && document.getElementById('liveActivityFeedContainer')) {
                var feedHtml = '';
                data.activityFeed.forEach(function (item) {
                    feedHtml += '<div class="d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 p-3 rounded-2xl smooth-all border border-slate-100 hover:border-slate-200" style="background: #FFFFFF; border-radius: 16px;">' +
                        '<div class="d-flex align-items-center gap-3">' +
                            '<div class="d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 44px; height: 44px; border-radius: 14px; background: ' + item.icon_bg + '; color: ' + item.icon_color + ';">' +
                                '<iconify-icon icon="' + item.icon + '" width="20" height="20"></iconify-icon>' +
                            '</div>' +
                            '<div>' +
                                '<div class="font-jakarta font-weight-bold text-slate-900" style="font-size: 0.9rem;">' + item.title + '</div>' +
                                '<div class="text-slate-500 font-mono-code" style="font-size: 0.75rem;">' + item.details + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="d-flex align-items-center gap-3">' +
                            '<span class="badge font-mono-code" style="background: ' + item.badge_bg + '; color: ' + item.badge_color + '; font-size: 0.72rem; border-radius: 8px; padding: 5px 9px;">' + item.badge + '</span>' +
                            '<span class="text-slate-400 font-mono-code" style="font-size: 0.72rem;">' + item.time_ago + '</span>' +
                        '</div>' +
                    '</div>';
                });
                document.getElementById('liveActivityFeedContainer').innerHTML = feedHtml;
            }
        };
    })();
</script>
@endpush
