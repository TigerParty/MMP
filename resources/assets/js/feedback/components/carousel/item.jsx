import React from 'react'


class CarouselItem extends React.Component {
    constructor(props) {
        super(props)
    }

    carouselItemClass() {
        const { isAcitve } = this.props
        return isAcitve ? 'carousel-item active' : 'carousel-item'
    }

    checkDescription() {
        const { description } = this.props
        if(description) {
            return(
              <div className="description
                mt-4
                font-size-14
                text-greyish-brown">
                <div className="font-size-15
                  font-weight-bold
                  title
                  mb-2">
                  { description? description.header:'' }</div>
                <span className="opacity-0-5"> { description? description.content:'' } </span>
              </div>
            )
        }
    }

    render() {
        const { attachmentId, description, path } = this.props
        return (
          <div className={ this.carouselItemClass() }>
            <div className="img-box" style={{ backgroundImage: `url(${path})`}}>
            </div>
            { this.checkDescription() }

          </div>
        )
    }

}

export default CarouselItem
