<?php
    // Add this PHP block at the very top of your branch view file
    $csrf = [
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة مواقع الفروع</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --text-light: #fff;
            --text-dark: #343a40;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: grad 20s ease infinite;
            color: var(--text-dark);
            position: relative;
        }
        
        @keyframes grad {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .particles { position: fixed; inset: 0; overflow: hidden; z-index: -1; }
        .particle { position: absolute; background: rgba(255, 140, 0, 0.1); clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); animation: float 25s infinite ease-in-out; opacity: 0; filter: blur(2px); }
        .particle:nth-child(even) { background: rgba(0, 31, 63, 0.1); }
        .particle:nth-child(1) { width: 40px; height: 40px; left: 10%; top: 20%; animation-duration: 18s; }
        .particle:nth-child(2) { width: 70px; height: 70px; left: 25%; top: 50%; animation-duration: 22s; animation-delay: 2s; }
        @keyframes float {
            0% { transform: translateY(0) translateX(0) rotate(0); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(50px) rotate(360deg); opacity: 0; }
        }
        
        #loading-screen { position: fixed; inset: 0; background: #001f3f; z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column; transition: opacity 0.5s; }
        .loader { width: 50px; height: 50px; border: 5px solid rgba(255, 255, 255, 0.3); border-top: 5px solid var(--marsom-orange); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .main-container { padding: 30px 15px; visibility: hidden; opacity: 0; transition: opacity 0.5s; position: relative; z-index: 1; }
        .page-title { font-family: 'El Messiri', sans-serif; font-weight: 700; font-size: 2.8rem; color: var(--text-light); margin-bottom: 32px; text-align: center; position: relative; display: inline-block; padding-bottom: 10px; text-shadow: 0 3px 6px rgba(0, 0, 0, 0.4); }
        .page-title::after { content: ''; position: absolute; width: 100px; height: 4px; background: linear-gradient(90deg, var(--marsom-blue), var(--marsom-orange)); bottom: 0; left: 50%; transform: translateX(-50%); border-radius: 2px; }
        
        .content-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); padding: 25px; }
        .table thead th { background-color: #001f3f !important; color: #fff; text-align: center; vertical-align: middle; border-bottom: 2px solid #00152b; }
        .table tbody td { text-align: center; vertical-align: middle; font-size: 14px; }
        .table tbody tr:hover { background-color: rgba(0, 31, 63, 0.05); }
        
        .top-actions { position: fixed; top: 12px; right: 12px; display: flex; gap: 10px; z-index: 5; }
        .top-actions a { background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; text-decoration: none; border-radius: 10px; padding: 8px 14px; display: inline-flex; align-items: center; gap: 8px; transition: 0.25s; }
        .top-actions a:hover { background: rgba(255, 255, 255, 0.2); color: var(--marsom-orange); }
        
        #map { height: 400px; border-radius: 10px; margin-bottom: 20px; border: 2px solid #001f3f; background: #eee; }
        .map-controls { position: absolute; top: 10px; left: 10px; z-index: 1000; display: flex; flex-direction: column; gap: 5px; }
        .map-controls button { background: white; border: none; border-radius: 5px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); cursor: pointer; transition: all 0.3s; }
        .map-controls button:hover { background: #f0f0f0; }

        /* This is the new API key warning style */
        .api-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }
        #map-container {
    min-height: 400px;
}

#map {
    width: 100%;
    height: 100%;
    min-height: 400px;
}
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div id="loading-screen">
        <div class="loader"></div>
        <h3 style="color: #fff">جاري التحميل ...</h3>
    </div>

    <div class="top-actions">
        <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
        <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
    </div>

    <div class="main-container container-fluid">
        <div class="text-center">
            <h1 class="page-title">إدارة مواقع الفروع</h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card content-card">
                    <div class="card-body">
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                            <span id="success-message"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                            <span id="error-message"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <h4><i class="fas fa-plus-circle text-primary me-2"></i>إضافة فرع جديد</h4>
                        <p class="text-muted">انقر على الخريطة لتحديد موقع الفرع.</p>
                        
                        <div id="api-key-section"></div>

                        <div class="mb-3">
                            <input id="location-search" class="form-control" type="text" placeholder="ابحث عن موقع (مثال: الرياض، جدة...)"/>
                        </div>
                        
                        <div id="map-container" class="mb-3 position-relative">
                            <div id="map"></div>
                            <div class="map-controls">
                                <button id="zoom-in" title="تكبير"><i class="fas fa-plus"></i></button>
                                <button id="zoom-out" title="تصغير"><i class="fas fa-minus"></i></button>
                                <button id="reset-view" title="إعادة تعيين"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </div>

                        <form id="branch-form">
                            <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>" id="csrf-token">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="branch_name" class="form-label fw-bold">اسم الفرع</label>
                                    <input type="text" class="form-control" id="branch_name" name="branch_name" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="latitude" class="form-label fw-bold">خط العرض (Latitude)</label>
                                    <input type="text" class="form-control bg-light" id="latitude" name="latitude" value="24.7136" readonly required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="longitude" class="form-label fw-bold">خط الطول (Longitude)</label>
                                    <input type="text" class="form-control bg-light" id="longitude" name="longitude" value="46.6753" readonly required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4" id="save-branch-btn">
                                <i class="fas fa-save me-2"></i> حفظ الفرع
                            </button>
                        </form>

                        <hr class="my-4">

                        <h4><i class="fas fa-list-ul text-primary me-2"></i>الفروع الحالية</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>اسم الفرع</th>
                                        <th>خط العرض</th>
                                        <th>خط الطول</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="branches-table">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="ms-2">جاري تحميل الفروع...</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
   <script>
    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCVZ9dZqjeFsbXcOrppGqDtCFUfAQz2PSc&libraries=places&language=ar&region=SA&callback=initializeGoogleMap';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
    
    // Load Google Maps API after the page loads
    window.addEventListener('load', loadGoogleMapsAPI);
</script>
    <script>
        // Global variables
        let map;
        let markers = {}; // Use an object to map markers by branch ID
        let geocoder;
        let autocomplete;
        let branches = []; // This will hold the branches from the server
        let csrfName = '<?php echo $csrf['name']; ?>';
        let csrfHash = '<?php echo $csrf['hash']; ?>';
        let tempMarker = null; // A single marker for the user's selection

        /**
         * This function is CALLED BY the Google Maps script tag above (using &callback=initializeGoogleMap)
         * This is why the old API key input field is no longer needed.
         */
        function initializeGoogleMap() {
            console.log('Google Maps API loaded successfully.');
            
            try {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 24.7136, lng: 46.6753 }, // Riyadh
                    zoom: 6,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                    zoomControl: false,
                    styles: [ { "featureType": "all", "elementType": "geometry", "stylers": [ { "color": "#f5f5f5" } ] }, { "featureType": "all", "elementType": "labels.text.fill", "stylers": [ { "gamma": 0.01 }, { "lightness": 20 } ] }, { "featureType": "all", "elementType": "labels.text.stroke", "stylers": [ { "saturation": -31 }, { "lightness": -33 }, { "weight": 2 }, { "gamma": 0.8 } ] }, { "featureType": "all", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ] }, { "featureType": "landscape", "elementType": "geometry", "stylers": [ { "lightness": 30 }, { "saturation": 30 } ] }, { "featureType": "poi", "elementType": "geometry", "stylers": [ { "saturation": 20 } ] }, { "featureType": "poi.park", "elementType": "geometry", "stylers": [ { "lightness": 20 }, { "saturation": -20 } ] }, { "featureType": "road", "elementType": "geometry", "stylers": [ { "lightness": 10 }, { "saturation": -30 } ] }, { "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "saturation": 25 }, { "lightness": 25 } ] }, { "featureType": "water", "elementType": "all", "stylers": [ { "lightness": -20 } ] } ]
                });
                
                geocoder = new google.maps.Geocoder();
                
                autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById('location-search'),
                    { types: ['geocode'], componentRestrictions: {country: 'sa'} }
                );
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        showAlert('لم يتم العثور على الموقع المحدد', 'error');
                        return;
                    }
                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    addTemporaryMarker(lat, lng);
                    showAlert(`تم تحديد موقع ${place.name} بنجاح`, 'success');
                });
                
                map.addListener('click', function(event) {
                    const lat = event.latLng.lat();
                    const lng = event.latLng.lng();
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    addTemporaryMarker(lat, lng);
                    
                    geocoder.geocode({location: event.latLng}, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            showAlert(`تم تحديد الموقع: ${results[0].formatted_address}`, 'success');
                        } else {
                            showAlert(`تم تحديد الموقع: ${lat.toFixed(6)}, ${lng.toFixed(6)}`, 'success');
                        }
                    });
                });
                
                // Add map controls
                document.getElementById('zoom-in').addEventListener('click', () => map.setZoom(map.getZoom() + 1));
                document.getElementById('zoom-out').addEventListener('click', () => map.setZoom(map.getZoom() - 1));
                document.getElementById('reset-view').addEventListener('click', () => {
                    map.setCenter({ lat: 24.7136, lng: 46.6753 });
                    map.setZoom(6);
                });
                
                // Now that map is ready, load branches
                loadExistingBranches();
                
            } catch (error) {
                console.error('Error initializing Google Maps:', error);
                document.getElementById('map-container').innerHTML = '<p class="text-danger text-center">فشل تحميل الخريطة. يرجى مراجعة وحدة التحكم.</p>';
            }
        }

        /**
         * This is a global error handler for the Maps API script.
         * It will show an error if your API key is wrong.
         */
        window.gm_authFailure = () => {
            console.error('Google Maps API Key Authentication Failure.');
            document.getElementById('api-key-section').innerHTML = `
                <div class="api-warning" style="display:block;">
                    <h5><i class="fas fa-exclamation-triangle text-danger me-2"></i>فشل في مصادقة مفتاح API</h5>
                    <p class="mb-0">المفتاح الذي أدخلته غير صالح أو أن النطاق غير مصرح له. يرجى التحقق من مفتاحك في Google Cloud Console.</p>
                </div>`;
            document.getElementById('map-container').innerHTML = '<p class="text-danger text-center">فشل تحميل الخريطة.</p>';
            loadExistingBranches(); // Load table anyway
        };

        // Enhanced loading screen logic
        document.addEventListener('DOMContentLoaded', function() {
            const loading = document.getElementById('loading-screen');
            const main = document.querySelector('.main-container');
            
            function hideLoadingScreen() {
                if (loading) {
                    loading.style.opacity = '0';
                    setTimeout(() => { 
                        loading.style.display = 'none'; 
                        document.body.style.overflow = 'auto'; 
                        if (main) {
                            main.style.visibility = 'visible'; 
                            main.style.opacity = '1'; 
                        }
                    }, 400);
                }
            }
            
            window.addEventListener('load', hideLoadingScreen);
            setTimeout(hideLoadingScreen, 3000); // Fallback

            // Attach form submit listener
            document.getElementById('branch-form').addEventListener('submit', addNewBranch);
            
            // This function is defined *outside* initializeGoogleMap
            // so it can run even if the map fails to load.
            loadExistingBranches();
        });

        // --- NEW AJAX FUNCTIONS ---

        /**
         * Updates the CSRF token on the page after an AJAX request.
         */
        function updateCsrfToken(newHash) {
            csrfHash = newHash;
            const tokenInput = document.getElementById('csrf-token');
            if (tokenInput) {
                tokenInput.value = newHash;
            }
            csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>"; // Reset CSRF name
        }

        /**
         * Fetches the list of branches from the server.
         */
        async function loadExistingBranches() {
            try {
                // This calls the 'get_branches_ajax' function you added to Users2.php
                const response = await fetch('<?php echo site_url("users2/get_branches_ajax"); ?>');
                const result = await response.json();

                if (result.status === 'success') {
                    branches = result.branches; // Save to global variable
                    updateBranchesTable();
                    if (map) { // Check if map is initialized
                        addExistingBranchesToMap();
                    }
                } else {
                    showAlert('فشل تحميل الفروع: ' + result.message, 'error');
                    document.getElementById('branches-table').innerHTML = '<tr><td colspan="5" class="text-center text-danger">فشل تحميل الفروع.</td></tr>';
                }
            } catch (error) {
                console.error('Error fetching branches:', error);
                showAlert('فشل الاتصال بالخادم لتحميل الفروع.', 'error');
                document.getElementById('branches-table').innerHTML = '<tr><td colspan="5" class="text-center text-danger">فشل الاتصال بالخادم.</td></tr>';
            }
        }

        /**
         * Handles the form submission to add a new branch.
         */
        async function addNewBranch(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('save-branch-btn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';

            const branchName = document.getElementById('branch_name').value.trim();
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            
            if (!branchName || !latitude || !longitude) {
                showAlert('يرجى إدخال اسم الفرع وتحديد موقعه على الخريطة.', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i> حفظ الفرع';
                return;
            }

            const formData = new FormData();
            formData.append('branch_name', branchName);
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
            formData.append(csrfName, csrfHash);

            try {
                // This calls the 'save_branch' function you added to Users2.php
                const response = await fetch('<?php echo site_url("users2/save_branch"); ?>', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                
                const result = await response.json();
                
                // Update CSRF token after every request
                if (result.csrf_hash) {
                    updateCsrfToken(result.csrf_hash);
                }

                if (result.status === 'success') {
                    showAlert('تم إضافة الفرع بنجاح!', 'success');
                    document.getElementById('branch-form').reset();
                    if(tempMarker) tempMarker.setMap(null); // Remove temp marker
                    loadExistingBranches(); // Reload all branches from server
                } else {
                    showAlert('فشل إضافة الفرع: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error adding branch:', error);
                showAlert('فشل الاتصال بالخادم لإضافة الفرع.', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i> حفظ الفرع';
            }
        }

        /**
         * Deletes a branch by its ID.
         */
        async function deleteBranch(id) {
            if (!confirm('هل أنت متأكد من حذف هذا الفرع؟')) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append(csrfName, csrfHash);

            try {
                // This calls the 'delete_branch_ajax' function you added to Users2.php
                const response = await fetch('<?php echo site_url("users2/delete_branch_ajax"); ?>', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                
                const result = await response.json();

                if (result.csrf_hash) {
                    updateCsrfToken(result.csrf_hash);
                }

                if (result.status === 'success') {
                    showAlert('تم حذف الفرع بنجاح.', 'success');
                    loadExistingBranches(); // Reload all branches
                } else {
                    showAlert('فشل حذف الفرع: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting branch:', error);
                showAlert('فشل الاتصال بالخادم لحذف الفرع.', 'error');
            }
        }

        /**
         * Updates the HTML table with the current list of branches.
         */
        function updateBranchesTable() {
            const tableBody = document.getElementById('branches-table');
            
            if (branches.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">لا توجد فروع مضافة حالياً.</td></tr>';
                return;
            }
            
            tableBody.innerHTML = branches.map(branch => `
                <tr>
                    <td>${branch.id}</td>
                    <td>${branch.branch_name}</td>
                    <td>${branch.latitude}</td>
                    <td>${branch.longitude}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteBranch(${branch.id})">
                            <i class="fas fa-trash me-1"></i> حذف
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        /**
         * Clears and re-adds all markers for existing branches to the map.
         */
        function addExistingBranchesToMap() {
            // Clear existing markers
            Object.values(markers).forEach(marker => marker.setMap(null));
            markers = {};

            if (!map) return; // Don't run if map failed to load

            branches.forEach(branch => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(branch.latitude), lng: parseFloat(branch.longitude) },
                    map: map,
                    title: branch.branch_name,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                            <svg width="30" height="40" viewBox="0 0 30 40" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#001f3f" d="M15 0C6.716 0 0 6.716 0 15c0 11.25 15 25 15 25s15-13.75 15-25c0-8.284-6.716-15-15-15z"/>
                                <circle cx="15" cy="15" r="8" fill="white"/>
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(30, 40),
                        anchor: new google.maps.Point(15, 40)
                    }
                });
                
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div class="p-2 text-dark"><h6 class="mb-1">${branch.branch_name}</h6></div>`
                });
                
                marker.addListener('click', () => infoWindow.open(map, marker));
                markers[branch.id] = marker;
            });
        }
        
        /**
         * Places a single, temporary orange marker on the map.
         */
        function addTemporaryMarker(lat, lng) {
            // Clear the *previous* temporary marker
            if (tempMarker) {
                tempMarker.setMap(null);
            }
            
            if (!map) return; // Don't run if map failed to load
            
            tempMarker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                title: 'الموقع المحدد',
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                        <svg width="30" height="40" viewBox="0 0 30 40" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#FF8C00" d="M15 0C6.716 0 0 6.716 0 15c0 11.25 15 25 15 25s15-13.75 15-25c0-8.284-6.716-15-15-15z"/>
                            <circle cx="15" cy="15" r="8" fill="white"/>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(30, 40),
                    anchor: new google.maps.Point(15, 40)
                }
            });
        }

        /**
         * Shows a Bootstrap alert at the top of the page.
         */
        function showAlert(message, type) {
            const successAlert = document.querySelector('.alert-success');
            const errorAlert = document.querySelector('.alert-danger');
            
            if (type === 'success') {
                document.getElementById('success-message').textContent = message;
                successAlert.style.display = 'block';
                errorAlert.style.display = 'none';
            } else {
                document.getElementById('error-message').textContent = message;
                errorAlert.style.display = 'block';
                successAlert.style.display = 'none';
            }
            
            setTimeout(() => {
                successAlert.style.display = 'none';
                errorAlert.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>