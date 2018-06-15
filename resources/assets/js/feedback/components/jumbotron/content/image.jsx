import React from 'react'


class ImageConent extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { title, imgPath} = this.props
        return (
          <div className={`row description without-mask`}>
            <div className="col-12
              text-greyish-brown
              font-size-15
              font-size-i7-17
              font-size-sm-19
              font-size-md-21
              font-size-lg-28
              font-weight-bold
              letter-spacing-0-2">
              { title }
            </div>
            <div className="col-6
              col-lg-4
              pt-1
              pt-lg-2
              content">
              <img className="img-fluid" src={imgPath}/>
            </div>
          </div>
        )
    }

}

export default ImageConent
