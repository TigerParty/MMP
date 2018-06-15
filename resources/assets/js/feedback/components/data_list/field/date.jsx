import React from 'react'


class DateField extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data} = this.props
        return (
          <div className="col-4
            col-xl-2
            order-xl-3
            pr-lg-0
            mr-auto
            opacity-0-4
            font-size-10
            font-size-md-14
            text-right
            text-truncate
            date">
              { data }
          </div>
        )
    }

}

export default DateField
