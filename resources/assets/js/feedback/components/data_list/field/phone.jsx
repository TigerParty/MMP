import React from 'react'


class PhoneField extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data} = this.props
        return (
          <div className="col-8
            col-xl-2
            order-xl-1
            pl-lg-0
            font-size-14
            font-size-md-17
            text-truncate
            phone">
              { data }
          </div>
        )
    }

}

export default PhoneField
