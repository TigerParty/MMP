import React from 'react'


class TitleField extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { data, isRead } = this.props
        return (
          <div className="col-8
            col-xl-10
            order-xl-1
            pl-lg-0
            font-size-14
            font-size-md-17
            text-truncate
            phone">
            <div className="row">
              <div className="col-9 text-truncate">{ data }</div>
              { !isRead &&
               <div className="col-3">
                 <span className="bg-primary text-white p-2 rounded">NEW</span>
               </div> }
            </div>
          </div>
        )
    }

}

export default TitleField
