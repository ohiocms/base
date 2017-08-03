<template>
    <div>
        <div class="btn-group">
            <button class="btn btn-default" @click.prevent="centerSpot()"><i class="fa fa-arrows"></i></button>
        </div>
        <div class="coordinates" ref="map">... Map Loading ...</div>
    </div>
</template>

<script>
import loadGoogleMapsAPI from 'load-google-maps-api';

let gMap = null;
let dragSpot = null;

export default {
  name: 'coordinates-map',
  props: ['lat', 'lng', 'type', 'level', 'zoom'],
  data () {
    return {
      center: '',
      dragSpot: null,
      gMap: null,
    }
  },
  mounted () {
    // Fetch google maps API
    loadGoogleMapsAPI({ key: config('gmaps_api_key')})
      .then(this.initMap)
      .catch((err) => console.error(err));
  }, 
  methods: {

    centerSpot() {
        this.dragSpot.setPosition(this.gMap.getCenter());
    },

    /**
     * Initilize Map
     */

    initMap () {
        // Get zoom from parameter or config
        let zoom = parseInt(this.zoom ? this.zoom : config('zoom', 15));
  
        this.center = new google.maps.LatLng(config('lat'), config('lng'));

        this.gMap = new google.maps.Map(this.$refs.map , {
          center: this.center,
          zoom: zoom,
          disableDefaultUI: false,
          gestureHandling: 'cooperative',
          scrollwheel: false,
        });

        // Set marker default location OR passed in coordinates
        let dragSpotLocation = {}
        if (this.lat && this.lng) {
          dragSpotLocation = new google.maps.LatLng(this.lat, this.lng)
        } else {
          dragSpotLocation = this.center
        }

        // Create draggable marker
        this.dragSpot = new google.maps.Marker({
          position: dragSpotLocation,
          map: this.gMap,
          draggable: true,
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            strokeOpacity: 0.0,
            strokeColor: 'red',
            fillOpacity: 1.0,
            fillColor: 'red'
          }
        });

        // Add listener, emit events and coords changed
        const _vue = this;
        google.maps.event.addListener(this.dragSpot, 'dragend', function (event) {
           _vue.$emit('spot-updated', {
             lat: this.getPosition().lat(),
             lng: this.getPosition().lng()
           })
        });

        return Promise.resolve();
    },

    /** 
     * Update Dragspot Location
     */

    updateDragSpotLocation () {
      if (this.dragSpot && this.gMap) {
        const updatedPosition = new google.maps.LatLng(this.lat, this.lng)
        this.dragSpot.setPosition(updatedPosition)
        this.gMap.setCenter(updatedPosition)
      }
    },

  },
  watch: {
    lat: function (val) {
      this.updateDragSpotLocation()
    },
    lng: function (val) {
      this.updateDragSpotLocation()
    }
  }
}

/**
 * Helper Funtions
 */

function iconDefault () {
  return {
    path: google.maps.SymbolPath.CIRCLE,
    scale: 8,
    strokeOpacity: 0.0,
    strokeColor: '#b9b9b9',
    fillOpacity: 1.0,
    fillColor: '#b9b9b9'
  }
}

function iconActive () {
  return {
    url: activeDestinationMarker,
    labelOrigin: new google.maps.Point(18, 75),
  }
}

function labelActive (text) {
  return {
    color: '#193d66',
    fontFamily: 'ApexNew',
    text: text
  }
}

function config(key, _default) {

    if (_.get(window, 'larabelt.coords.' + key)) {
        return _.get(window, 'larabelt.coords.' + key);
    }

    if (_default) {
        return _default;
    }

    return null;
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.coordinates {
  height: 500px;
}
</style>