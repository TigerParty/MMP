import React from 'react'

class ContentField extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data, isRead } = this.props
        return (
          <div className="col-12
            px-lg-0
            col-xl-8
            order-xl-2
            font-size-12
            font-size-md-15
            text-truncate
            mt-1
            content">
            <div className="row">
              <div className="col-10 text-truncate">{data}</div>
              { !isRead &&
                <div className="col-auto ml-auto pl-0">
                  <span className="bg-primary text-white p-1 rounded">NEW</span>
                </div> }
            </div>
          </div>
        )
    }

}

export default ContentField
