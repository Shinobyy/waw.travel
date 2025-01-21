    window.initMap = async function() {
        try {
            const position = { lat: 51.5074, lng: 0.1278 };

            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 2,
                center: position,
                mapId: "fa7b1aadc11c7224",
                mapTypeId: 'satellite',
                mapTypeControl: false,
                fullscreenControl: false,
                linksControl: false,
                panControl: false,
                addressControl: false,
                enableCloseButton: false,
                scaleControl: false,
                navigationControl: false,
                streetViewControl: false,
            });

            const coordinates = [];

            checkpoints.forEach(checkpoint => {
                const [lat, lng] = checkpoint.split(',').map(Number);
                if (!isNaN(lat) && !isNaN(lng)) {
                    const position = { lat, lng };
                    coordinates.push(position);
                    new google.maps.marker.AdvancedMarkerElement({
                        map,
                        position,
                        title: "Checkpoint",
                    });
                } else {
                    console.warn("Invalid checkpoint data:", checkpoint);
                }
            });

            if (coordinates.length > 1) {
                const polyline = new google.maps.Polyline({
                    path: coordinates,
                    geodesic: false, // Set to false to draw straight lines
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 1.5,
                });
                polyline.setMap(map);
            }
        } catch (error) {
            console.error("Error initializing the map:", error);
        }
    }

    document.addEventListener("turbo:load", function() {
        if (document.getElementById('map')) {
            initMap();
        }
    })