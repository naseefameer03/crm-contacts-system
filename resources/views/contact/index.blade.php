<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contacts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker.css') }}">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-8 py-6">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Contact Management System</h1>
            <p class="text-gray-600">Manage and filter your contacts efficiently</p>
        </header>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <form id="filterForm" class="space-y-4" autocomplete="off">
                <div class="flex flex-col md:flex-row md:items-end gap-4 mb-6">
                    <!-- Status Filter -->
                    <div class="flex-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 px-3 border">
                            <option value="">All Statuses</option>
                            <option value="Lead" {{ request('status') == 'Lead' ? 'selected' : '' }}>Lead</option>
                            <option value="Prospect" {{ request('status') == 'Prospect' ? 'selected' : '' }}>Prospect
                            </option>
                            <option value="Blocked" {{ request('status') == 'Blocked' ? 'selected' : '' }}>Blocked
                            </option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <!-- Company Filter -->
                    <div class="flex-1">
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                        <select name="company" id="company"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 px-3 border">
                            <option value="">All Companies</option>
                            <option value="Google" {{ request('company') == 'Google' ? 'selected' : '' }}>Google
                            </option>
                            <option value="Amazon" {{ request('company') == 'Amazon' ? 'selected' : '' }}>Amazon
                            </option>
                            <option value="Tesla" {{ request('company') == 'Tesla' ? 'selected' : '' }}>Tesla</option>
                            <option value="Apple" {{ request('company') == 'Apple' ? 'selected' : '' }}>Apple</option>
                            <option value="Meta" {{ request('company') == 'Meta' ? 'selected' : '' }}>Meta</option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="flex-1">
                        <label for="dateRangeVal" class="block text-sm font-medium text-gray-700 mb-1">Created
                            Date</label>
                        <input type="hidden" name="dateRange" id="dateRange" value="">
                        <div class="created-date-form" data-toggle="tooltip" title="Enquiry date range">
                            <div class="date-range-box">
                                <div id="created-daterange" class="dt-range daterange-pick">
                                    <i class="ft-calendar"></i>&nbsp;
                                    <span></span> <i class="ft-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="search" name="search"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 px-3 border"
                                value="{{ request('search') }}" placeholder="Name, email" maxlength="200">
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <div>
                        <button type="submit" id="filterBtn"
                            class="h-10 px-4 flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium text-sm smooth-transition">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <p id="resultCount" class="text-sm text-gray-600 font-medium"></p>
                    <button type="button" id="exportBtn"
                        class="h-9 px-3 flex items-center justify-center border border-gray-300 hover:border-primary-500 bg-white hover:bg-primary-50 text-primary-600 rounded-lg font-medium text-sm smooth-transition">
                        <i class="fas fa-file-export mr-2"></i>
                        <span id="exportBtnText">Export to CSV</span>
                    </button>
                </div>
            </form>

            <div id="pending_message" class="hidden">
                <div
                    class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-start gap-2 shadow-md mt-4 mb-4">
                    <svg class="w-5 h-5 mt-1 text-blue-500 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium" id="text-content">Your export is being processed. You’ll be
                            notified when it’s
                            ready.</p>
                    </div>
                </div>
            </div>

        </div>


        <!-- Contacts Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Phone</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Company</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="contactsTable1">
                        @forelse ($contacts as $contact)
                            @php
                                $statusClass = '';
                                switch ($contact->status) {
                                    case 'Lead':
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        break;
                                    case 'Prospect':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'Blocked':
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'Inactive':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        break;
                                }
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-medium">
                                            {{ first_letter($contact->name) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->company }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $contact->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contact->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center px-4 py-2 text-gray-500">No contacts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $contacts->withQueryString()->onEachSide(1)->links('pagination::tailwind') }}
            </div>

        </div>
    </div>

    <div id="exportModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg relative">
            <h2 class="text-xl font-semibold text-green-700 mb-4">✅ Export Completed</h2>
            <p class="mb-4 text-sm text-gray-600">Your export is ready. Click the button below to download it.</p>

            <a href="javascript:void(0)" id="downloadExportBtn"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded block text-center transition">
                ⬇ Download Export
            </a>

            <button onclick="closeModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
        </div>
    </div>

    <input type="hidden" name="base_url" id="base_url" value="{{ url('/') }}">

    <script src="{{ asset('assets/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/moment.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/contacts.js') }}"></script>

</body>

</html>
