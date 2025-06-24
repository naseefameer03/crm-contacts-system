<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advanced Contact Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e3f2fd, #ffffff);
      font-family: 'Segoe UI', sans-serif;
    }

    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .form-label {
      font-weight: 500;
      color: #333;
    }

    .table thead {
      background-color: #0d6efd;
      color: white;
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: #f9f9f9;
    }

    .btn-primary {
      background-color: #0d6efd;
      border: none;
    }

    .btn-primary:hover {
      background-color: #0b5ed7;
    }

    .table-container {
      max-height: 400px;
      overflow-y: auto;
      border: 1px solid #dee2e6;
      border-radius: 0.5rem;
    }

    .pagination .page-item.active .page-link {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }

    .pagination .page-link {
      color: #0d6efd;
    }

    h2 span {
      color: #0d6efd;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <div class="card p-5">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-address-book me-2"></i>Contact <span>Management</span></h2>
        <button class="btn btn-primary px-4" onclick="exportToCSV()">
          <i class="fas fa-file-csv me-2"></i>Export CSV
        </button>
      </div>

      <form id="filter-form" class="row g-4">
        <div class="col-md-4">
          <label for="status" class="form-label">Status</label>
          <select id="status" class="form-select shadow-sm">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <div class="col-md-4">
          <label for="company" class="form-label">Company</label>
          <input type="text" id="company" class="form-control shadow-sm" placeholder="Search by Company">
        </div>

        <div class="col-md-4">
          <label for="search" class="form-label">Search</label>
          <input type="text" id="search" class="form-control shadow-sm" placeholder="Search name or email">
        </div>
      </form>

      <div class="table-container mt-4">
        <table class="table table-hover table-striped table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Status</th>
              <th>Company</th>
              <th>Created Date</th>
            </tr>
          </thead>
          <tbody id="contacts-body">
            <!-- Contacts will be rendered here -->
          </tbody>
        </table>
      </div>

      <div class="mt-4 d-flex justify-content-center">
        <nav>
          <ul class="pagination" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>

  <script>
    const contacts = Array.from({ length: 120 }, (_, i) => ({
      name: `Contact ${i + 1}`,
      email: `user${i + 1}@example.com`,
      status: i % 2 === 0 ? 'active' : 'inactive',
      company: `Company ${i % 5 + 1}`,
      created: new Date(2024, i % 12, (i % 28) + 1).toISOString().split('T')[0]
    }));

    let filteredContacts = [...contacts];
    let currentPage = 1;
    const pageSize = 10;

    document.querySelectorAll('#filter-form input, #filter-form select').forEach(input => {
      input.addEventListener('input', applyFilters);
    });

    function applyFilters() {
      const status = document.getElementById('status').value.toLowerCase();
      const company = document.getElementById('company').value.toLowerCase();
      const search = document.getElementById('search').value.toLowerCase();

      filteredContacts = contacts.filter(contact => {
        return (
          (!status || contact.status.toLowerCase() === status) &&
          (!company || contact.company.toLowerCase().includes(company)) &&
          (!search || contact.name.toLowerCase().includes(search) || contact.email.toLowerCase().includes(search))
        );
      });

      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function renderTable() {
      const tbody = document.getElementById('contacts-body');
      tbody.innerHTML = '';

      const start = (currentPage - 1) * pageSize;
      const end = start + pageSize;
      const pageItems = filteredContacts.slice(start, end);

      for (let contact of pageItems) {
        const row = `<tr>
          <td>${contact.name}</td>
          <td>${contact.email}</td>
          <td><span class="badge bg-${contact.status === 'active' ? 'success' : 'secondary'}">${contact.status}</span></td>
          <td>${contact.company}</td>
          <td>${contact.created}</td>
        </tr>`;
        tbody.innerHTML += row;
      }
    }

    function renderPagination() {
      const totalPages = Math.ceil(filteredContacts.length / pageSize);
      const pagination = document.getElementById('pagination');
      pagination.innerHTML = '';

      for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener('click', e => {
          e.preventDefault();
          currentPage = i;
          renderTable();
          renderPagination();
        });
        pagination.appendChild(li);
      }
    }

    function exportToCSV() {
      const rows = [
        ["Name", "Email", "Status", "Company", "Created Date"],
        ...filteredContacts.map(c => [c.name, c.email, c.status, c.company, c.created])
      ];
      const csvContent = rows.map(e => e.join(",")).join("\n");
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      const link = document.createElement("a");
      link.setAttribute("href", URL.createObjectURL(blob));
      link.setAttribute("download", "contacts.csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    applyFilters();
  </script>
</body>

</html>