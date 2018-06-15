import React from 'react'

class Avatar extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
          <div className="rounded-circle
              bg-grey-blue
              p-4
              absolute-center-box">
              <span className="
                  center-item
                  center
                  text-secondary
                  font-size-22
                  opacity-0-5
                  font-weight-bold-extreme
                  text-uppercase">
                  {lang.site.shorthead_title.length > 3 ? lang.site.shorthead_title.charAt(0):lang.site.shorthead_title}
              </span>
          </div>
        )
    }
}



export default Avatar
