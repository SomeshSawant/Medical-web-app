<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Medical web app — Medical Case Management System') }}</title>

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons 1.11.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Medical Web App Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/medical-app.css') }}">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Desktop Sidebar Navigation -->
        <aside class="jm-sidebar d-none d-lg-flex flex-column flex-shrink-0">
            <a href="{{ route('dashboard') }}" class="jm-sidebar-brand">
                <div class="jm-brand-icon">
                    <i class="bi bi-hospital"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1 text-white fs-6">Medical web app</div>
                    <small class="text-white-50" style="font-size: 0.72rem;">Case Management</small>
                </div>
            </a>

            <div class="nav-heading">Clinical Navigation</div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cases.index') }}" class="nav-link {{ request()->routeIs('cases.index', 'cases.show', 'cases.edit') ? 'active' : '' }}">
                        <i class="bi bi-folder2-open"></i>
                        <span>Medical Cases</span>
                    </a>
                </li>
                @can('create', App\Models\MedicalCase::class)
                    <li class="nav-item">
                        <a href="{{ route('cases.create') }}" class="nav-link {{ request()->routeIs('cases.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i>
                            <span>Create New Case</span>
                        </a>
                    </li>
                @endcan
            </ul>

            <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-white-50" style="font-size: 0.7rem;">SIGNED IN ROLE</small>
                    @php
                        $userRole = Auth::user()?->role;
                        $roleBadge = match($userRole) {
                            'admin' => 'bg-danger text-white',
                            'engineer' => 'bg-info text-dark',
                            default => 'bg-success text-white',
                        };
                    @endphp
                    <span class="badge {{ $roleBadge }} text-uppercase fw-semibold" style="font-size: 0.65rem;">
                        {{ $userRole ?? 'Viewer' }}
                    </span>
                </div>
                <div class="text-truncate fw-medium text-white small">{{ Auth::user()?->name }}</div>
                <div class="text-truncate text-white-50" style="font-size: 0.75rem;">{{ Auth::user()?->email }}</div>
            </div>
        </aside>

        <!-- Mobile Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start bg-dark text-white jm-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
                <div class="d-flex align-items-center gap-2">
                    <div class="jm-brand-icon">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white fs-6">Medical web app</div>
                        <small class="text-white-50" style="font-size: 0.72rem;">Case Management</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0 d-flex flex-column">
                <div class="nav-heading">Clinical Navigation</div>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('cases.index') }}" class="nav-link {{ request()->routeIs('cases.index', 'cases.show', 'cases.edit') ? 'active' : '' }}">
                            <i class="bi bi-folder2-open"></i>
                            <span>Medical Cases</span>
                        </a>
                    </li>
                    @can('create', App\Models\MedicalCase::class)
                        <li class="nav-item">
                            <a href="{{ route('cases.create') }}" class="nav-link {{ request()->routeIs('cases.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle"></i>
                                <span>Create New Case</span>
                            </a>
                        </li>
                    @endcan
                </ul>

                <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="text-white-50" style="font-size: 0.7rem;">CURRENT USER</small>
                        <span class="badge {{ $roleBadge }} text-uppercase fw-semibold" style="font-size: 0.65rem;">
                            {{ $userRole ?? 'Viewer' }}
                        </span>
                    </div>
                    <div class="text-truncate fw-medium text-white small">{{ Auth::user()?->name }}</div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100">
                            <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Wrapper -->
        <div class="jm-main-wrapper">
            <!-- Topbar Header -->
            <header class="jm-topbar sticky-top">
                <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Mobile Hamburger Button -->
                        <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Toggle navigation">
                            <i class="bi bi-list fs-5"></i>
                        </button>

                        <div>
                            @isset($header)
                                {{ $header }}
                            @else
                                <h1 class="h5 mb-0 fw-bold text-dark">Medical web app</h1>
                            @endisset
                        </div>
                    </div>

                    <!-- User Information & Profile Dropdown -->
                    <div class="d-flex align-items-center gap-2">
                        <!-- Role Badge -->
                        <span class="badge {{ $roleBadge }} text-uppercase px-2.5 py-1.5 fw-semibold d-none d-sm-inline-block">
                            <i class="bi bi-shield-check me-1"></i>{{ ucfirst($userRole ?? 'Viewer') }}
                        </span>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                    {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline small fw-semibold text-dark">{{ Auth::user()?->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border" aria-labelledby="userMenuButton" style="min-width: 200px;">
                                <li class="px-3 py-2 border-bottom bg-light">
                                    <div class="small fw-bold text-dark text-truncate">{{ Auth::user()?->name }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ Auth::user()?->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person me-2 text-muted"></i> Account Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 small text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-grow-1 p-3 p-md-4">
                <div class="container-fluid px-0">
                    <!-- Flash Success Notification -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Flash Error Notification -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-5 me-2"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Validation Error Summary -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                            <div class="d-flex align-items-center mb-1 fw-bold">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Please correct the following errors:
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Page Content Slot -->
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
                <div class="container-fluid px-3">
                    <span>&copy; {{ date('Y') }} Medical web app &bull; Medical Case Management System</span>
                    <span class="mx-2">&bull;</span>
                    <span>Medical Device Implant Tracking</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Global Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCaseModal" tabindex="-1" aria-labelledby="deleteCaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fs-6" id="deleteCaseModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Case Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="text-danger mb-3">
                        <i class="bi bi-trash3-fill" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Are you sure you want to delete this case?</h6>
                    <p class="text-muted small mb-0">
                        You are about to permanently delete case <strong id="modalCaseNumberDisplay" class="text-dark">#CASE</strong>. This action is irreversible and removes the clinical case from the registry.
                    </p>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="modalDeleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger px-3">
                            <i class="bi bi-trash3 me-1"></i> Delete Case
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        // Setup Delete Confirmation Modal Dynamically
        document.addEventListener('DOMContentLoaded', function () {
            const deleteModal = document.getElementById('deleteCaseModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (button) {
                        const caseNumber = button.getAttribute('data-case-number') || 'this case';
                        const actionUrl = button.getAttribute('data-action-url') || '';
                        
                        document.getElementById('modalCaseNumberDisplay').textContent = caseNumber;
                        document.getElementById('modalDeleteForm').setAttribute('action', actionUrl);
                    }
                });
            }
        });
    </script>
</body>
</html>
