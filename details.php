<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>List Your Property - Luxury Getaways</title>
    <link rel="stylesheet" href="styles.css" />
    <?php include 'Header.php'; ?>
    <?php include 'login_form.php'; ?>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        main {
            width: 40%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        textarea {
            resize: none;
            overflow-y: hidden;
            min-height: 100px;
            max-height: 300px;
            width: calc(100% - 16px);
            box-sizing: border-box;
        }
        .form-group {
            display: flex;
            gap: 10px;
        }
        .form-group input {
            flex: 1;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        #Pogasubmit {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #5c7f4a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        #file-input {
            display: block;
            margin-top: 10px;
        }
        /* Map Styling */
        #map {
            height: 400px;
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>

    <!-- Leaflet.js for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>
<body>

<main>
    <h1>Tell us about yourself!</h1>
    <form action="submit_listing.php" method="POST" enctype="multipart/form-data">
        <!-- Personal Information -->
        <div class="form-group">
            <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="form-group">
            <input type="tel" id="phone" name="phone" placeholder="Phone" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>

        <!-- Property Address Information -->
        <h2>Tell us about your property</h2>
        <input type="text" id="property_name" name="property_name" placeholder="Property Name" required>
        <input type="text" id="street_address" name="street_address" placeholder="Property Street Address">
        <div class="form-group">
            <input type="text" id="city" name="city" placeholder="City" required>
            <input type="text" id="postal_code" name="postal_code" placeholder="Postal Code">
        </div>

        <div id="map"></div>
        <label for="price">Price</label>
        <input type="number" id="price" name="price" placeholder="Price per Night (€)" required>
        <label for="guests">Additional information</label>
        <input type="number" id="guests" name="guests" min="1" placeholder="Max Guests" required>
        <input type="number" id="bedrooms" name="bedrooms" min="1" placeholder="Bedrooms" required>
        <input type="number" id="bathrooms" name="bathrooms" min="1" step="0.5" placeholder="Bathrooms" required>

        <label for="property_type">Property Type</label>
        <select id="property_type" name="property_type" required>
            <option value="" disabled selected>Select property type</option>
            <option value="house">House</option>
            <option value="villa">Villa</option>
            <option value="cabin">Cabin</option>
            <option value="apartment">Apartment</option>
        </select>
        
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="Short description" required oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>

        <label for="file-input">Upload Property Photos</label>
        <input type="file" id="file-input" name="image[]" accept="image/*" multiple required>

        

        <button id="Pogasubmit" type="submit">Submit Listing</button>
    </form>
</main>

<script>
    let map = L.map('map').setView([56.8796, 24.6032], 6); // Default: Germany
    let marker = L.marker([56.8796, 24.6032]).addTo(map);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    function updateMap(query) {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let lat = parseFloat(data[0].lat);
                    let lon = parseFloat(data[0].lon);
                    map.setView([lat, lon], 13);
                    marker.setLatLng([lat, lon]);
                }
            })
            .catch(error => console.error('Error fetching location:', error));
    }

    function handleLocationUpdate() {
        let city = document.getElementById('city').value.trim();
        let street = document.getElementById('street_address').value.trim();

        if (street.length > 2 && city.length > 2) {
            updateMap(`${street}, ${city}`);
        } else if (city.length > 2) {
            updateMap(city);
        }
    }

    // Debounce function to avoid too many API requests
    function debounce(func, delay) {
        let timer;
        return function() {
            clearTimeout(timer);
            timer = setTimeout(func, delay);
        };
    }

    // Event listeners with debounce
    document.getElementById('city').addEventListener('input', debounce(handleLocationUpdate, 500));
    document.getElementById('street_address').addEventListener('input', debounce(handleLocationUpdate, 500));
</script>


</body>
</html>
