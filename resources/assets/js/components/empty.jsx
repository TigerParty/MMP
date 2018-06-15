import React from 'react'

class Empty extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <div className="row
              font-size-30
              font-weight-bold
              data-list
              text-gray
              opacity-0-5
              justify-content-center
              text-center
              py-4">
              <div className="col-12 col-md-auto">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="#c3c5c7">
                  <path d="M0 0h24v24H0z" fill="none"/>
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
              </svg>
              </div>
              <div className="col-12 col-md-auto">
                  No Data
              </div>
            </div>

        )
    }

}

export default Empty
