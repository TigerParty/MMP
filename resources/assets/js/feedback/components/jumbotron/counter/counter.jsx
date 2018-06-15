import React from 'react'
import ChartBar from './chart_bar'
import NumberInfo from './number_info/number_info'


class Counter extends React.Component {
    constructor(props) {
        super(props)
    }

    calculateHeight(numerator, denominator) {
        const intNenominator = parseInt(numerator)
        const intDenominator = parseInt(denominator)

        if(denominator < 1) {
            return 0
        }
        return intNenominator/intDenominator*100

    }

    render() {
        const { data  } = this.props

        return (
          <div className="row h-100 counter justify-content-xl-end">
              {
                data.map((item, index) => {
                    return (
                        <div className="col-auto mt-3" key={`counterItem-${index}`}>
                          <div className="d-flex h-100">
                            <ChartBar  height={ this.calculateHeight(item.numerator, item.denominator) }  yellowBg={ item.yellowBg } />
                            <NumberInfo  { ...item } />
                          </div>
                        </div>
                    )
                })
              }
          </div>
        )
    }

}

export default Counter
