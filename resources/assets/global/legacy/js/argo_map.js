var MapApp = angular.module("ArgoMap",[]);

MapApp.config(function($interpolateProvider, $httpProvider, $logProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
});

MapApp.component('argoMap', {
    templateUrl: window.constants.ABS_URI + 'partials/map.html',
    bindings: { data: '<'},
    controller: function() {

        this.mapObj = L.map('leafletMap');

        this.MapModel = {
            basemap: L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 28
            }),
            center: L.latLng(1, -1),
            zoom: {
                defaultLevel: 7,
                position: 'topright'
            },
            customizeIcon: L.icon({
                iconUrl: window.constants.ABS_URI + 'images/icon/pin.png',
                iconSize:     [20, 20],
                popupAnchor:  [0, -10]
            }),
        };

        this.initMoal = function(centerObj){
            this.MapModel.center = L.latLng(centerObj.lat, centerObj.lng);
            this.MapModel.zoom.defaultLevel = centerObj.zoom;
        };

        this.initMap = function(){
            this.mapObj.setView(this.MapModel.center, this.MapModel.zoom.defaultLevel);
            this.mapObj.zoomControl.setPosition(this.MapModel.zoom.position);
            this.MapModel.basemap.addTo(this.mapObj);
        };

        this.setMarkers = function(markers){
            var that = this;
            var markers_arr = [];
            markers.forEach(function(marker) {
                var marker = that.createMarker([marker.lat, marker.lng]);
                this.push(marker);
            }, markers_arr);
            projectLayerGroup = L.layerGroup(markers_arr).addTo(this.mapObj);
            this.layerControl = L.control.layers().addTo(this.mapObj);
            this.layerControl.addOverlay(projectLayerGroup, "project");

        };

        this.setTrackers = function(trackers){
            var that = this;
            var trackers_path = [];
            that.speedColorTracker = {};
            trackers.forEach(function(tracker, index){
                var polyline = that.createPolyLine(tracker, that);
                this.push(polyline);
            },trackers_path);
            that.showSpeedLegend = true;
            that.trackerLayerGroup = L.featureGroup(trackers_path).addTo(that.mapObj);
            that.layerControl.addOverlay(that.trackerLayerGroup, "tracker");
        };

        this.createPolyLine = function(tracker, that){
            var coordinate_arr = [];
            tracker.coordinates.forEach(function(coord, index){
                this.push([coord[0], coord[1]]);
            },coordinate_arr);

            var polyline = L.polyline(coordinate_arr,
                {
                    color: '#217c27',
                    weight: 4,
                    opacity: 1
                });

            polyline.on('click', function(e) {
                if(Object.keys(that.speedColorTracker).length !== 0){
                    that.trackerLayerGroup.removeLayer(that.speedColorTracker.id);
                    var polyline = that.createPolyLine(that.speedColorTracker.object, that);
                    that.trackerLayerGroup.addLayer(polyline);
                }
                var polyline_arr =[];
                for (var i = 1; i <=this.trackerObj.coordinates.length - 1; i++) {
                    var latlngs = [[this.trackerObj.coordinates[i-1][1], this.trackerObj.coordinates[i-1][0]],
                                   [this.trackerObj.coordinates[i][1], this.trackerObj.coordinates[i][0]]];

                    var distance = Math.sqrt(
                        Math.pow(latlngs[0][0] - latlngs[1][0], 2) +
                        Math.pow(latlngs[0][1] - latlngs[1][1], 2)
                    );

                    distance *= 111;

                    var time = this.trackerObj.coordinates[i][2] - this.trackerObj.coordinates[i - 1][2];
                    if (time > 0) {
                        var speed = distance / time * 3600;
                        var state ={
                            "type": "Feature",
                            "properties": {"speed": speed},
                            "geometry": {
                                "type": "LineString",
                                "coordinates": latlngs
                        }};
                        polyline_arr.push(state);
                    }
                };

                var speedPolyline = L.geoJson(polyline_arr, {
                        style: function(feature) {
                            var speed = feature.properties.speed;
                            if (speed < 20) {
                                speedColor = '#FF0000';
                            } else if (speed < 40) {
                                speedColor = '#FF9200';
                            } else if (speed < 60) {
                                speedColor = '#FAFF00';
                            } else if (speed < 80) {
                                speedColor = '#71F829';
                            } else if (speed >= 80) {
                                speedColor = '#1fe6ff';
                            }
                            return {"color": speedColor, "opacity": 1};
                        }});
                that.trackerLayerGroup.removeLayer(this.polylineObj._leaflet_id);
                that.trackerLayerGroup.addLayer(speedPolyline);

                that.speedColorTracker['id'] = speedPolyline._leaflet_id;
                that.speedColorTracker['object'] = this.trackerObj;
            },({polylineObj: polyline,
                trackerObj: tracker}));

            return polyline
        };

        this.createMarker = function(coords){
            var markerOptions = {
                icon: this.MapModel.customizeIcon,
                clickable: false
            };
            var marker = new L.marker(coords, markerOptions);

            return marker
        };

        this.createLayer = function(){
            var layerOptions = {
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 12
            };
            var layer = new L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',layerOptions);

            return layer;
        };

        this.$onInit = function(argument) {
            this.showSpeedLegend = false;
        };

        this.$onChanges = function(changes){
            if(changes.data.currentValue){
                this.initMoal(changes.data.currentValue.center);
                this.initMap();
                if(changes.data.currentValue.markers.length > 0){
                    this.setMarkers(changes.data.currentValue.markers);
                }
                if(changes.data.currentValue.tracker.length > 0){
                    this.setTrackers(changes.data.currentValue.tracker);
                }
            }
        };

    }
});
