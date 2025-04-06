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
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
                    <div class="p-4 bg-blue-500 text-white rounded-full">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 dark:text-gray-400">Total Users</p>
                        <h2 class="text-xl font-semibold">1,250</h2>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
                    <div class="p-4 bg-green-500 text-white rounded-full">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 dark:text-gray-400">Monthly Revenue</p>
                        <h2 class="text-xl font-semibold">$45,000</h2>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
                    <div class="p-4 bg-yellow-500 text-white rounded-full">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 dark:text-gray-400">Orders Processed</p>
                        <h2 class="text-xl font-semibold">3,200</h2>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
                    <div class="p-4 bg-red-500 text-white rounded-full">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 dark:text-gray-400">Pending Issues</p>
                        <h2 class="text-xl font-semibold">5</h2>
                    </div>
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
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'العائدات الشهرية',
                data: [2000, 2500, 1800, 3000, 2800, 3200],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
