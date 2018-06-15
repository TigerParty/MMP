import React from 'react'


class CarouselIndicator extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { total, targetId } = this.props

        return (
          <ol className="carousel-indicators">
              {[...Array(total)].map((item, index) =>
                <li data-target={`#${targetId}`} key={ index } data-slide-to={ index }  className={ index == 0 ? 'active' : ''} ></li>
              )}
          </ol>
        )
    }

}

export default CarouselIndicator
