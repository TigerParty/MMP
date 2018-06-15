import React from 'react'


class CarouselControl extends React.Component {
    constructor(props) {
        super(props)
    }

    controlClass() {
        const { isPrev, isNext } = this.props
        if(isPrev) {
            return 'carousel-control-prev'
        }

        if(isNext) {
            return 'carousel-control-next'
        }
    }

    controlIcon() {
        const { isPrev, isNext } = this.props
        if(isPrev) {
            return (<img src='/images/icon/slider-left.svg' />)
        }

        if(isNext) {
            return (<img src='/images/icon/slider-right.svg' />)
        }
    }

    controlType() {
        const { isPrev, isNext } = this.props
        if(isPrev) {
            return 'prev'
        }

        if(isNext) {
            return 'next'
        }
    }

    render() {
        const { isPrev, isNext, targetId } = this.props

        return (
          <a className={ this.controlClass() } href={`#${targetId}`} role="button" data-slide={ this.controlType()} >
            { this.controlIcon() }
          </a>
        )
    }

}

export default CarouselControl
