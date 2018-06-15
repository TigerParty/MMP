import React from 'react'

class ChartBar extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { yellowBg, height } = this.props
        const chartBg = yellowBg? 'yellow-bg': ''
        const chartBarColor = yellowBg? 'bg-yellow-tan': 'bg-primary'
        return (
          <div className={`h-100
            position-relative
            mr-4
            chart
            ${chartBg}`}>
            <div className={`position-absolute
              w-100
              chart-bar
              ${chartBarColor}`}
              style={{height: height + '%'}}>
              </div>
          </div>
        )
    }

}

export default ChartBar
