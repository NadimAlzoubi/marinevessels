<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">

            <div class="text text-2xl font-bold mb-4">Dashboard</div>

            @if (session('success'))
                <p class="text-green-600">{{ session('success') }}</p>
            @endif

            @if ($errors->any())
                <div class="mb-4">
                    <ul class="text-red-500">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-dashboard.card title="Total Vessels" icon="bxs-ship" color="blue" :value="$vesselsCount" />
                <x-dashboard.card title="Total Invoices" icon="bxs-file" color="green" :value="$invoicesCount" />
                <x-dashboard.card title="Total Revenue" icon="bxs-dollar-circle" color="yellow" :value="'$' . number_format($totalRevenue, 2)" />
                <x-dashboard.card title="Recent Invoices (7d)" icon="bxs-time" color="gray" :value="$recentInvoicesCount" />
                <x-dashboard.card title="Draft Invoices" icon="bxs-file" color="red" :value="$draftInvoicesCount" />
                <x-dashboard.card title="Proforma Invoices" icon="bxs-file-find" color="indigo" :value="$proformaInvoicesCount" />
                <x-dashboard.card title="Preliminary Invoices" icon="bxs-file-plus" color="purple" :value="$preliminaryInvoicesCount" />
                <x-dashboard.card title="Final Invoices" icon="bx-check-double" color="blue" :value="$finalInvoicesCount" />
                @if (!is_null($clientsCount))
                    <x-dashboard.card title="Total Clients" icon="bxs-user-account" color="orange" :value="$clientsCount" />
                @endif
                <x-dashboard.card title="Pending Vessels" icon="bxs-hourglass" color="amber" :value="$pendingVessels" />
                <x-dashboard.card title="In Progress Vessels" icon="bx-loader-circle" color="cyan"
                    :value="$inProgressVessels" />
                <x-dashboard.card title="Completed Vessels" icon="bxs-check-circle" color="lime" :value="$completedVessels" />
            </div>

            <!-- Revenue Chart -->
            <div class="mt-10 mb-5 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
                <div class="relative" style="height: 400px;">
                    <canvas id="revenueChart" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>


        </div>
    </section>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRevenueLabels) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlyRevenueData) !!},
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }


    });
</script>
