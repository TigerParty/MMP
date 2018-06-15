import React from 'react'
import NumberItem from './number_item'


class NumberInfo extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { numerator,
          numeratorTitle,
          numeratorDisplayNumber,
          denominator,
          denominatorDisplayNumber,
          denominatorTitle } = this.props
        return (
          <div className="d-flex
            flex-column
            h-100
            text-greyish-brown
            absolute-center-box
            number-info">
            <NumberItem
              number={ numeratorDisplayNumber > -1 ? numeratorDisplayNumber:numerator }
              title={ numeratorTitle }
              isTop={ true }/>
            <div className="center-item vertical-center w-100 divider" />
            <NumberItem
              number={ denominatorDisplayNumber > -1 ? denominatorDisplayNumber:denominator }
              title={ denominatorTitle }
              isTop={ false }/>
          </div>
        )
    }

}

export default NumberInfo
