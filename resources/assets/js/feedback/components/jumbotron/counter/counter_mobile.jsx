import React from 'react'
import NumberItem from './number_info/number_item'


class CounterMobile extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data } = this.props
        return (
          <div className="row
            py-2
            bg-primary
            text-white
            text-center
            justify-content-center">
            <div className="col-12">
              <div className="row align-items-center">
                {
                  data.map((item, index) => {
                      return (
                          <div className="col px-1" key={ index }>
                            <NumberItem
                              number={item.hasOwnProperty('numerator') ? item.numerator : (item.denominatorDisplayNumber||item.denominator)}
                              title={ item.hasOwnProperty('numeratorTitle')? item.numeratorTitle:item.denominatorTitle }
                              isMobile={ true }/>
                          </div>
                      )
                  })
                }
              </div>
            </div>
          </div>
        )
    }

}

export default CounterMobile
