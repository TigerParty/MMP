import React from 'react'

class NumberItem extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { isTop,
            isMobile,
            number,
            title } = this.props
        const positionTop = isTop && !isMobile ? 'mb-auto' : ''
        const topItem = isTop && !isMobile? 'mb-3' : ''
        const bottomItem = !isTop && !isMobile? 'mt-4 mb-3' : ''
        const typeFontWeight = isMobile? 'opacity-0-6 font-weight-normal': 'opacity-0-5 font-weight-bold'
        return (
          <div className={positionTop}>
            <div className={`font-size-24
              font-size-lg-60
              font-weight-light
              number
              ${topItem}
              ${bottomItem}`} >
              { number }
            </div>
            <div className={`text-uppercase
              font-size-10
              font-size-lg-12
              letter-spacing-0-1
              ${typeFontWeight}`}>
              { title }
            </div>
          </div>
        )
    }

}

export default NumberItem
