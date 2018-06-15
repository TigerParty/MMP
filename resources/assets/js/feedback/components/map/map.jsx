import React from 'react'
import { withScriptjs, withGoogleMap, GoogleMap, Marker } from 'react-google-maps'
import { MAP_DEFAULT_ZOOM } from '../../constants'
import { compose, withProps } from 'recompose'


const Map = compose(
    withProps({
        googleMapURL: `https://maps.googleapis.com/maps/api/js?key=${lang.feedback.map_key}`,
        loadingElement: <div style={{ height: `100%` }} />,
        containerElement: <div style={{ height: `250px` }} />,
        mapElement: <div style={{ height: `100%` }} />,
    }),
    withScriptjs,
    withGoogleMap
)((props) =>
  <div className="row mb-3">
    <div className="col-12">
      <GoogleMap
        defaultZoom={ MAP_DEFAULT_ZOOM }
        defaultCenter={{ lat: props.lat, lng: props.lng }}>
        <Marker position={{ lat: props.lat, lng: props.lng  }} icon={{ url: "/images/icon/map-marker.svg" ,scaledSize: new google.maps.Size(50, 50)}} />
      </GoogleMap>
    </div>
  </div>
)



export default Map
